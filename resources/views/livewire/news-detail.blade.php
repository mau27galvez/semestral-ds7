<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Back button --}}
        <div class="mb-6">
            <a href="{{ route('home') }}"
                class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Home
            </a>
        </div>

        {{-- Article header --}}
        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            {{-- Hero image --}}
            @if ($news->first_image)
                <div class="h-64 md:h-96 bg-gray-200 overflow-hidden">
                    <img src="{{ Storage::url($news->first_image) }}" alt="{{ $news->title }}"
                        class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-6 md:p-8">
                {{-- Category and date --}}
                <div class="flex items-center mb-4">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $news->category->name }}
                    </span>
                    <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">
                        {{ $news->created_at->format('F j, Y') }}
                    </span>
                </div>

                {{-- Title --}}
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                    {{ $news->title }}
                </h1>

                {{-- Author and stats --}}
                <div class="flex items-center justify-between mb-6 border-b border-gray-200 dark:border-gray-700 pb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div
                                class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                {{ $news->author->initials() }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $news->author->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $news->author->getRoleDisplayName() }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $news->views_count }} {{ Str::plural('view', $news->views_count) }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $this->comments->count() }} {{ Str::plural('comment', $this->comments->count()) }}
                        </div>
                    </div>
                </div>

                {{-- Article content --}}
                <div class="prose prose-lg max-w-none dark:prose-invert">
                    {!! nl2br(e($news->paragraph)) !!}
                </div>

                {{-- Additional images --}}
                @if (count($news->images) > 1)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gallery</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach (array_slice($news->images, 1) as $image)
                                <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                                    <img src="{{ Storage::url($image) }}" alt="{{ $news->title }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Like button --}}
                <div class="mt-8 flex items-center space-x-4">
                    @auth
                        <button wire:click="toggleLike"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md {{ $this->isLiked ? 'text-red-600 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800' : 'text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2 {{ $this->isLiked ? 'fill-current' : '' }}"
                                fill="{{ $this->isLiked ? 'currentColor' : 'none' }}" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            {{ $this->isLiked ? 'Liked' : 'Like' }}
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                            Like
                        </a>
                    @endauth

                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->likesCount }} {{ Str::plural('like', $this->likesCount) }}
                    </span>
                </div>
            </div>
        </article>

        {{-- Comments section --}}
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    Comments ({{ $this->comments->count() }})
                </h2>

                {{-- Add comment form --}}
                @if ($news->comments_enabled)
                    @auth
                        <form wire:submit="addComment" class="mb-8">
                            <div class="mb-4">
                                <label for="comment"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Add a comment
                                </label>
                                <textarea wire:model="commentContent" id="comment" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Share your thoughts..." maxlength="1000"></textarea>
                                @error('commentContent')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Post Comment
                            </button>
                        </form>
                    @else
                        <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-gray-600 dark:text-gray-300">
                                <a href="{{ route('login') }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400">Sign in</a>
                                or
                                <a href="{{ route('register') }}"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400">register</a>
                                to leave a comment.
                            </p>
                        </div>
                    @endauth
                @else
                    <div class="mb-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <p class="text-yellow-800 dark:text-yellow-200">
                            Comments are disabled for this article.
                        </p>
                    </div>
                @endif

                {{-- Comments list --}}
                @if ($this->comments->count() > 0)
                    <div class="space-y-6">
                        @foreach ($this->comments as $comment)
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium text-sm">
                                        {{ $comment->user->initials() }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm">
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                        <span
                                            class="text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>

                                        {{-- Moderation controls --}}
                                        @auth
                                            @if (auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                                                <button wire:click="toggleCommentStatus({{ $comment->id }})"
                                                    class="ml-2 text-xs text-red-600 hover:text-red-800 dark:text-red-400">
                                                    {{ $comment->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            @endif
                                        @endauth
                                    </div>
                                    <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $comment->content }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No comments yet. Be the first to
                            comment!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif
</div>
