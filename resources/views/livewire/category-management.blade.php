<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Category Management') }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Manage categories, create new categories, and update category information.') }}</p>
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
            <flux:input wire:model.live.debounce.300ms="search" type="text" :label="__('Search categories...')" />
        </div>

        <flux:button wire:click="createCategory" wire:loading.attr="disabled" icon:trailing="plus"
            class=" bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 disabled:cursor-not-allowed">
            {{ __('Create Category') }}
        </flux:button>
    </div>

    {{-- Categories Table --}}
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Slug') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Created') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($this->categories as $category)
                        <tr wire:key="category-{{ $category->id }}" class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center text-white font-medium text-sm">
                                            {{ $category->initials() }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $category->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <code
                                    class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ $category->slug }}</code>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <flux:tooltip :content="$category->created_at->format('F j, Y \a\t g:i A')">
                                    {{ $category->created_at->diffForHumans() }}
                                </flux:tooltip>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <flux:tooltip content="{{ __('Edit Category') }}">
                                        <flux:button icon="pencil" wire:click="editCategory({{ $category->id }})" />
                                    </flux:tooltip>

                                    {{-- Edit Category Modal --}}
                                    <flux:modal name="edit-category-{{ $category->id }}" class="w-full max-w-xl">
                                        <form wire:submit="updateCategory({{ $category->id }})">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">
                                                        {{ __('Edit Category') }}
                                                    </flux:heading>
                                                    <flux:text class="mt-2">
                                                        {{ __('Update category information.') }}
                                                    </flux:text>
                                                </div>

                                                <div class="space-y-4">
                                                    <flux:input wire:model="updateCategoryForm.name"
                                                        :label="__('Name')" />

                                                    <flux:input wire:model="updateCategoryForm.slug"
                                                        :label="__('Slug')" />
                                                </div>

                                                <div class="flex gap-3 justify-end">
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">
                                                            {{ __('Cancel') }}
                                                        </flux:button>
                                                    </flux:modal.close>

                                                    <flux:button type="submit" wire:loading.attr="disabled"
                                                        wire:target="updateCategory" variant="primary">
                                                        {{ __('Update Category') }}
                                                    </flux:button>
                                                </div>
                                            </div>
                                        </form>
                                    </flux:modal>

                                    <flux:modal.trigger name="delete-category-{{ $category->id }}">
                                        <flux:tooltip content="{{ __('Delete Category') }}">
                                            <flux:button variant="danger" icon="trash" />
                                        </flux:tooltip>
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-category-{{ $category->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Delete category?</flux:heading>
                                                <flux:text class="mt-2">
                                                    <p>You're about to delete the category {{ $category->name }}.</p>
                                                    <p>This action cannot be reversed.</p>
                                                </flux:text>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Cancel</flux:button>
                                                </flux:modal.close>
                                                <flux:button type="submit" variant="danger"
                                                    wire:click="deleteCategory({{ $category->id }})">
                                                    Delete category
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    @if ($search)
                                        {{ __('No categories found matching ":search"', ['search' => $search]) }}
                                    @else
                                        {{ __('No categories found.') }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($this->categories->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $this->categories->links() }}
            </div>
        @endif
    </div>

    {{-- Create Category Modal --}}
    <flux:modal name="create-category" class="w-full max-w-xl">
        <form wire:submit="storeCategory">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('Create New Category') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Add a new category to the system.') }}</p>
                </div>

                <div class="space-y-4">
                    <flux:input wire:model.live="name" :label="__('Name')" />

                    <flux:input wire:model="slug" :label="__('Slug')" />

                    <flux:text class="mt-2 text-xs text-gray-500">
                        {{ __('The slug will be automatically generated from the name, but you can customize it.') }}
                    </flux:text>
                </div>

                <div class="flex gap-3 justify-end">
                    <flux:button type="button" wire:click="closeModal" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" wire:loading.attr="disabled" wire:target="storeCategory"
                        variant="primary">
                        <span wire:loading.remove wire:target="storeCategory">
                            {{ __('Create Category') }}
                        </span>
                        <span wire:loading wire:target="storeCategory" class="flex items-center">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2">
                            </div>
                            {{ __('Creating...') }}
                        </span>
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
