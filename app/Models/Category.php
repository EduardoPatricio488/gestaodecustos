<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToWorkspace;
use Illuminate\Support\Str;

class Category extends Model
{
    use BelongsToWorkspace;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'name',
        'slug',
        'color',
        'order',
        'icon',
        'budget_limit',
        'is_fixed',
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = static::uniqueSlugFor(
                    $category->name,
                    $category->workspace_id,
                    $category->id
                );
            }
        });
    }

    public function resolveSlug(): string
    {
        if (filled($this->slug)) {
            return $this->slug;
        }

        return static::uniqueSlugFor($this->name, $this->workspace_id, $this->id);
    }

    public static function uniqueSlugFor(string $name, ?int $workspaceId, ?int $exceptId = null): string
    {
        $slug = Str::slug($name, '-');
        $base = $slug;
        $i = 2;

        while (static::slugExistsInWorkspace($slug, $workspaceId, $exceptId)) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public static function backfillMissingSlugs(?int $workspaceId = null): int
    {
        $query = static::query()
            ->where(fn ($q) => $q->whereNull('slug')->orWhere('slug', ''));

        if ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        }

        $count = 0;

        $query->each(function (Category $category) use (&$count) {
            $category->slug = static::uniqueSlugFor(
                $category->name,
                $category->workspace_id,
                $category->id
            );
            $category->saveQuietly();
            $count++;
        });

        return $count;
    }

    private static function slugExistsInWorkspace(string $slug, ?int $workspaceId, ?int $exceptId = null): bool
    {
        $query = static::where('workspace_id', $workspaceId)->where('slug', $slug);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(\App\Models\CategoryField::class)->orderBy('order');
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(CategoryWidget::class)->orderBy('order');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
