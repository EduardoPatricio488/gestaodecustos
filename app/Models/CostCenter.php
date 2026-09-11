<?php
namespace App\Models;
use App\Traits\BelongsToWorkspace;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class CostCenter extends Model
{
    use BelongsToWorkspace, LogsActivity;
    protected $fillable = ['workspace_id','user_id','code','name','description','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
}
