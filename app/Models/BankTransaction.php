<?php
namespace App\Models;
use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BankTransaction extends Model
{
    use BelongsToWorkspace;
    protected $fillable = ['workspace_id','bank_account_id','user_id','transaction_date','amount','currency','description','external_reference','status','matched_type','matched_id','reconciled_at'];
    protected $casts = ['transaction_date'=>'date','amount'=>'decimal:2','reconciled_at'=>'datetime'];
    public function bankAccount(): BelongsTo { return $this->belongsTo(BankAccount::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function isReconciled(): bool { return $this->status === 'reconciled'; }
}
