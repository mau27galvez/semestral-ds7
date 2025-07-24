<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('News Management') }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Manage news articles, create new stories, and update news information.') }}</p>
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
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between mb-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex-1 min-w-[300px]">
                <flux:input wire:model.live.debounce.300ms="search" type="text" :label="__('Search news...')" />
            </div>
        </div>

        <flux:button wire:click="createNews" wire:loading.attr="disabled" icon:trailing="plus"
            class="bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 disabled:cursor-not-allowed">
            {{ __('Create News') }}
        </flux:button>
    </div>

    {{-- News Table --}}
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Title') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Author') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Category') }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('Status') }}
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
                    @forelse ($this->news as $newsItem)
                        <tr wire:key="news-{{ $newsItem->id }}" class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if ($newsItem->first_image)
                                            <img class="h-12 w-12 rounded-lg object-cover"
                                                src="{{ Storage::url($newsItem->first_image) }}"
                                                alt="{{ $newsItem->title }}">
                                        @else
                                            <div
                                                class="h-12 w-12 rounded-lg bg-blue-500 flex items-center justify-center text-white font-medium text-sm">
                                                {{ $newsItem->initials() }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ Str::limit($newsItem->title, 50) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $newsItem->excerpt }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $newsItem->author }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <flux:badge color="purple">
                                    {{ $newsItem->category->name }}
                                </flux:badge>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if ($newsItem->is_published)
                                        <flux:badge color="lime">
                                            {{ __('Published') }}
                                        </flux:badge>
                                    @else
                                        <flux:badge color="gray">
                                            {{ __('Draft') }}
                                        </flux:badge>
                                    @endif
                                    <button wire:click="togglePublished({{ $newsItem->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                        {{ $newsItem->is_published ? __('Unpublish') : __('Publish') }}
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <flux:tooltip :content="$newsItem->created_at->format('F j, Y \a\t g:i A')">
                                    {{ $newsItem->created_at->diffForHumans() }}
                                </flux:tooltip>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <flux:modal.trigger name="edit-news-{{ $newsItem->id }}">
                                        <flux:tooltip content="{{ __('Edit News') }}">
                                            <flux:button icon="pencil" />
                                        </flux:tooltip>
                                    </flux:modal.trigger>

                                    {{-- Edit News Modal --}}
                                    <flux:modal name="edit-news-{{ $newsItem->id }}" class="w-full max-w-4xl">
                                        <form wire:submit="updateNews({{ $newsItem->id }})">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">
                                                        {{ __('Edit News') }}
                                                    </flux:heading>
                                                    <flux:text class="mt-2">
                                                        {{ __('Update news information and images.') }}
                                                    </flux:text>
                                                </div>

                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                    <div class="space-y-4">
                                                        <flux:input wire:model="updateNewsForm.title"
                                                            :label="__('Title')" />

                                                        <flux:input wire:model="updateNewsForm.author"
                                                            :label="__('Author')" />

                                                        <flux:select wire:model="updateNewsForm.category_id"
                                                            :label="__('Category')">
                                                            <flux:select.option value="">
                                                                {{ __('Select Category') }}
                                                            </flux:select.option>
                                                            @foreach ($this->categories as $category)
                                                                <flux:select.option value="{{ $category->id }}">
                                                                    {{ $category->name }}
                                                                </flux:select.option>
                                                            @endforeach
                                                        </flux:select>
                                                    </div>

                                                    <div class="space-y-4">
                                                        <div>
                                                            <flux:label>{{ __('Current Images') }}</flux:label>
                                                            <div class="mt-2 grid grid-cols-3 gap-2">
                                                                @foreach ($newsItem->images as $index => $image)
                                                                    <div class="relative">
                                                                        <img src="{{ Storage::url($image) }}"
                                                                            class="w-full h-20 object-cover rounded">
                                                                        <button type="button"
                                                                            wire:click="removeExistingImage({{ $index }})"
                                                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                                                            ×
                                                                        </button>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <flux:label>{{ __('Add New Images') }}</flux:label>
                                                            <input type="file"
                                                                wire:model="updateNewsForm.new_images" multiple
                                                                accept="image/*"
                                                                class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <flux:textarea wire:model="updateNewsForm.paragraph"
                                                        :label="__('Content')" rows="6" />
                                                </div>

                                                <div class="flex gap-3 justify-end">
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">
                                                            {{ __('Cancel') }}
                                                        </flux:button>
                                                    </flux:modal.close>

                                                    <flux:button type="submit" wire:loading.attr="disabled"
                                                        wire:target="updateNews" variant="primary">
                                                        {{ __('Update News') }}
                                                    </flux:button>
                                                </div>
                                            </div>
                                        </form>
                                    </flux:modal>

                                    <flux:modal.trigger name="delete-news-{{ $newsItem->id }}">
                                        <flux:tooltip content="{{ __('Delete News') }}">
                                            <flux:button variant="danger" icon="trash" />
                                        </flux:tooltip>
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-news-{{ $newsItem->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Delete news?</flux:heading>
                                                <flux:text class="mt-2">
                                                    <p>You're about to delete the news "{{ $newsItem->title }}".</p>
                                                    <p>This action cannot be reversed.</p>
                                                </flux:text>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:spacer />
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Cancel</flux:button>
                                                </flux:modal.close>
                                                <flux:button type="submit" variant="danger"
                                                    wire:click="deleteNews({{ $newsItem->id }})">
                                                    Delete news
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    @if ($search)
                                        {{ __('No news found matching ":search"', ['search' => $search]) }}
                                    @else
                                        {{ __('No news found.') }}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($this->news->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $this->news->links() }}
            </div>
        @endif
    </div>

    {{-- Create News Modal --}}
    <flux:modal name="create-news" class="w-full max-w-4xl">
        <form wire:submit="storeNews">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('Create New News') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Add a new news article to the system.') }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <flux:input wire:model="title" :label="__('Title')" />

                        <flux:input wire:model="author" :label="__('Author')" />

                        <flux:select wire:model="category_id" :label="__('Category')">
                            <flux:select.option value="">{{ __('Select Category') }}</flux:select.option>
                            @foreach ($this->categories as $category)
                                <flux:select.option value="{{ $category->id }}">{{ $category->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <flux:label>{{ __('Images (minimum 3 required)') }}</flux:label>
                            <input type="file" wire:model="images" multiple accept="image/*"
                                class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <flux:text class="mt-2 text-xs text-gray-500">
                                {{ __('You must upload at least 3 images. Each image must be less than 2MB.') }}
                            </flux:text>
                        </div>

                        @if (!empty($images))
                            <div>
                                <flux:label>{{ __('Image Preview') }}</flux:label>
                                <div class="mt-2 grid grid-cols-3 gap-2">
                                    @foreach ($images as $image)
                                        <div class="relative">
                                            <img src="{{ $image->temporaryUrl() }}"
                                                class="w-full h-20 object-cover rounded">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <flux:textarea wire:model="paragraph" :label="__('Content')" rows="6" />
                </div>

                <div class="flex gap-3 justify-end">
                    <flux:button type="button" wire:click="closeModal" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" wire:loading.attr="disabled" wire:target="storeNews"
                        variant="primary">
                        <span wire:loading.remove wire:target="storeNews">
                            {{ __('Create News') }}
                        </span>
                        <span wire:loading wire:target="storeNews" class="flex items-center">
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
