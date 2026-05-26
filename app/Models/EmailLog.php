<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace; // Importante
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model {
    use BelongsToWorkspace; // Ativa o filtro automático

    protected $fillable = [
        'user_id',
        'workspace_id', // Adiciona esta linha
        'subject',
        'month_reference',
        'sent_at'
    ];
}
