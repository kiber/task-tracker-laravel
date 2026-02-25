@props([
    'completed' => false,
])

@php
    $badgeClass = $completed
        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
    {{ $completed ? __('Completed') : __('Incomplete') }}
</span>
