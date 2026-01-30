<x-layouts>
    <x-slot:heading>
        Notifications
    </x-slot:heading>

    <div class="max-w-4xl mx-auto">
        <!-- Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <div>
                <p class="text-gray-600 dark:text-gray-400">
                    Stay updated with your account activities and course enrollments.
                </p>
                @if($unreadCount > 0)
                    <p class="text-sm text-indigo-600 dark:text-indigo-400 mt-1">
                        You have {{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}.
                    </p>
                @endif
            </div>

            <div class="flex gap-2">
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors">
                            Mark all as read
                        </button>
                    </form>
                @endif
                @if($notifications->where('is_read', true)->count() > 0)
                    <form action="{{ route('notifications.clearRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            Clear read
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
                   class="@if($filter === 'all') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    All Notifications
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                   class="@if($filter === 'unread') border-indigo-500 text-indigo-600 dark:text-indigo-400 @else border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 @endif whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    Unread
                    @if($unreadCount > 0)
                        <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            </nav>
        </div>

        @if($notifications->isEmpty())
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($filter === 'unread')
                        You're all caught up! No unread notifications.
                    @else
                        You don't have any notifications yet.
                    @endif
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($notifications as $notification)
                    <div class="@if(!$notification->is_read) bg-indigo-50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 @else bg-white dark:bg-gray-800 @endif rounded-lg shadow-sm p-4 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                @php
                                    $iconColors = [
                                        'success' => 'bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400',
                                        'warning' => 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400',
                                        'error' => 'bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400',
                                        'info' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400',
                                    ];
                                    $iconColor = $iconColors[$notification->type] ?? $iconColors['info'];
                                @endphp
                                <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $iconColor }}">
                                    @if($notification->type === 'success')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @elseif($notification->type === 'warning')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    @elseif($notification->type === 'error')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $notification->title }}
                                        </p>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $notification->message }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                                        @if(!$notification->is_read)
                                            <span class="h-2 w-2 bg-indigo-500 rounded-full"></span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>

                                    <div class="flex items-center space-x-2">
                                        @if($notification->action_url)
                                            <form action="{{ route('notifications.read', $notification) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                                                    {{ $notification->action_text ?? 'View' }}
                                                </button>
                                            </form>
                                        @elseif(!$notification->is_read)
                                            <form action="{{ route('notifications.read', $notification) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                                    Mark as read
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-gray-400 hover:text-red-500 dark:hover:text-red-400">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts>
