<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = ['user_id', 'workspace_id', 'subject', 'status', 'priority'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ADICIONA ESTA FUNÇÃO AQUI EM BAIXO:
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }
}
