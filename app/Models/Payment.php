<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    protected $fillable = ['user_id', 'invoice_id', 'plan_type', 'amount', 'status', 'method', 'paid_at'];
    public function user() { return $this->belongsTo(User::class); }
}
