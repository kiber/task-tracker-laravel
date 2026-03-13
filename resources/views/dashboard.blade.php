<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Keep up with overdue items and focus on today.') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('tasks.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    {{ __('All Tasks') }}
                </a>
                <a
                    href="{{ route('tasks.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    {{ __('New Task') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ __('Overdue') }}</p>
                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
                            {{ __('Needs action') }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['overdue'] }}</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Tasks due before today') }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ __('Completed Today') }}</p>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            {{ $dateLabels['today'] }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['completed_today'] }}</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Closed out since midnight') }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ __('Last 7 Days') }}</p>
                        <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ __('Completed') }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['completed_last_7_days'] }}</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('All completed tasks') }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ __('Due Today') }}</p>
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                            {{ __('Focus') }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['due_today'] }}</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Tasks scheduled for today') }}</p>
                    </div>
                </div>
            </div>

            @if (count($recentOverdueTasks) > 0)
                <section class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-rose-100 dark:border-rose-900/40 overflow-hidden">
                    <div class="p-6 border-b border-rose-100 dark:border-rose-900/40">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Overdue Tasks') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('From') }} {{ $dateLabels['two_days_ago'] }} {{ __('to') }} {{ $dateLabels['yesterday'] }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">
                                {{ __('Complete these first') }}
                            </span>
                        </div>
                    </div>

                    @if (!empty($recentOverdueTasks))
                    <x-dashboard.task-list
                        :title="__('Upcoming tasks')"
                        :tasks="$recentOverdueTasks"
                        :emptyMessage="__('No tasks scheduled for today.')"
                    />
                    @endif
                </section>
            @endif

            <section class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Today') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $dateLabels['today'] }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                            {{ $stats['due_today'] }} {{ __('Tasks') }}
                        </span>
                    </div>
                </div>

                <x-dashboard.task-list
                    :title="__('Upcoming tasks')"
                    :tasks="$todayTasks"
                    :emptyMessage="__('No tasks scheduled for today.')"
                />
            </section>
        </div>
    </div>
</x-app-layout>
