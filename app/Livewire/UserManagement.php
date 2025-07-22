<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Flux\Flux;

class UserManagement extends Component
{
    use WithPagination, AuthorizesRequests;

    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    public ?User $selectedUser = null;
    public string $search = '';

    // Livewire-native message handling
    public string $successMessage = '';
    public string $errorMessage = '';

    // User form properties
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('nullable|string|min:8')]
    public string $password_confirmation = '';

    public function mount()
    {
        // Only admins or users with specific permission can access user management
        $this->authorize('viewAny', User::class);
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when(
                $this->search,
                fn($query) =>
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function createUser()
    {
        $this->authorize('create', User::class);

        $this->resetForm();
        Flux::modal('create-user')->show();
    }

    public function storeUser()
    {
        $this->authorize('create', User::class);

        // Add unique email validation for creation
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'email_verified_at' => now(), // Auto-verify for admin created users
        ]);

        Flux::modal('create-user')->close();
        $this->resetForm();

        // Livewire-native success message
        $this->successMessage = "User '{$this->name}' created successfully!";
        $this->clearMessagesAfterDelay();

        // Dispatch event for potential listeners
        $this->dispatch('user-created', name: $this->name);
    }

    public function editUser(User $user)
    {
        $this->authorize('update', $user);

        $this->selectedUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';

        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->authorize('update', $this->selectedUser);

        // Add unique email validation excluding current user
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->selectedUser->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        // Only update password if provided
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $userName = $this->selectedUser->name;
        $this->selectedUser->update($data);

        $this->showEditModal = false;
        $this->resetForm();

        // Livewire-native success message
        $this->successMessage = "User '{$userName}' updated successfully!";
        $this->clearMessagesAfterDelay();

        $this->dispatch('user-updated', name: $userName);
    }

    public function confirmDelete(User $user)
    {
        $this->authorize('delete', $user);

        $this->selectedUser = $user;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        $this->authorize('delete', $this->selectedUser);

        // Prevent self-deletion
        if ($this->selectedUser->id === Auth::id()) {
            $this->errorMessage = 'You cannot delete your own account!';
            $this->showDeleteModal = false;
            $this->clearMessagesAfterDelay();
            return;
        }

        $userName = $this->selectedUser->name;
        $this->selectedUser->delete();

        $this->showDeleteModal = false;
        $this->selectedUser = null;

        // Livewire-native success message
        $this->successMessage = "User '{$userName}' deleted successfully!";
        $this->clearMessagesAfterDelay();

        $this->dispatch('user-deleted', name: $userName);
    }

    public function closeModal()
    {
        Flux::modal('create-user')->close();
        Flux::modal('delete-user-{{ $selectedUser->id }}')->close();
        Flux::modal('edit-user-{{ $selectedUser->id }}')->close();
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
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedUser = null;
        $this->resetErrorBag();

        // Clear any existing messages when resetting
        $this->clearMessages();
    }

    public function render()
    {
        return view('livewire.user-management')
            ->name('User Management');
    }
}
