@if (count($tasks) > 0)
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach ($tasks as $task)
            <li class="p-4 flex items-start justify-between gap-4 group" data-task-item data-completed="{{ $task['completed_at'] ? 'true' : 'false' }}">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 group-data-[completed=true]:line-through">{{ $task['title'] }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 group-data-[completed=true]:line-through">
                        {{ $task['category']['name'] ?? __('Uncategorized') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 group-data-[completed=true]:line-through">
                        {{ \Illuminate\Support\Str::limit($task['description'] ?? '', 120) ?: __('No description') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <x-tasks.status-badge :completed="$task['is_completed']" />
                    <button
                        type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full border transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 {{ $task['is_completed'] ? 'border-green-600 bg-green-100 text-green-700 dark:border-green-400 dark:bg-green-900/30 dark:text-green-300' : 'border-gray-300 bg-white text-gray-400 hover:bg-gray-50 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700' }}"
                        aria-label="{{ $task['is_completed'] ? __('Mark task as incomplete') : __('Mark task as completed') }}"
                        title="{{ $task['is_completed'] ? __('Mark Incomplete') : __('Mark Complete') }}"
                        data-task-toggle
                        data-task-id="{{ $task['id'] }}"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.2 7.2a1 1 0 01-1.417 0l-3.2-3.2a1 1 0 111.414-1.42l2.493 2.494 6.493-6.494a1 1 0 011.417 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <div class="p-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $emptyMessage }}</p>
        <a
            href="{{ route('tasks.create') }}"
            class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        >
            {{ __('Add a task') }}
        </a>
    </div>
@endif
