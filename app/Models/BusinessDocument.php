<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BusinessDocument extends Model
{
    use BelongsToWorkspace;

    protected $fillable = ['workspace_id', 'name', 'category', 'file_path', 'expiry_date', 'notes'];

    protected $casts = ['expiry_date' => 'date'];

    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }

    public function isExpired(): bool
    {
        return $this->expiry_date ? $this->expiry_date->isPast() : false;
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date ? $this->expiry_date->isFuture() && $this->expiry_date->diffInDays(now()) <= 30 : false;
    }

    public function getIcon(): string
    {
        return match ($this->category) {
            'Legal' => 'document-text', 'RH' => 'users', 'Seguros' => 'shield-check', 'Impostos' => 'receipt-percent', default => 'document',
        };
    }

    /**
     * Documentos empresariais ficam no disco privado. Não expor Storage::url()
     * para evitar que um link público contorne a autorização do workspace.
     */
    public function getUrlAttribute(): ?string
    {
        return null;
    }
}
