<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;
use App\Livewire\Forms\UpdateCategoryForm;
use Illuminate\Support\Str;

class CategoryManagement extends Component
{
    use WithPagination, AuthorizesRequests;

    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public ?Category $selectedCategory = null;
    public string $search = '';

    // Livewire-native message handling
    public string $successMessage = '';
    public string $errorMessage = '';

    // Category form properties
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255|unique:categories,slug')]
    public string $slug = '';

    public UpdateCategoryForm $updateCategoryForm;

    public function mount()
    {
        // Only users with specific permission can access category management
        $this->authorize('viewAny', Category::class);
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->when(
                $this->search,
                fn($query) =>
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%')
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedName()
    {
        // Auto-generate slug when name changes
        if (!empty($this->name)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function createCategory()
    {
        $this->authorize('create', Category::class);

        $this->resetForm();
        Flux::modal('create-category')->show();
    }

    public function storeCategory()
    {
        $this->authorize('create', Category::class);

        // Add unique validation for creation
        $this->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'required|string|max:255|unique:categories',
        ]);

        Category::create([
            'name' => $this->name,
            'slug' => $this->slug,
        ]);

        Flux::modal('create-category')->close();
        $this->resetForm();

        // Livewire-native success message
        $this->successMessage = "Category '{$this->name}' created successfully!";
        $this->clearMessagesAfterDelay();

        // Dispatch event for potential listeners
        $this->dispatch('category-created', name: $this->name);
    }

    public function updateCategory($categoryId)
    {
        $this->updateCategoryForm->id = $categoryId;
        $this->updateCategoryForm->validate();

        $category = Category::findOrFail($this->updateCategoryForm->id);
        $this->authorize('update', $category);

        $data = [
            'name' => $this->updateCategoryForm->name,
            'slug' => $this->updateCategoryForm->slug,
        ];

        $categoryName = $category->name;
        $category->update($data);

        $this->showEditModal = false;
        $this->resetForm();

        // Livewire-native success message
        $this->successMessage = "Category '{$categoryName}' updated successfully!";
        $this->clearMessagesAfterDelay();

        $this->dispatch('category-updated', name: $categoryName);
    }

    public function deleteCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $this->authorize('delete', $category);

        $categoryName = $category->name;
        $category->delete();

        // Livewire-native success message
        $this->successMessage = "Category '{$categoryName}' deleted successfully!";
        $this->clearMessagesAfterDelay();

        $this->dispatch('category-deleted', name: $categoryName);
    }

    public function closeModal()
    {
        Flux::modal('create-category')->close();
        Flux::modal('delete-category-{{ $selectedCategory->id }}')->close();
        Flux::modal('edit-category-{{ $selectedCategory->id }}')->close();
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
        $this->name = '';
        $this->slug = '';

        // Reset the UpdateCategoryForm
        $this->updateCategoryForm->reset();

        $this->selectedCategory = null;
        $this->resetErrorBag();

        // Clear any existing messages when resetting
        $this->clearMessages();
    }

    public function render()
    {
        return view('livewire.category-management')
            ->name('Category Management');
    }
}
