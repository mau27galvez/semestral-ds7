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
use App\Livewire\Forms\UpdateUserForm;

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

    #[Validate('required|string|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|in:admin,supervisor,editor,regular')]
    public string $role = 'regular';

    #[Validate('nullable|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('nullable|string|min:8')]
    public string $password_confirmation = '';

    public UpdateUserForm $updateUserForm;

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

    #[Computed]
    public function availableRoles()
    {
        return [
            'regular' => 'Regular User',
            'editor' => 'Editor',
            'supervisor' => 'Supervisor',
            'admin' => 'Administrator',
        ];
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
            'role' => 'required|in:admin,supervisor,editor,regular',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
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

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        // Populate the form with existing data
        $this->updateUserForm->id = $user->id;
        $this->updateUserForm->name = $user->name;
        $this->updateUserForm->email = $user->email;
        $this->updateUserForm->role = $user->role;
        $this->updateUserForm->password = ''; // Don't pre-fill password

        Flux::modal("edit-user-{$user->id}")->show();
    }

    public function updateUser($userId)
    {
        $this->updateUserForm->id = $userId;
        $this->updateUserForm->validate();

        $user = User::findOrFail($this->updateUserForm->id);
        $this->authorize('update', $user);

        $data = [
            'name' => $this->updateUserForm->name,
            'email' => $this->updateUserForm->email,
            'role' => $this->updateUserForm->role,
        ];

        // Only update password if provided
        if (!empty($this->updateUserForm->password)) {
            $data['password'] = Hash::make($this->updateUserForm->password);
        }

        $userName = $user->name;
        $user->update($data);

        $this->showEditModal = false;
        $this->resetForm();

        // Livewire-native success message
        $this->successMessage = "User '{$userName}' updated successfully!";
        $this->clearMessagesAfterDelay();

        Flux::modal("edit-user-{$user->id}")->close();

        $this->dispatch('user-updated', name: $userName);
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('delete', $user);

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            $this->errorMessage = 'You cannot delete your own account!';
            $this->clearMessagesAfterDelay();
            return;
        }

        $userName = $user->name;
        $user->delete();

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
        $this->role = 'regular';
        $this->password = '';
        $this->password_confirmation = '';

        // Reset the UpdateUserForm
        $this->updateUserForm->reset();

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
