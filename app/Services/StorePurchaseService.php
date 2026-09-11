<?php

namespace App\Services;

use App\Mail\StorePurchaseReceiptMail;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Expense;
use App\Models\StoreCheckoutSession;
use App\Models\StoreCoupon;
use App\Models\StoreProduct;
use App\Models\StorePurchase;
use App\Models\StoreReview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StorePurchaseService
{
    public function __construct(private StoreLicenseService $licenses, private StoreCatalogService $catalog) {}

    public function completePurchase(StoreProduct $product, float $amountPaid, string $paymentMethod = 'simulated', ?StoreCoupon $coupon = null, float $discount = 0, ?int $userId = null, ?string $stripeSessionId = null): StorePurchase
    {
        $userId = $userId ?? Auth::id();
        abort_unless($userId, 401);

        return DB::transaction(function () use ($product, $amountPaid, $paymentMethod, $coupon, $discount, $userId, $stripeSessionId) {
            $existing = StorePurchase::query()->where('user_id', $userId)->where('product_id', $product->id)->where('payment_status', 'completed')->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            $purchase = StorePurchase::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'amount_paid' => max(0, $amountPaid),
                'payment_status' => 'completed',
                'payment_method' => $paymentMethod,
                'coupon_code' => $coupon?->code,
                'discount_amount' => max(0, $discount),
                'stripe_session_id' => $stripeSessionId,
            ]);

            $this->licenses->issue($purchase);
            $product->increment('sales_count');

            if ($product->points_reward > 0) {
                $user = User::find($userId);
                if ($user) {
                    DB::table('users')->where('id', $user->id)->increment('points', $product->points_reward);
                    $user->addXp($product->points_reward * 10);
                }
            }
            if ($coupon) {
                $coupon->increment('used_count');
            }

            $this->logActivity('store_purchase', "Comprou: {$product->title}", ['product_id' => $product->id, 'amount' => $amountPaid], $userId);
            $this->catalog->clearCache();
            if ($user = User::find($userId)) {
                app(StoreEntitlementService::class)->clearCache($user);
            }

            return $purchase;
        });
    }

    public function updateProductRating(int $productId): void
    {
        $stats = StoreReview::where('product_id', $productId)->where('is_approved', true)->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')->first();
        StoreProduct::where('id', $productId)->update(['rating_avg' => round($stats->avg_rating ?? 0, 2), 'rating_count' => $stats->total ?? 0]);
        $this->catalog->clearCache();
    }

    public function recordEducationExpense(StoreProduct $product, float $amount, ?int $userId = null): ?Expense
    {
        $userId = $userId ?? Auth::id();
        $user = User::find($userId);
        if (! $user?->current_workspace_id || $amount <= 0) {
            return null;
        }
        $workspaceId = $user->current_workspace_id;
        $category = Category::where('workspace_id', $workspaceId)->where(fn ($q) => $q->where('slug', 'educacao')->orWhere('name', 'Educação'))->first();
        if (! $category) {
            $category = Category::create(['user_id' => $userId, 'workspace_id' => $workspaceId, 'name' => 'Educação', 'slug' => Category::uniqueSlugFor('Educação', $workspaceId), 'icon' => 'academic-cap', 'color' => '#06b6d4', 'is_fixed' => true]);
        }

        return Expense::create(['user_id' => $userId, 'workspace_id' => $workspaceId, 'category_id' => $category->id, 'amount' => round($amount, 2), 'description' => $product->title, 'subcategory' => 'Formação', 'spent_at' => now(), 'metadata' => ['source' => 'store_checkout', 'product_id' => $product->id, 'store_product' => $product->title]]);
    }

    public function logActivity(string $action, string $description, array $metadata = [], ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();
        if (! $userId) {
            return;
        }
        $user = User::find($userId);
        if (! $user) {
            return;
        }
        ActivityLog::create(['workspace_id' => $user->current_workspace_id, 'user_id' => $userId, 'action' => $action, 'description' => $description, 'model_type' => 'store', 'model_id' => $metadata['product_id'] ?? 0, 'metadata' => $metadata]);
    }

    public function completeStoreCheckout(StoreCheckoutSession $pending, string $stripeSessionId): void
    {
        $purchases = DB::transaction(function () use ($pending, $stripeSessionId) {
            $locked = StoreCheckoutSession::where('id', $pending->id)->lockForUpdate()->first();

            if (! $locked || $locked->status !== 'pending') {
                return collect();
            }

            if (! hash_equals((string) $locked->stripe_session_id, $stripeSessionId)) {
                throw new \RuntimeException('A sessão Stripe não corresponde ao checkout pendente.');
            }

            $coupon = $locked->coupon_code ? StoreCoupon::where('code', $locked->coupon_code)->first() : null;
            $purchases = collect();

            foreach ($locked->items as $index => $item) {
                $product = StoreProduct::query()->where('is_active', true)->find($item['product_id']);
                if (! $product) {
                    throw new \RuntimeException('Um produto do checkout deixou de estar disponível.');
                }

                $amountPaid = max(0, (float) ($item['amount_paid'] ?? 0));
                $purchase = $this->completePurchase(
                    $product,
                    $amountPaid,
                    'stripe',
                    $index === 0 ? $coupon : null,
                    0,
                    $locked->user_id,
                    $stripeSessionId
                );
                $purchases->push($purchase);

                if ($locked->add_expense_to_education && $purchase->wasRecentlyCreated) {
                    $this->recordEducationExpense($product, $amountPaid, $locked->user_id);
                }
            }

            if ($purchases->isEmpty() && ! empty($locked->items)) {
                throw new \RuntimeException('O checkout não contém produtos válidos.');
            }

            $locked->update(['status' => 'completed', 'stripe_session_id' => $stripeSessionId]);
            return $purchases->unique('id')->values();
        });

        if ($purchases->isEmpty()) {
            return;
        }

        $user = User::find($pending->user_id);
        if ($user) {
            try {
                Mail::to($user->email)->send(new StorePurchaseReceiptMail($user, $purchases, $stripeSessionId));
            } catch (\Exception $e) {
                Log::error("Falha ao enviar recibo da loja para utilizador {$user->id}: ".$e->getMessage());
            }
        }
    }
}
