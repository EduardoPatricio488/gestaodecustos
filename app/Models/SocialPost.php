<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialPost extends Model
{
    protected $fillable = [
        'user_id', 'workspace_id', 'type', 'content',
        'media_path', 'financial_type', 'financial_id',
        'visibility', 'is_story', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_story'   => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public static function publishFinancialEvent($userId, $title, $type, $id, $visibility = 'workspace')
    {
        return self::create([
            'user_id'        => $userId,
            'workspace_id'   => auth()->user()->current_workspace_id,
            'type'           => 'financial',
            'content'        => $title,
            'financial_type' => $type,
            'financial_id'   => $id,
            'visibility'     => $visibility,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(SocialLike::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SocialComment::class, 'post_id');
    }

    /**
     * Extrai hashtags (#exemplo) do conteúdo do post.
     */
    public function getHashtagsAttribute(): array
    {
        if (!$this->content) {
            return [];
        }

        preg_match_all('/#([\p{L}0-9_]+)/u', $this->content, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    /**
     * Devolve o conteúdo com hashtags transformadas em links clicáveis (HTML-safe).
     */
    public function getContentHtmlAttribute(): string
    {
        $escaped = e($this->content ?? '');

        return preg_replace_callback(
            '/#([\p{L}0-9_]+)/u',
            fn ($m) => '<a href="'.route('social.hub', ['tag' => $m[1]]).'" wire:navigate class="font-bold text-indigo-500 hover:text-indigo-400 transition-colors">#'.$m[1].'</a>',
            $escaped
        );
    }

    public function scopeWithHashtag($query, string $tag)
    {
        return $query->where('content', 'like', '%#'.$tag.'%');
    }
}
