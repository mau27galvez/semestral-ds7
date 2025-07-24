<?php

namespace App\Livewire;

use App\Models\News;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Flux\Flux;
use App\Livewire\Forms\UpdateNewsForm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class NewsManagement extends Component
{
    use WithPagination, AuthorizesRequests, WithFileUploads;

    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public ?News $selectedNews = null;
    public string $search = '';

    // Livewire-native message handling
    public string $successMessage = '';
    public string $errorMessage = '';

    // News form properties
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string')]
    public string $paragraph = '';

    #[Validate('required|exists:users,id')]
    public int $author_id = 0;

    public bool $is_published = false;

    #[Validate('required|exists:categories,id')]
    public int $category_id = 0;

    public array $images = [];

    public UpdateNewsForm $updateNewsForm;

    public function mount()
    {
        // Only users with specific permission can access news management
        $this->authorize('viewAny', News::class);

        // Set default author to the currently authenticated user
        $this->author_id = Auth::id();
    }

    #[Computed]
    public function news()
    {
        return News::query()
            ->with(['category', 'author'])
            ->when(
                $this->search,
                fn($query) =>
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('paragraph', 'like', '%' . $this->search . '%')
                    ->orWhereHas('author', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function createNews()
    {
        $this->authorize('create', News::class);

        $this->resetForm();
        Flux::modal('create-news')->show();
    }

    public function storeNews()
    {
        $this->authorize('create', News::class);

        // Validate form data
        $this->validate([
            'title' => 'required|string|max:255',
            'paragraph' => 'required|string',
            'author_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'images' => 'required|array|min:3',
            'images.*' => 'image|max:2048',
        ], [
            'images.min' => 'You must upload at least 3 images.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.max' => 'Each image must not be larger than 2MB.',
        ]);

        // Store images
        $imagePaths = [];
        foreach ($this->images as $image) {
            $path = $image->store('news-images', 'public');
            $imagePaths[] = $path;
        }

        News::create([
            'title' => $this->title,
            'paragraph' => $this->paragraph,
            'author_id' => $this->author_id,
            'is_published' => $this->is_published,
            'category_id' => $this->category_id,
            'images' => $imagePaths,
        ]);

        Flux::modal('create-news')->close();
        $this->resetForm();

        // Livewire-native success message
        $this->successMessage = "News '{$this->title}' created successfully!";
        $this->clearMessagesAfterDelay();

        // Dispatch event for potential listeners
        $this->dispatch('news-created', title: $this->title);
    }

    public function editNews($newsId)
    {
        $news = News::findOrFail($newsId);
        $this->authorize('update', $news);

        // Populate the form with existing data
        $this->updateNewsForm->id = $news->id;
        $this->updateNewsForm->title = $news->title;
        $this->updateNewsForm->paragraph = $news->paragraph;
        $this->updateNewsForm->author_id = $news->author_id;
        $this->updateNewsForm->is_published = $news->is_published;
        $this->updateNewsForm->category_id = $news->category_id;
        $this->updateNewsForm->existing_images = $news->images ?? [];
        $this->updateNewsForm->new_images = [];

        Flux::modal("edit-news-{$news->id}")->show();
    }

    public function updateNews($newsId)
    {
        $this->updateNewsForm->id = $newsId;
        $this->updateNewsForm->validate();

        $news = News::findOrFail($this->updateNewsForm->id);
        $this->authorize('update', $news);

        // Handle new images
        $allImages = $this->updateNewsForm->existing_images;
        if (!empty($this->updateNewsForm->new_images)) {
            foreach ($this->updateNewsForm->new_images as $image) {
                $path = $image->store('news-images', 'public');
                $allImages[] = $path;
            }
        }

        $data = [
            'title' => $this->updateNewsForm->title,
            'paragraph' => $this->updateNewsForm->paragraph,
            'author_id' => $this->updateNewsForm->author_id,
            'is_published' => $this->updateNewsForm->is_published,
            'category_id' => $this->updateNewsForm->category_id,
            'images' => $allImages,
        ];

        $newsTitle = $news->title;
        $news->update($data);

        $this->showEditModal = false;
        $this->resetForm();

        // Livewire-native success message
        $this->successMessage = "News '{$newsTitle}' updated successfully!";
        $this->clearMessagesAfterDelay();

        Flux::modal("edit-news-{$news->id}")->close();
        $this->dispatch('news-updated', title: $newsTitle);
    }

    public function deleteNews($newsId)
    {
        $news = News::findOrFail($newsId);
        $this->authorize('delete', $news);

        // Delete associated images
        foreach ($news->images as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $newsTitle = $news->title;
        $news->delete();

        // Livewire-native success message
        $this->successMessage = "News '{$newsTitle}' deleted successfully!";
        $this->clearMessagesAfterDelay();

        $this->dispatch('news-deleted', title: $newsTitle);
    }

    public function togglePublished($newsId)
    {
        $news = News::findOrFail($newsId);
        $this->authorize('update', $news);

        $news->update(['is_published' => !$news->is_published]);

        $status = $news->is_published ? 'published' : 'unpublished';
        $this->successMessage = "News '{$news->title}' {$status} successfully!";
        $this->clearMessagesAfterDelay();
    }

    public function removeExistingImage($imageIndex)
    {
        if (isset($this->updateNewsForm->existing_images[$imageIndex])) {
            unset($this->updateNewsForm->existing_images[$imageIndex]);
            $this->updateNewsForm->existing_images = array_values($this->updateNewsForm->existing_images);
        }
    }

    public function closeModal()
    {
        Flux::modal('create-news')->close();
        Flux::modal('delete-news-{{ $selectedNews->id }}')->close();
        Flux::modal('edit-news-{{ $selectedNews->id }}')->close();
        $this->resetForm();
    }

    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    private function clearMessagesAfterDelay()
    {
        // Auto-clear messages after 5 seconds using Alpine.js
        $this->dispatch('message-shown');
    }

    private function resetForm()
    {
        $this->title = '';
        $this->paragraph = '';
        $this->author_id = Auth::id(); // Reset to current user
        $this->is_published = false;
        $this->category_id = 0;
        $this->images = [];

        // Reset the UpdateNewsForm
        $this->updateNewsForm->reset();

        $this->selectedNews = null;
        $this->resetErrorBag();

        // Clear any existing messages when resetting
        $this->clearMessages();
    }

    public function render()
    {
        return view('livewire.news-management')
            ->name('News Management');
    }
}
