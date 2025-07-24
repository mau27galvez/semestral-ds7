<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'paragraph',
        'images',
        'author',
        'is_published',
        'category_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get the category that owns the news.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the news's initials for display
     */
    public function initials(): string
    {
        return Str::of($this->title)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the first image or default placeholder
     */
    public function getFirstImageAttribute(): ?string
    {
        $images = $this->images ?? [];
        return count($images) > 0 ? $images[0] : null;
    }

    /**
     * Get excerpt of paragraph
     */
    public function getExcerptAttribute(): string
    {
        return Str::limit($this->paragraph, 100);
    }

    /**
     * Scope for published news
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for draft news
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }
}
