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
        'author_id',
        'is_published',
        'category_id',
        'views_count',
        'comments_enabled',
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
            'comments_enabled' => 'boolean',
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
     * Get the user that authored the news.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the comments for the news.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the active comments for the news.
     */
    public function activeComments()
    {
        return $this->hasMany(Comment::class)->where('is_active', true);
    }

    /**
     * Get the likes for the news.
     */
    public function likes()
    {
        return $this->hasMany(NewsLike::class);
    }

    /**
     * Get the users who liked the news.
     */
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'news_likes');
    }

    /**
     * Check if a user liked this news.
     */
    public function isLikedBy($user)
    {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the likes count.
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * Get the active comments count.
     */
    public function getActiveCommentsCountAttribute()
    {
        return $this->activeComments()->count();
    }

    /**
     * Increment the views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
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
