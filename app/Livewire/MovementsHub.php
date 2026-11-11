<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Category;
use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\BankTransfer;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MovementsHub extends Component
{
    public string $type = 'all';
    public string $search = '';
    public string $period = 'all';
    public string $editingType = '';
    public string $conversionType = '';
    public bool $showEditModal = false;
    public bool $showConversionModal = false;
    public string $editingFromAccountId = '';
    public string $editingToAccountId = '';
    public ?int $editingId = null;
    public string $editingTitle = '';
    public string $editingDescription = '';
    public string $editingAmount = '';
    public string $editingDate = '';
    public string $editingCategoryId = '';
    public string $editingAccountId = '';
    public string $editingCurrency = 'EUR';
    public string $editingNotes = '';
    public string $editingSource = '';
    public string $editingIncomeType = '';
    public string $editingFrequency = '';
    public string $editingDirection = 'out';
    public string $editingRecipient = '';
    public string $editingTransferMethod = '';
    public string $editingTransferCategory = '';
    public string $editingStatus = 'completed';
    public string $editingCycle = 'monthly';
    public string $editingPaymentSource = 'company';
    public bool $editingCompanyPaid = false;
    public bool $editingActive = true;
    public string $editingAmountPaid = '0';
    public string $editingOutstandingAmount = '0';
    public string $editingVatAmount = '0';
    public string $editingMetadata = '';
    public array $conversionFieldValues = [];
    public string $editingIncomeTaxEstimate = '';
    public string $editingIncomeDestination = 'cash';
    public string $editingSubscriptionBillingDay = '';
    public string $editingSubscriptionPaymentMethod = 'card';
    public string $editingSubscriptionStatus = 'active';
    public string $editingSubscriptionStartedAt = '';
    public string $editingSubscriptionRenewalDate = '';
    public bool $editingNotifyBeforeBilling = false;
    public string $editingNotifyDaysBefore = '3';
    public string $editingTransferStatus = 'completed';

    private function scopedRecordQuery(string $type, int $id)
    {
        $user = auth()->user();
        $model = match ($type) {
            'expense' => Expense::query(),
            'income' => Income::query(),
            'transfer' => BankTransfer::query(),
            'subscription' => Subscription::query(),
            'subscription_payment' => SubscriptionPayment::query(),
            default => abort(404),
        };
        $model->where('workspace_id', $user->current_workspace_id);
        if ($user->currentWorkspace?->type === 'personal') {
            $model->where('user_id', $user->id);
        }
        return $model->whereKey($id);
    }

    private function normalizeTransferCategory(mixed $category): string
    {
        if (is_array($category)) {
            return trim((string) ($category['name'] ?? ''));
        }

        if (is_object($category)) {
            return trim((string) ($category->name ?? ''));
        }

        $value = trim((string) $category);
        if ($value === '') {
            return '';
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return trim((string) ($decoded['name'] ?? ''));
        }

        return $value;
    }

    public function editMovement(string $type, int $id): void
    {
        $record = $this->scopedRecordQuery($type, $id)->firstOrFail();
        $this->resetValidation();
        $this->editingType = $type;
        $this->conversionType = $type;
        $this->showEditModal = true;
        $this->showConversionModal = false;
        $this->editingFromAccountId = (string) ($record->from_account_id ?? '');
        $this->editingToAccountId = (string) ($record->to_account_id ?? '');
        $this->editingId = $id;
        $this->editingTitle = (string) ($record->title ?? $record->name ?? $record->description ?? '');
        $this->editingDescription = (string) ($record->description ?? '');
        $this->editingAmount = (string) ($record->amount ?? '0');
        $this->editingDate = match ($type) {
            'expense' => $record->spent_at?->format('Y-m-d'),
            'income' => $record->received_at?->format('Y-m-d'),
            'transfer' => $record->transferred_at?->format('Y-m-d'),
            'subscription' => $record->renewal_date?->format('Y-m-d') ?? $record->started_at?->format('Y-m-d'),
            'subscription_payment' => $record->paid_at?->format('Y-m-d') ?? $record->billing_date?->format('Y-m-d'),
        } ?? now()->format('Y-m-d');
        $this->editingCategoryId = (string) ($record->category_id ?? '');
        $this->editingAccountId = (string) ($record->bank_account_id ?? '');
        $this->editingCurrency = strtoupper((string) ($record->currency ?? auth()->user()->currentWorkspace?->currency ?? 'EUR'));
        $this->editingNotes = (string) ($record->notes ?? '');
        $this->editingSource = (string) ($record->source ?? '');
        $this->editingIncomeType = (string) ($record->type ?? '');
        $this->editingFrequency = (string) ($record->frequency ?? '');
        $this->editingDirection = (string) ($record->direction ?? 'out');
        $this->editingRecipient = (string) ($record->recipient ?? '');
        $this->editingTransferMethod = (string) ($record->transfer_method ?? '');
        $this->editingTransferCategory = $this->normalizeTransferCategory($record->category ?? '');
        $this->editingStatus = (string) ($record->status ?? 'completed');
        $this->editingCycle = match ((string) ($record->cycle ?? 'monthly')) { 'yearly' => 'annual', 'weekly' => 'monthly', default => (string) ($record->cycle ?? 'monthly') };
        $this->editingPaymentSource = (string) ($record->payment_source ?? 'company');
        $this->editingCompanyPaid = (bool) ($record->paid_by_company ?? $record->is_company ?? false);
        $this->editingActive = (bool) ($record->is_active ?? true);
        $paidAmount = (float) ($record->amount_paid ?? 0) > 0 ? (float) $record->amount_paid : (float) $record->amount;
        $this->editingAmountPaid = (string) $paidAmount;
        $this->editingOutstandingAmount = (string) max(0, round((float) $record->amount - $paidAmount, 2));
        $this->editingVatAmount = (string) ($record->vat_amount ?? '0');
        $this->editingMetadata = $this->formatMetadataForDisplay($record->metadata) ?? '';
        $this->editingIncomeTaxEstimate = (string) ($record->tax_estimate ?? '');
        $this->editingIncomeDestination = $record->bank_account_id ? 'bank:'.$record->bank_account_id : 'cash';
        $this->editingSubscriptionBillingDay = (string) ($record->billing_day ?? ($this->editingDate ? Carbon::parse($this->editingDate)->day : now()->day));
        $this->editingSubscriptionPaymentMethod = (string) ($record->payment_method ?? 'card');
        $this->editingSubscriptionStatus = (string) ($record->status ?? 'active');
        $this->editingSubscriptionStartedAt = $record->started_at?->format('Y-m-d') ?? $this->editingDate;
        $this->editingSubscriptionRenewalDate = $record->renewal_date?->format('Y-m-d') ?? $this->editingDate;
        $this->editingNotifyBeforeBilling = (bool) ($record->notify_before_billing ?? false);
        $this->editingNotifyDaysBefore = (string) ($record->notify_days_before ?? 3);
        $this->editingTransferStatus = (string) ($record->status ?? 'completed');
        $this->conversionFieldValues = is_array($record->metadata ?? null) ? $record->metadata : [];
    }

    public function openConversionModal(string $type, int $id): void
    {
        abort_unless(in_array($type, ['expense', 'income', 'transfer', 'subscription'], true), 404);
        $this->editMovement($type, $id);
        if (trim($this->editingDescription) === '') {
            $this->editingDescription = $this->editingTitle;
        }
        $this->showEditModal = false;
        $this->showConversionModal = true;
        $this->conversionType = '';
        $this->resetValidation();
    }

    public function updatedConversionType(string $type): void
    {
        if (! in_array($type, ['expense', 'income', 'transfer', 'subscription'], true)) {
            return;
        }

        $this->resetValidation();

        if ($type === 'expense' && $this->editingType === 'transfer') {
            $this->editingAccountId = $this->editingFromAccountId;
        }

        if ($type === 'income' && $this->editingType === 'transfer') {
            $this->editingAccountId = $this->editingToAccountId;
            $this->editingIncomeDestination = $this->editingToAccountId !== ''
                ? 'bank:'.$this->editingToAccountId
                : 'cash';
        }

        if ($type === 'transfer') {
            $this->editingTransferStatus = $this->editingType === 'transfer'
                ? $this->editingTransferStatus
                : 'completed';

            if ($this->editingType === 'income' && $this->editingAccountId !== '') {
                $this->editingToAccountId = $this->editingAccountId;
            } elseif ($this->editingType === 'expense' && $this->editingAccountId !== '') {
                $this->editingFromAccountId = $this->editingAccountId;
            }
        }

        if ($type === 'subscription' && $this->editingType !== 'subscription') {
            $this->editingCycle = 'monthly';
            $this->editingSubscriptionStatus = 'active';
            $this->editingSubscriptionPaymentMethod = 'card';
            $this->editingSubscriptionBillingDay = (string) Carbon::parse($this->editingDate)->day;
            $this->editingSubscriptionStartedAt = $this->editingDate;
            $this->editingSubscriptionRenewalDate = $this->editingDate;
            $this->editingNotifyBeforeBilling = false;
            $this->editingNotifyDaysBefore = '3';
        }

    }

    public function updatedEditingIncomeDestination(string $destination): void
    {
        if ($destination === 'cash') {
            $this->editingAccountId = '';
            return;
        }

        if (str_starts_with($destination, 'bank:')) {
            $this->editingAccountId = substr($destination, 5);
        }
    }

    public function updatedEditingCategoryId(): void
    {
        if ($this->conversionType !== 'expense' || $this->editingCategoryId === '') {
            return;
        }

        $user = auth()->user();
        $category = Category::where('workspace_id', $user->current_workspace_id)
            ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->with('fields')
            ->find((int) $this->editingCategoryId);

        if (! $category) {
            $this->conversionFieldValues = [];
            return;
        }

        $allowedKeys = $category->fields->pluck('key')->all();
        $this->conversionFieldValues = array_intersect_key(
            $this->conversionFieldValues,
            array_flip($allowedKeys)
        );
    }

    private function conversionCategoryQuery()
    {
        $user = auth()->user();

        return Category::where('workspace_id', $user->current_workspace_id)
            ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
            ->where(function ($query) {
                $query->where('hidden_from_sidebar', false)
                    ->orWhereRaw('LOWER(TRIM(name)) = ?', ['outros']);
            });
    }

    public function closeConversionModal(): void
    {
        $this->showConversionModal = false;
        $this->cancelEditMovement();
    }

    public function cancelEditMovement(): void
    {
        $this->showEditModal = false;
        $this->showConversionModal = false;
        $this->reset([
            'editingType', 'editingId', 'editingTitle', 'editingDescription', 'editingAmount',
            'conversionType', 'editingFromAccountId', 'editingToAccountId',
            'editingDate', 'editingCategoryId', 'editingAccountId', 'editingNotes',
            'editingSource', 'editingIncomeType', 'editingFrequency', 'editingRecipient',
            'editingTransferMethod', 'editingTransferCategory',
            'conversionFieldValues', 'editingIncomeTaxEstimate', 'editingIncomeDestination',
            'editingSubscriptionBillingDay', 'editingSubscriptionPaymentMethod', 'editingSubscriptionStatus',
            'editingSubscriptionStartedAt', 'editingSubscriptionRenewalDate', 'editingNotifyDaysBefore',
        ]);
        $this->editingCurrency = strtoupper((string) (auth()->user()->currentWorkspace?->currency ?? 'EUR'));
        $this->editingDirection = 'out';
        $this->editingStatus = 'completed';
        $this->editingCycle = 'monthly';
        $this->editingPaymentSource = 'company';
        $this->editingCompanyPaid = false;
        $this->editingActive = true;
        $this->editingAmountPaid = '0';
        $this->editingVatAmount = '0';
        $this->editingOutstandingAmount = '0';
        $this->editingMetadata = '';
        $this->editingNotifyBeforeBilling = false;
        $this->editingIncomeDestination = 'cash';
        $this->editingTransferStatus = 'completed';
    }

    public function saveMovement(): void
    {
        abort_unless($this->editingType !== '' && $this->editingId, 404);
        $rules = [
            'editingAmount' => 'required|numeric|min:0.01',
            'editingDate' => 'required|date',
            'editingCurrency' => 'required|string|size:3',
            'editingDescription' => 'nullable|string|max:255',
        ];
        if ($this->editingType === 'expense') {
            $rules['editingVatAmount'] = 'nullable|numeric|min:0|max:'.$this->editingAmount;
            $rules['editingOutstandingAmount'] = 'required|numeric|min:0|max:'.$this->editingAmount;
            $rules['editingStatus'] = 'required|string|max:50';
            $rules['editingMetadata'] = 'nullable|string|max:10000';
            $rules['editingCategoryId'] = 'nullable|integer';
        } elseif ($this->editingType === 'income') {
            $rules['editingCategoryId'] = 'nullable|integer';
            $rules['editingSource'] = 'nullable|string|max:100';
            $rules['editingIncomeType'] = 'nullable|string|max:100';
            $rules['editingFrequency'] = 'nullable|string|max:100';
        } elseif ($this->editingType === 'transfer') {
            $rules['editingDirection'] = 'required|in:in,out';
            $rules['editingRecipient'] = 'nullable|string|max:255';
            $rules['editingTransferMethod'] = 'nullable|string|max:100';
            $rules['editingTransferCategory'] = 'nullable|string|max:100';
            $rules['editingStatus'] = 'required|string|max:50';
        } elseif ($this->editingType === 'subscription_payment') {
            $rules['editingPaymentSource'] = 'required|string|max:50';
        } else {
            $rules['editingCategoryId'] = 'nullable|integer';
            $rules['editingTitle'] = 'required|string|max:255';
            $rules['editingCycle'] = 'required|string|max:50';
        }
        $this->validate($rules);


        $user = auth()->user();
        $workspaceId = (int) $user->current_workspace_id;
        $category = null;
        if (in_array($this->editingType, ['expense', 'income', 'subscription'], true) && $this->editingCategoryId !== '') {
            $category = Category::where('workspace_id', $workspaceId)
                ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->findOrFail((int) $this->editingCategoryId);
        }
        $account = null;
        if (in_array($this->editingType, ['expense', 'income'], true) && $this->editingAccountId !== '') {
            $account = BankAccount::where('workspace_id', $workspaceId)
                ->when($user->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', $user->id))
                ->findOrFail((int) $this->editingAccountId);
        }

        $metadata = $this->editingType === 'expense' ? $this->parseMetadataFromEditor($this->editingMetadata) : null;

        DB::transaction(function () use ($category, $account, $metadata): void {
            $record = $this->scopedRecordQuery($this->editingType, (int) $this->editingId)->lockForUpdate()->firstOrFail();
            $amount = round((float) $this->editingAmount, 2);
            if ($this->editingType === 'expense') {
                $record->update([
                    'title' => $this->editingTitle !== '' ? $this->editingTitle : null,
                    'description' => $this->editingDescription,
                    'amount' => $amount,
                    'amount_paid' => round($amount - (float) $this->editingOutstandingAmount, 2),
                    'vat_amount' => round((float) ($this->editingVatAmount ?: 0), 2),
                    'status' => 'completed',
                    'metadata' => $metadata,
                    'spent_at' => $this->editingDate,
                    'category_id' => $category?->id,
                    'bank_account_id' => $account?->id,
                    'currency' => strtoupper($this->editingCurrency),
                    'is_company' => $this->editingCompanyPaid,
                ]);
            } elseif ($this->editingType === 'income') {
                $record->update([
                    'description' => $this->editingDescription,
                    'amount' => $amount,
                    'received_at' => $this->editingDate,
                    'category_id' => $category?->id,
                    'bank_account_id' => $account?->id,
                    'currency' => strtoupper($this->editingCurrency),
                    'source' => $this->editingSource,
                    'type' => $this->editingIncomeType,
                    'frequency' => $this->editingFrequency,
                    'notes' => $this->editingNotes,
                ]);
            } elseif ($this->editingType === 'transfer') {
                $record->update([
                    'description' => $this->editingDescription,
                    'amount' => $amount,
                    'transferred_at' => $this->editingDate,
                    'direction' => $this->editingDirection,
                    'recipient' => $this->editingRecipient,
                    'transfer_method' => $this->editingTransferMethod,
                    'category' => $this->editingTransferCategory,
                    'status' => $this->editingStatus,
                    'currency' => strtoupper($this->editingCurrency),
                    'notes' => $this->editingNotes,
                ]);
            } elseif ($this->editingType === 'subscription_payment') {
                $record->update([
                    'amount' => $amount,
                    'paid_at' => $this->editingDate,
                    'payment_source' => $this->editingPaymentSource,
                ]);
            } else {
                $record->update([
                    'name' => $this->editingTitle,
                    'amount' => $amount,
                    'cycle' => $this->editingCycle,
                    'category_id' => $category?->id,
                    'renewal_date' => $this->editingDate,
                    'paid_by_company' => $this->editingCompanyPaid,
                    'is_active' => $this->editingActive,
                    'notes' => $this->editingNotes,
                ]);
            }
        });

        $this->cancelEditMovement();
        $this->dispatch('toast', text: 'Registo atualizado com sucesso.');
    }



    public function convertMovement(): void
    {
        abort_unless($this->editingType !== '' && $this->editingId, 404);

        $allowed = ['expense', 'income', 'transfer', 'subscription'];
        if (! in_array($this->editingType, $allowed, true) || ! in_array($this->conversionType, $allowed, true)) {
            $this->addError('conversionType', 'Seleciona um tipo de movimento válido.');
            return;
        }
        if ($this->conversionType === $this->editingType) {
            $this->addError('conversionType', 'O movimento já é deste tipo.');
            return;
        }

        $rules = [
            'editingAmount' => 'required|numeric|min:0.01',
            'editingDate' => 'required|date',
            'editingCurrency' => 'required|string|size:3',
        ];

        $category = null;
        if (in_array($this->conversionType, ['expense', 'subscription'], true)) {
            $rules['editingCategoryId'] = 'required|integer';
            if ($this->editingCategoryId !== '') {
                $category = $this->conversionCategoryQuery()
                    ->with('fields')
                    ->findOrFail((int) $this->editingCategoryId);
            }
        }

        if ($this->conversionType === 'expense') {
            $rules['editingDescription'] = 'nullable|string|max:255';
            foreach (($category?->fields ?? collect()) as $field) {
                $rule = match ($field->type) {
                    'number' => 'numeric',
                    'date' => 'date',
                    'checkbox' => 'boolean',
                    'select' => 'in:'.collect($field->options ?? [])->implode(','),
                    default => 'string|max:1000',
                };
                $rules['conversionFieldValues.'.$field->key] = $field->required ? $rule.'|required' : $rule.'|nullable';
            }
        } elseif ($this->conversionType === 'income') {
            $rules['editingDescription'] = 'required|string|max:255';
            $rules['editingIncomeTaxEstimate'] = 'nullable|numeric|min:0|max:100';
            $rules['editingNotes'] = 'nullable|string|max:500';
        } elseif ($this->conversionType === 'transfer') {
            $rules['editingFromAccountId'] = 'required|integer|different:editingToAccountId';
            $rules['editingToAccountId'] = 'required|integer';
            $rules['editingTransferStatus'] = 'required|in:completed,pending,failed';
            $rules['editingDescription'] = 'nullable|string|max:255';
            $rules['editingNotes'] = 'nullable|string|max:500';
        } else {
            $rules['editingTitle'] = 'required|string|max:255';
            $rules['editingSubscriptionBillingDay'] = 'required|integer|min:1|max:31';
            $rules['editingSubscriptionPaymentMethod'] = 'required|in:card,direct_debit,bank_transfer,paypal,cash';
            $rules['editingSubscriptionStatus'] = 'required|in:active,paused,cancelled';
            $rules['editingSubscriptionStartedAt'] = 'required|date';
            $rules['editingSubscriptionRenewalDate'] = 'required|date';
            $rules['editingCycle'] = 'required|in:monthly,quarterly,semiannual,annual';
            $rules['editingNotes'] = 'nullable|string|max:500';
            $rules['editingNotifyDaysBefore'] = 'nullable|integer|min:1|max:30';
        }

        $this->validate($rules);

        $user = auth()->user();
        $workspaceId = (int) $user->current_workspace_id;
        $personal = $user->currentWorkspace?->type === 'personal';

        $account = null;
        if (in_array($this->conversionType, ['expense', 'income'], true) && $this->editingAccountId !== '') {
            $account = BankAccount::where('workspace_id', $workspaceId)
                ->when($personal, fn ($q) => $q->where('user_id', $user->id))
                ->findOrFail((int) $this->editingAccountId);
        }

        $fromAccount = null;
        $toAccount = null;
        if ($this->conversionType === 'transfer') {
            $fromAccount = BankAccount::where('workspace_id', $workspaceId)
                ->when($personal, fn ($q) => $q->where('user_id', $user->id))
                ->findOrFail((int) $this->editingFromAccountId);
            $toAccount = BankAccount::where('workspace_id', $workspaceId)
                ->when($personal, fn ($q) => $q->where('user_id', $user->id))
                ->findOrFail((int) $this->editingToAccountId);
        }

        $metadata = [];
        if ($this->conversionType === 'expense' && $category) {
            foreach ($category->fields as $field) {
                $value = $this->conversionFieldValues[$field->key] ?? null;
                if ($value !== null && $value !== '') {
                    $metadata[$field->key] = $value;
                }
            }
        }

        DB::transaction(function () use ($category, $account, $fromAccount, $toAccount, $user, $workspaceId, $metadata): void {
            $old = $this->scopedRecordQuery($this->editingType, (int) $this->editingId)->lockForUpdate()->firstOrFail();
            $amount = round((float) $this->editingAmount, 2);
            $description = trim($this->editingDescription) !== '' ? trim($this->editingDescription) : trim($this->editingTitle);
            $common = [
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,
                'amount' => $amount,
                'currency' => strtoupper($this->editingCurrency),
            ];

            if ($this->conversionType === 'expense') {
                Expense::create($common + [
                    'description' => $description ?: null,
                    'category_id' => $category?->id,
                    'bank_account_id' => $account?->id,
                    'spent_at' => $this->editingDate,
                    'metadata' => $metadata,
                ]);
            } elseif ($this->conversionType === 'income') {
                Income::create($common + [
                    'description' => $description ?: 'Receita',
                    'category_id' => null,
                    'bank_account_id' => $account?->id,
                    'received_at' => $this->editingDate,
                    'tax_estimate' => $this->editingIncomeTaxEstimate !== '' ? (float) $this->editingIncomeTaxEstimate : null,
                    'type' => 'Extra',
                    'source' => 'emprego',
                    'frequency' => 'pontual',
                    'notes' => $this->editingNotes ?: null,
                ]);
            } elseif ($this->conversionType === 'transfer') {
                BankTransfer::create($common + [
                    'from_account_id' => $fromAccount->id,
                    'to_account_id' => $toAccount->id,
                    'description' => $description ?: 'Transferência',
                    'transferred_at' => $this->editingDate,
                    'status' => $this->editingTransferStatus,
                    'notes' => $this->editingNotes ?: null,
                ]);

                if ($this->editingTransferStatus === 'completed') {
                    BankAccount::where('workspace_id', $workspaceId)->whereKey($fromAccount->id)->decrement('balance', $amount);
                    BankAccount::where('workspace_id', $workspaceId)->whereKey($toAccount->id)->increment('balance', $amount);
                }
            } else {
                Subscription::create($common + [
                    'name' => $this->editingTitle ?: $description ?: 'Nova assinatura',
                    'category_id' => $category->id,
                    'cycle' => $this->editingCycle,
                    'billing_day' => (int) $this->editingSubscriptionBillingDay,
                    'started_at' => $this->editingSubscriptionStartedAt,
                    'renewal_date' => $this->editingSubscriptionRenewalDate,
                    'payment_method' => $this->editingSubscriptionPaymentMethod,
                    'status' => $this->editingSubscriptionStatus,
                    'is_active' => $this->editingSubscriptionStatus === 'active',
                    'paid_by_company' => $this->editingCompanyPaid,
                    'notes' => $this->editingNotes ?: null,
                    'notify_before_billing' => $this->editingNotifyBeforeBilling,
                    'notify_days_before' => $this->editingNotifyDaysBefore !== '' ? (int) $this->editingNotifyDaysBefore : null,
                ]);
            }

            if ($this->editingType === 'transfer' && $old->status === 'completed') {
                BankAccount::where('workspace_id', $workspaceId)->whereKey($old->from_account_id)->increment('balance', (float) $old->amount);
                BankAccount::where('workspace_id', $workspaceId)->whereKey($old->to_account_id)->decrement('balance', (float) $old->amount);
            }

            $old->delete();
        });

        $this->cancelEditMovement();
        $this->dispatch('toast', text: 'Movimento convertido com sucesso. O registo anterior foi substituído.');
    }

    private function parseMetadataFromEditor(string $text): ?array
    {
        $text = trim($text);

        if ($text === '') {
            return null;
        }

        // Mantém compatibilidade com registos colados no formato JSON antigo.
        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $keys = [
            'banco' => 'bank',
            'número da importação' => 'import_id',
            'numero da importacao' => 'import_id',
            'id da importação' => 'import_id',
            'id da importacao' => 'import_id',
            'tipo de ficheiro de origem' => 'source_file_type',
            'tipo de ficheiro' => 'source_file_type',
        ];

        $metadata = [];
        foreach (preg_split('/\\r\\n|\\r|\\n/', $text) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$label, $value] = array_map('trim', explode(':', $line, 2));
            if ($label === '' || $value === '') {
                continue;
            }

            $normalizedLabel = mb_strtolower($label);
            $key = $keys[$normalizedLabel] ?? str_replace(' ', '_', $normalizedLabel);

            if ($key === 'bank') {
                $value = mb_strtolower($value);
            } elseif ($key === 'source_file_type') {
                $value = mb_strtolower($value);
            } elseif ($key === 'import_id' && is_numeric($value)) {
                $value = (int) $value;
            }

            $metadata[$key] = $value;
        }

        return $metadata !== [] ? $metadata : null;
    }

    private function normalizeStatusForDisplay(?string $status): string
    {
        // Na área de movimentos, todos os registos são apresentados como completos.
        return 'Completo';
    }

    private function formatMetadataForDisplay(mixed $metadata): ?string
    {
        if (is_string($metadata)) {
            $decoded = json_decode($metadata, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $metadata = $decoded;
            } elseif (trim($metadata) !== '') {
                return trim($metadata);
            } else {
                return null;
            }
        }

        if (! is_array($metadata) || $metadata === []) {
            return null;
        }

        $labels = [
            'bank' => 'Banco',
            'import_id' => 'Número da importação',
            'source_file_type' => 'Tipo de ficheiro de origem',
        ];

        return collect($metadata)->map(function ($value, $key) use ($labels) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            $label = $labels[$key] ?? ucfirst(str_replace(['_', '-'], ' ', (string) $key));
            $displayValue = (string) $value;

            if ($key === 'bank') {
                $displayValue = mb_strtoupper($displayValue);
            } elseif ($key === 'source_file_type') {
                $displayValue = mb_strtoupper($displayValue);
            }

            return $label.': '.$displayValue;
        })->implode("\n");
    }

    public function clearFilters(): void
    {
        $this->type = 'all';
        $this->search = '';
        $this->period = 'all';
    }

    public function render()
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;
        $personal = $user->currentWorkspace?->type === 'personal';
        $scope = fn ($query) => $query->where('workspace_id', $workspaceId)
            ->when($personal, fn ($q) => $q->where('user_id', $user->id));

        $items = collect();

        if (in_array($this->type, ['all', 'expense'], true)) {
            $items = $items->concat($scope(Expense::query()->with(['category', 'supplier', 'bankAccount', 'project', 'task', 'costCenter']))->get()->map(fn ($item) => [
                'key' => 'expense-'.$item->id, 'record_id' => $item->id, 'edit_type' => 'expense', 'type' => 'expense', 'type_label' => 'Despesa',
                'record_url' => route('hub.category', $item->category?->resolveSlug() ?? 'outras').'?open_expense='.$item->id,
                'title' => $item->title ?: $item->description ?: 'Despesa',
                'detail' => $item->category?->name ?: 'Sem categoria',
                'date' => $item->spent_at, 'amount' => (float) ((float) $item->amount_paid > 0 ? $item->amount_paid : $item->amount),
                'direction' => 'out', 'status' => $this->normalizeStatusForDisplay($item->status ?: 'Completo'),
                'details' => [
                    'Descrição' => $item->description,
                    'Categoria' => $item->category?->name,
                    'Fornecedor' => $item->supplier?->name,
                    'Conta bancária' => $item->bankAccount?->name,
                    'Projeto' => $item->project?->name,
                    'Tarefa' => $item->task?->name,
                    'Centro de custo' => $item->costCenter?->name,
                    'Valor total' => number_format((float) $item->amount, 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'Valor pago' => number_format((float) ((float) $item->amount_paid > 0 ? $item->amount_paid : $item->amount), 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'Em falta' => number_format(max(0, (float) $item->amount - (float) ((float) $item->amount_paid > 0 ? $item->amount_paid : $item->amount)), 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'IVA' => $item->vat_amount !== null ? number_format((float) $item->vat_amount, 2, ',', '.').' '.($item->currency ?: 'EUR') : null,
                    'Data' => $item->spent_at?->format('d/m/Y'),
                    'Estado' => $this->normalizeStatusForDisplay($item->status ?: 'Completo'),
                    'Informações de importação' => $this->formatMetadataForDisplay($item->metadata),
                ],
            ]));
        }

        if (in_array($this->type, ['all', 'income'], true)) {
            $items = $items->concat($scope(Income::query()->with(['category', 'bankAccount']))
                ->where(fn ($q) => $q->whereNull('added_to_salary')->orWhere('added_to_salary', false))
                ->get()->map(fn ($item) => [
                    'key' => 'income-'.$item->id, 'record_id' => $item->id, 'edit_type' => 'income', 'type' => 'income', 'type_label' => 'Receita',
                    'record_url' => route('hub.incomes'),
                    'title' => $item->description ?: 'Receita',
                    'detail' => $item->category?->name ?: ($item->source ?: 'Receita'),
                    'date' => $item->received_at, 'amount' => (float) $item->amount,
                    'direction' => 'in', 'status' => 'Completo',
                    'details' => [
                        'Descrição' => $item->description,
                        'Categoria' => $item->category?->name,
                        'Origem' => $item->source,
                        'Tipo' => $item->type,
                        'Frequência' => $item->frequency,
                        'Conta bancária' => $item->bankAccount?->name,
                        'Valor' => number_format((float) $item->amount, 2, ',', '.').' '.($item->currency ?: 'EUR'),
                        'Data de recebimento' => $item->received_at?->format('d/m/Y'),
                        'Notas' => $item->notes,
                    ],
                ]));
        }

        if (in_array($this->type, ['all', 'transfer'], true)) {
            $items = $items->concat($scope(BankTransfer::query()->with(['fromAccount', 'toAccount']))->get()->map(fn ($item) => [
                'key' => 'transfer-'.$item->id, 'record_id' => $item->id, 'edit_type' => 'transfer', 'type' => 'transfer', 'type_label' => 'Transferência',
                'record_url' => route('hub.debts'),
                'title' => $item->description ?: ($item->recipient ?: 'Transferência bancária'),
                'detail' => ($item->fromAccount?->name ?: 'Origem externa').' → '.($item->toAccount?->name ?: 'Destino externo'),
                'date' => $item->transferred_at, 'amount' => (float) $item->amount,
                'direction' => ($item->direction ?? 'out') === 'in' ? 'in' : 'out',
                'status' => 'Completo',
                'details' => [
                    'Descrição' => $item->description,
                    'Destinatário' => $item->recipient,
                    'Conta de origem' => $item->fromAccount?->name,
                    'Conta de destino' => $item->toAccount?->name,
                    'Direção' => ($item->direction ?? 'out') === 'in' ? 'Entrada' : 'Saída',
                    'Método' => $item->transfer_method,
                    'Categoria' => $item->category,
                    'Valor' => number_format((float) $item->amount, 2, ',', '.').' '.($item->currency ?: 'EUR'),
                    'Data' => $item->transferred_at?->format('d/m/Y'),
                    'Estado' => $this->normalizeStatusForDisplay($item->status),
                    'Notas' => $item->notes,
                ],
            ]));
        }

        if (in_array($this->type, ['all', 'subscription'], true)) {
            $items = $items->concat($scope(Subscription::query())->get()->map(fn ($item) => [
                'key' => 'subscription-'.$item->id, 'record_id' => $item->id, 'edit_type' => 'subscription', 'type' => 'subscription', 'type_label' => 'Assinatura',
                'record_url' => route('hub.subscriptions'),
                'title' => $item->name,
                'detail' => 'Periodicidade: '.($item->cycle ?: 'Recorrente'),
                'date' => $item->renewal_date ?: $item->started_at,
                'amount' => (float) $item->amount,
                'direction' => $item->paid_by_company ? 'company' : 'out',
                'status' => 'Completo',
                'details' => [
                    'Nome' => $item->name,
                    'Valor por ciclo' => number_format((float) $item->amount, 2, ',', '.').' €',
                    'Periodicidade' => match (strtolower((string) $item->cycle)) { 'monthly' => 'Mensal', 'yearly' => 'Anual', 'weekly' => 'Semanal', 'quarterly' => 'Trimestral', default => ucfirst((string) $item->cycle) },
                    'Método de pagamento' => $item->paid_by_company ? 'Pago pela empresa' : $item->payment_method,
                    'Data de início' => $item->started_at?->format('d/m/Y'),
                    'Próxima renovação' => $item->renewal_date?->format('d/m/Y'),
                    'Estado' => $item->is_active ? 'Ativa' : 'Inativa',
                    'Notas' => $item->notes,
                ],
            ]));
            $items = $items->concat($scope(\App\Models\SubscriptionPayment::query()->with('subscription'))->get()->map(fn ($payment) => [
                'key' => 'subscription-payment-'.$payment->id,
                'record_id' => $payment->id,
                'edit_type' => 'subscription_payment',
                'record_url' => route('hub.subscriptions'),
                'type' => 'subscription',
                'type_label' => 'Mensalidade',
                'title' => $payment->subscription?->name ?: 'Assinatura',
                'detail' => 'Mensalidade paga pela empresa',
                'date' => $payment->paid_at,
                'amount' => (float) $payment->amount,
                'direction' => 'company',
                'status' => 'Completo',
                'details' => [
                    'Assinatura' => $payment->subscription?->name,
                    'Data da mensalidade' => $payment->billing_date?->format('d/m/Y'),
                    'Data de registo do pagamento' => $payment->paid_at?->format('d/m/Y'),
                    'Valor' => number_format((float) $payment->amount, 2, ',', '.').' €',
                    'Origem do pagamento' => $payment->payment_source === 'company' ? 'Empresa' : $payment->payment_source,
                    'Estado' => 'Pago pela empresa',
                ],
            ]));
        }

        $items = $items->filter(function ($item) {
            $date = $item['date'] ? Carbon::parse($item['date']) : null;

            if ($this->period === 'month' && (! $date || ! $date->isCurrentMonth())) return false;
            if ($this->period === 'year' && (! $date || ! $date->isCurrentYear())) return false;

            $needle = mb_strtolower(trim($this->search));

            return $needle === '' || str_contains(mb_strtolower($item['title'].' '.$item['detail'].' '.$item['type_label']), $needle);
        })->sortByDesc(fn ($item) => $item['date'] ? Carbon::parse($item['date'])->getTimestamp() : 0)->values();

        return view('livewire.movements-hub', [
            'movements' => $items,
            'movementCount' => $items->count(),
            'incomeTotal' => $items->where('direction', 'in')->sum('amount'),
            'expenseTotal' => $items->where('direction', 'out')->sum('amount'),
            'editCategories' => Category::query()
                ->where('workspace_id', $workspaceId)
                ->when($personal, fn ($q) => $q->where('user_id', $user->id))
                ->where(function ($query) {
                    $query->where('hidden_from_sidebar', false)
                        ->orWhereRaw('LOWER(TRIM(name)) = ?', ['outros']);
                })
                ->orderByRaw("CASE WHEN LOWER(TRIM(name)) = 'outros' THEN 1 ELSE 0 END ASC")
                ->orderBy('order')
                ->orderBy('name')
                ->get(),
            'editAccounts' => BankAccount::where('workspace_id', $workspaceId)
                ->when($personal, fn ($q) => $q->where('user_id', $user->id))
                ->orderBy('name')->get(),
            'conversionExpenseCategory' => $this->conversionType === 'expense' && $this->editingCategoryId !== ''
                ? $this->conversionCategoryQuery()->with(['fields' => fn ($query) => $query->orderBy('order')])->find((int) $this->editingCategoryId)
                : null,
        ]);
    }
}
