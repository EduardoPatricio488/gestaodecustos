<?php
namespace App\Models;
use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CreditNote extends Model
{
    use BelongsToWorkspace, LogsActivity;
    protected $fillable = ['workspace_id','user_id','invoice_id','number','amount_excl_vat','vat_amount','total_amount','currency','reason','issued_at','status'];
    protected $casts = ['amount_excl_vat'=>'decimal:2','vat_amount'=>'decimal:2','total_amount'=>'decimal:2','issued_at'=>'date'];
    protected static function booted(): void
    {
        static::saving(function (CreditNote $note): void {
            $note->amount_excl_vat = round((float) $note->amount_excl_vat, 2);
            $note->vat_amount = round((float) $note->vat_amount, 2);
            $note->total_amount = round((float) $note->amount_excl_vat + (float) $note->vat_amount, 2);
            if ($note->amount_excl_vat <= 0 || $note->vat_amount < 0) throw new \DomainException('Valores da nota de crédito inválidos.');
            $invoiceWorkspace = Invoice::withoutGlobalScopes()->whereKey($note->invoice_id)->value('workspace_id');
            if ((int) $invoiceWorkspace !== (int) $note->workspace_id) throw new \DomainException('A fatura não pertence à empresa.');
        });
    }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
