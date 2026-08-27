<?php

namespace App\Models;

use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'workspace_id', // Adicionado
        'client_name',
        'invoice_number',
        'amount_excl_vat',
        'vat_amount',
        'total_amount',
        'currency',
        'amount_excl_vat_converted',
        'vat_amount_converted',
        'total_amount_converted',
        'status',
        'due_date',
    ];

    protected $casts = [
        'amount_excl_vat' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_excl_vat_converted' => 'decimal:2',
        'vat_amount_converted' => 'decimal:2',
        'total_amount_converted' => 'decimal:2',
        'due_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice): void {
            if (! $invoice->workspace_id) {
                return;
            }

            $workspaceCurrency = strtoupper((string) (Workspace::find($invoice->workspace_id)?->currency ?? 'EUR'));
            $invoiceCurrency = strtoupper((string) ($invoice->currency ?: $workspaceCurrency));

            $invoice->currency = $invoiceCurrency;
            $invoice->forceFill([
                'amount_excl_vat_converted' => round((float) CurrencyService::convert(
                    (float) $invoice->amount_excl_vat,
                    $invoiceCurrency,
                    $workspaceCurrency
                ), 2),
                'vat_amount_converted' => round((float) CurrencyService::convert(
                    (float) $invoice->vat_amount,
                    $invoiceCurrency,
                    $workspaceCurrency
                ), 2),
                'total_amount_converted' => round((float) CurrencyService::convert(
                    (float) $invoice->total_amount,
                    $invoiceCurrency,
                    $workspaceCurrency
                ), 2),
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
