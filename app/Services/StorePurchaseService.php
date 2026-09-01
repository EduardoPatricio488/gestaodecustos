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
use Illuminate\Support\Facades\Mail;

class StorePurchaseService
{
    public function __construct(
        private StoreLicenseService $licenses,
        private StoreCatalogService $catalog,
    ) {}

    public function completePurchase(
        StoreProduct $product,
        float $amountPaid,
        string $paymentMethod = 'simulated',
        ?StoreCoupon $coupon = null,
        float $discount = 0,
        ?int $userId = null,
        ?string $stripeSessionId = null,
    ): StorePurchase {
        $userId = $userId ?? Auth::id();

        return DB::transaction(function () use ($product, $amountPaid, $paymentMethod, $coupon, $discount, $userId, $stripeSessionId) {
            $purchase = StorePurchase::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'amount_paid' => $amountPaid,
                'payment_status' => 'completed',
                'payment_method' => $paymentMethod,
                'coupon_code' => $coupon?->code,
                'discount_amount' => $discount,
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

            $this->logActivity('store_purchase', "Comprou: {$product->title}", [
                'product_id' => $product->id,
                'amount' => $amountPaid,
            ]);

            $this->catalog->clearCache();
            app(StoreEntitlementService::class)->clearCache(User::find($userId) ?? Auth::user());

            return $purchase;
        });
    }

    public function updateProductRating(int $productId): void
    {
        $stats = StoreReview::where('product_id', $productId)
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        StoreProduct::where('id', $productId)->update([
            'rating_avg' => round($stats->avg_rating ?? 0, 2),
            'rating_count' => $stats->total ?? 0,
        ]);

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

        $category = Category::where('workspace_id', $workspaceId)
            ->where(function ($query) {
                $query->where('slug', 'educacao')
                    ->orWhere('name', 'Educação');
            })
            ->first();

        if (! $category) {
            $category = Category::create([
                'user_id' => $userId,
                'workspace_id' => $workspaceId,
                'name' => 'Educação',
                'slug' => Category::uniqueSlugFor('Educação', $workspaceId),
                'icon' => 'academic-cap',
                'color' => '#06b6d4',
                'is_fixed' => true,
            ]);
        }

        return Expense::create([
            'user_id' => $userId,
            'workspace_id' => $workspaceId,
            'category_id' => $category->id,
            'amount' => round($amount, 2),
            'description' => $product->title,
            'subcategory' => 'Formação',
            'spent_at' => now(),
            'metadata' => [
                'source' => 'store_checkout',
                'product_id' => $product->id,
                'store_product' => $product->title,
            ],
        ]);
    }

    public function logActivity(string $action, string $description, array $metadata = [], ?int $userId = null): void
    {
        $userId = $userId ?? Auth::id();

        if (! $userId) {
            return;
        }

        ActivityLog::create([
            'workspace_id' => auth()->user()->current_workspace_id,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'model_type' => 'store',
            'model_id' => $metadata['product_id'] ?? 0, // 🔥 FIX: Agora enviamos o ID do produto
            'metadata' => $metadata,
        ]);
    }

    /**
     * Completa uma compra da loja paga via Stripe Checkout, criando as StorePurchase(s)
     * a partir da sessão pendente e enviando o recibo por e-mail. Idempotente: se a
     * sessão já estiver marcada como concluída, não faz nada.
     */
    public function completeStoreCheckout(StoreCheckoutSession $pending, string $stripeSessionId): void
    {
        $purchases = DB::transaction(function () use ($pending, $stripeSessionId) {
            $locked = StoreCheckoutSession::where('id', $pending->id)->lockForUpdate()->first();

            if (! $locked || $locked->status === 'completed') {
                return collect();
            }

            $coupon = $locked->coupon_code ? StoreCoupon::where('code', $locked->coupon_code)->first() : null;
            $purchases = collect();

            foreach ($locked->items as $index => $item) {
                $product = StoreProduct::find($item['product_id']);

                if (! $product) {
                    continue;
                }

                $purchases->push($this->completePurchase(
                    $product,
                    (float) $item['amount_paid'],
                    'stripe',
                    $index === 0 ? $coupon : null,
                    0,
                    $locked->user_id,
                    $stripeSessionId,
                ));

                if ($locked->add_expense_to_education) {
                    $this->recordEducationExpense($product, (float) $item['amount_paid'], $locked->user_id);
                }
            }

            $locked->update(['status' => 'completed', 'stripe_session_id' => $stripeSessionId]);

            return $purchases;
        });

        if ($purchases->isEmpty()) {
            return;
        }

        $user = User::find($pending->user_id);

        if ($user) {
            try {
                Mail::to($user->email)->send(new StorePurchaseReceiptMail($user, $purchases, $stripeSessionId));
            } catch (\Exception $e) {
                // Falha de envio de email não pode reverter/derrubar a compra já confirmada
                \Illuminate\Support\Facades\Log::error("Falha ao enviar recibo da loja para utilizador {$user->id}: ".$e->getMessage());
            }
        }
    }
}
