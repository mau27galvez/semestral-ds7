<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if user is editor
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if user is regular user
     */
    public function isRegular(): bool
    {
        return $this->role === 'regular';
    }

    /**
     * Check if user can manage users (admin only)
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage categories (admin only)
     */
    public function canManageCategories(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can create/edit news (admin, supervisor, editor)
     */
    public function canManageNews(): bool
    {
        return $this->isAdmin() || $this->isSupervisor() || $this->isEditor();
    }

    /**
     * Check if user can publish news (admin, supervisor only)
     */
    public function canPublishNews(): bool
    {
        return $this->isAdmin() || $this->isSupervisor();
    }

    /**
     * Check if user can access dashboard (everyone except regular users)
     */
    public function canAccessDashboard(): bool
    {
        return !$this->isRegular();
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'supervisor' => 'Supervisor',
            'editor' => 'Editor',
            'regular' => 'Regular User',
            default => 'Unknown'
        };
    }

    /**
     * Get the news authored by this user
     */
    public function news()
    {
        return $this->hasMany(News::class, 'author_id');
    }

    /**
     * Get the comments made by this user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the news liked by this user
     */
    public function likedNews()
    {
        return $this->belongsToMany(News::class, 'news_likes');
    }

    /**
     * Get the likes made by this user
     */
    public function newsLikes()
    {
        return $this->hasMany(NewsLike::class);
    }
}
