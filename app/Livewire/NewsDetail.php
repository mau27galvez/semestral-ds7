<?php

namespace App\Livewire;

use App\Models\News;
use App\Models\Comment;
use App\Models\NewsLike;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class NewsDetail extends Component
{
    public News $news;
    public string $commentContent = '';

    public function mount(News $news)
    {
        // Only show published news
        if (!$news->is_published) {
            abort(404);
        }

        $this->news = $news;

        // Increment view count
        $this->news->incrementViews();
    }

    #[Computed]
    public function comments()
    {
        return $this->news->activeComments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function isLiked()
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->news->isLikedBy(Auth::user());
    }

    #[Computed]
    public function likesCount()
    {
        return $this->news->likes()->count();
    }

    public function toggleLike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $existingLike = NewsLike::where('news_id', $this->news->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            NewsLike::create([
                'news_id' => $this->news->id,
                'user_id' => $user->id,
            ]);
        }

        // Reset computed properties
        unset($this->isLiked);
        unset($this->likesCount);
    }

    public function addComment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->news->comments_enabled) {
            session()->flash('error', 'Comments are disabled for this article.');
            return;
        }

        $this->validate([
            'commentContent' => 'required|string|min:3|max:1000',
        ]);

        Comment::create([
            'news_id' => $this->news->id,
            'user_id' => Auth::id(),
            'content' => $this->commentContent,
        ]);

        $this->commentContent = '';

        // Reset computed properties
        unset($this->comments);

        session()->flash('success', 'Comment added successfully!');
    }

    public function toggleCommentStatus($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        $user = Auth::user();

        // Only admins and supervisors can moderate comments
        if (!$user || (!$user->isAdmin() && !$user->isSupervisor())) {
            abort(403);
        }

        $comment->update(['is_active' => !$comment->is_active]);

        // Reset computed properties
        unset($this->comments);

        $status = $comment->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Comment {$status} successfully!");
    }

    public function render()
    {
        return view('livewire.news-detail');
    }
}
