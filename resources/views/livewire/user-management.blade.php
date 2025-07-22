<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('User Management') }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Manage users, create new accounts, and update user information.') }}</p>
    </div>

    {{-- Livewire Reactive Messages --}}
    @if ($successMessage)
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            @message-shown.window="setTimeout(() => show = false, 5000)"
            class="mb-6 rounded-md bg-green-50 p-4 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <flux:icon.check-circle class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
                    <div class="text-sm text-green-800 dark:text-green-200">
                        {{ $successMessage }}
                    </div>
                </div>
                <button wire:click="clearMessages"
                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                    <flux:icon.x-mark class="w-4 h-4" />
                </button>
            </div>
        </div>
    @endif

    @if ($errorMessage)
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            @message-shown.window="setTimeout(() => show = false, 5000)"
            class="mb-6 rounded-md bg-red-50 p-4 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <flux:icon.exclamation-circle class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" />
                    <div class="text-sm text-red-800 dark:text-red-200">
                        {{ $errorMessage }}
                    </div>
                </div>
                <button wire:click="clearMessages"
                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200">
                    <flux:icon.x-mark class="w-4 h-4" />
                </button>
            </div>
        </div>
    @endif

    {{-- Actions Bar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between mb-6">
        <div class="flex-1 max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" type="text" :label="__('Search users...')" />
        </div>

        <flux:button wire:click="createUser" wire:loading.attr="disabled" icon:trailing="plus"
            class=" bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 disabled:cursor-not-allowed">
            {{ __('Create User') }}
        </flux:button>
    </div>

    {{-- Users Table --}}
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Email') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Created') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Verified') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($this->users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium text-sm">
                                            {{ $user->initials() }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $user->name }}
                                        </div>
                                        @if ($user->id === auth()->id())
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200">
                                                {{ __('You') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <flux:tooltip :content="$user->created_at->format('F j, Y \a\t g:i A')">
                                    {{ $user->created_at->diffForHumans() }}
                                </flux:tooltip>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->email_verified_at)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                                        {{ __('Verified') }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                        {{ __('Unverified') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <flux:tooltip content="{{ __('Edit User') }}">
                                        <flux:button wire:click="editUser({{ $user->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 p-1">
                                            <flux:icon.pencil class="w-4 h-4" />
                                        </flux:button>
                                    </flux:tooltip>

                                    @if ($user->id !== auth()->id())
                                        <flux:tooltip content="{{ __('Delete User') }}">
                                            <flux:button wire:click="confirmDelete({{ $user->id }})"
                                                class="text-red-600 hover:text-red-900 p-1">
                                                <flux:icon.trash class="w-4 h-4" />
                                            </flux:button>
                                        </flux:tooltip>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    @if ($search)
                                        {{ __('No users found matching ":search"', ['search' => $search]) }}
                                    @else
                                        {{ __('No users found.') }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($this->users->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $this->users->links() }}
            </div>
        @endif
    </div>

    {{-- Create User Modal --}}
    <flux:modal name="create-user" class="w-full max-w-xl">
        <form wire:submit="storeUser">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('Create New User') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Add a new user to the system.') }}</p>
                </div>

                <div class="space-y-4">
                    <flux:input wire:model="name" :label="__('Name')" />

                    <flux:input wire:model="email" :label="__('Email')" />

                    <flux:input wire:model="password" :label="__('Password')" type="password" />

                    <flux:input wire:model="password_confirmation" :label="__('Confirm Password')" type="password" />
                </div>

                <div class="flex gap-3 justify-end">
                    <flux:button type="button" wire:click="closeModal" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" wire:loading.attr="disabled" wire:target="storeUser"
                        variant="primary">
                        <span wire:loading.remove wire:target="storeUser">
                            {{ __('Create User') }}
                        </span>
                        <span wire:loading wire:target="storeUser" class="flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2">
                            </div>
                            {{ __('Creating...') }}
                        </span>
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Edit User Modal --}}
    @if ($showEditModal)
        <flux:modal name="edit-user" class="w-full max-w-xl">
            <form wire:submit="updateUser">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Edit User') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Update user information.') }}</p>
                    </div>

                    <div
                        class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <form wire:submit="updateUser">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ __('Edit User') }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Update user information.') }}</p>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Name') }}</label>
                                        <input wire:model="name" type="text"
                                            placeholder="{{ __('Enter user name') }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                                        @error('name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Email') }}</label>
                                        <input wire:model="email" type="email"
                                            placeholder="{{ __('Enter email address') }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                                        @error('email')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('New Password') }}</label>
                                        <input wire:model="password" type="password"
                                            placeholder="{{ __('Leave blank to keep current password') }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                                        @error('password')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ __('Leave blank to keep the current password.') }}</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Confirm New Password') }}</label>
                                        <input wire:model="password_confirmation" type="password"
                                            placeholder="{{ __('Confirm new password') }}"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                                        @error('password_confirmation')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex gap-3 justify-end">
                                    <flux:button type="button" wire:click="closeModal"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-900">
                                        {{ __('Cancel') }}
                                    </flux:button>

                                    <flux:button type="submit" wire:loading.attr="disabled" wire:target="updateUser"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50">
                                        <span wire:loading.remove wire:target="updateUser">
                                            {{ __('Update User') }}
                                        </span>
                                        <span wire:loading wire:target="updateUser" class="flex items-center">
                                            <div
                                                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2">
                                            </div>
                                            {{ __('Updating...') }}
                                        </span>
                                    </flux:button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </form>
        </flux:modal>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-red-600">{{ __('Delete User') }}</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('This action cannot be undone.') }}</p>
                        </div>

                        @if ($selectedUser)
                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                <p class="text-sm text-red-800 dark:text-red-200">
                                    {{ __('Are you sure you want to delete :name? This will permanently remove the user and all associated data.', ['name' => $selectedUser->name]) }}
                                </p>
                            </div>
                        @endif

                        <div class="flex gap-3 justify-end">
                            <flux:button type="button" wire:click="closeModal"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-900">
                                {{ __('Cancel') }}
                            </flux:button>

                            <flux:button wire:click="deleteUser" wire:loading.attr="disabled"
                                wire:target="deleteUser"
                                class="bg-red-600 hover:bg-red-700 text-white disabled:opacity-50">
                                <span wire:loading.remove wire:target="deleteUser">
                                    {{ __('Delete User') }}
                                </span>
                                <span wire:loading wire:target="deleteUser" class="flex items-center">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                    {{ __('Deleting...') }}
                                </span>
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
