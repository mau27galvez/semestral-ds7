<div>
    <flux:heading size="xl">{{ __('User Management') }}</flux:heading>
    <flux:subheading>{{ __('Manage users, create new accounts, and update user information.') }}</flux:subheading>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <flux:banner variant="success" class="mb-6">
            {{ session('message') }}
        </flux:banner>
    @endif

    @if (session()->has('error'))
        <flux:banner variant="danger" class="mb-6">
            {{ session('error') }}
        </flux:banner>
    @endif

    {{-- Actions Bar --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex-1 max-w-md">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search users...') }}"
                icon="magnifying-glass" clearable />
        </div>

        <flux:button wire:click="createUser" variant="primary" icon="plus">
            {{ __('Create User') }}
        </flux:button>
    </div>

    {{-- Users Table --}}
    <flux:card>
        <flux:table>
            <flux:columns>
                <flux:column>{{ __('Name') }}</flux:column>
                <flux:column>{{ __('Email') }}</flux:column>
                <flux:column>{{ __('Created') }}</flux:column>
                <flux:column>{{ __('Verified') }}</flux:column>
                <flux:column class="w-32">{{ __('Actions') }}</flux:column>
            </flux:columns>

            <flux:rows>
                @forelse ($this->users as $user)
                    <flux:row wire:key="user-{{ $user->id }}">
                        <flux:cell>
                            <div class="flex items-center gap-3">
                                <flux:avatar :initials="$user->initials()" size="sm" />
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    @if ($user->id === auth()->id())
                                        <flux:badge size="sm" color="indigo">{{ __('You') }}</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </flux:cell>

                        <flux:cell>{{ $user->email }}</flux:cell>

                        <flux:cell>
                            <flux:tooltip :content="$user->created_at->format('F j, Y \a\t g:i A')">
                                {{ $user->created_at->diffForHumans() }}
                            </flux:tooltip>
                        </flux:cell>

                        <flux:cell>
                            @if ($user->email_verified_at)
                                <flux:badge color="green" size="sm">{{ __('Verified') }}</flux:badge>
                            @else
                                <flux:badge color="amber" size="sm">{{ __('Unverified') }}</flux:badge>
                            @endif
                        </flux:cell>

                        <flux:cell>
                            <div class="flex items-center gap-2">
                                <flux:tooltip content="{{ __('Edit User') }}">
                                    <flux:button wire:click="editUser({{ $user->id }})" size="sm"
                                        variant="ghost" icon="pencil" />
                                </flux:tooltip>

                                @if ($user->id !== auth()->id())
                                    <flux:tooltip content="{{ __('Delete User') }}">
                                        <flux:button wire:click="confirmDelete({{ $user->id }})" size="sm"
                                            variant="ghost" icon="trash" class="text-red-600 hover:text-red-700" />
                                    </flux:tooltip>
                                @endif
                            </div>
                        </flux:cell>
                    </flux:row>
                @empty
                    <flux:row>
                        <flux:cell colspan="5" class="text-center py-8">
                            <div class="text-zinc-500">
                                @if ($search)
                                    {{ __('No users found matching ":search"', ['search' => $search]) }}
                                @else
                                    {{ __('No users found.') }}
                                @endif
                            </div>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($this->users->hasPages())
            <div class="mt-6">
                {{ $this->users->links() }}
            </div>
        @endif
    </flux:card>

    {{-- Create User Modal --}}
    <flux:modal name="create-user-modal" :show="$showCreateModal" wire:model="showCreateModal">
        <form wire:submit="storeUser">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Create New User') }}</flux:heading>
                    <flux:subheading>{{ __('Add a new user to the system.') }}</flux:subheading>
                </div>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:input wire:model="name" placeholder="{{ __('Enter user name') }}" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email') }}</flux:label>
                        <flux:input type="email" wire:model="email" placeholder="{{ __('Enter email address') }}" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Password') }}</flux:label>
                        <flux:input type="password" wire:model="password" placeholder="{{ __('Enter password') }}" />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Confirm Password') }}</flux:label>
                        <flux:input type="password" wire:model="password_confirmation"
                            placeholder="{{ __('Confirm password') }}" />
                        <flux:error name="password_confirmation" />
                    </flux:field>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:button type="button" wire:click="closeModal" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary" :loading="$wire.busy">
                        {{ __('Create User') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Edit User Modal --}}
    <flux:modal name="edit-user-modal" :show="$showEditModal" wire:model="showEditModal">
        <form wire:submit="updateUser">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Edit User') }}</flux:heading>
                    <flux:subheading>{{ __('Update user information.') }}</flux:subheading>
                </div>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Name') }}</flux:label>
                        <flux:input wire:model="name" placeholder="{{ __('Enter user name') }}" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Email') }}</flux:label>
                        <flux:input type="email" wire:model="email" placeholder="{{ __('Enter email address') }}" />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('New Password') }}</flux:label>
                        <flux:input type="password" wire:model="password"
                            placeholder="{{ __('Leave blank to keep current password') }}" />
                        <flux:error name="password" />
                        <flux:description>{{ __('Leave blank to keep the current password.') }}</flux:description>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Confirm New Password') }}</flux:label>
                        <flux:input type="password" wire:model="password_confirmation"
                            placeholder="{{ __('Confirm new password') }}" />
                        <flux:error name="password_confirmation" />
                    </flux:field>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:button type="button" wire:click="closeModal" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary" :loading="$wire.busy">
                        {{ __('Update User') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-user-modal" :show="$showDeleteModal" wire:model="showDeleteModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" class="text-red-600">{{ __('Delete User') }}</flux:heading>
                <flux:subheading>{{ __('This action cannot be undone.') }}</flux:subheading>
            </div>

            @if ($selectedUser)
                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                    <p class="text-sm text-red-800 dark:text-red-200">
                        {{ __('Are you sure you want to delete :name? This will permanently remove the user and all associated data.', ['name' => $selectedUser->name]) }}
                    </p>
                </div>
            @endif

            <div class="flex gap-2 justify-end">
                <flux:button type="button" wire:click="closeModal" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button wire:click="deleteUser" variant="danger" :loading="$wire.busy">
                    {{ __('Delete User') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
