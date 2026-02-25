@php
    $task ??= null;
    $taskDate = isset($task['task_date']) ? date('Y-m-d', strtotime($task['task_date'])) : '';
    $taskDate = old('task_date', $taskDate);
@endphp

<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input
        id="title"
        class="block mt-1 w-full"
        type="text"
        name="title"
        :value="old('title', $task['title'] ?? '')"
        required
        autofocus
        autocomplete="off"
    />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div>
    <x-input-label for="category_id" :value="__('Category')" />
    <select
        id="category_id"
        name="category_id"
        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    >
        <option value="">{{ __('No category') }}</option>
        @foreach ($categories as $categoryId => $categoryName)
            <option value="{{ $categoryId }}" @selected((string) old('category_id', $task['category']['id'] ?? '') === (string) $categoryId)>
                {{ $categoryName }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="task_date" :value="__('Due Date')" />
    <x-text-input
        id="task_date"
        class="block mt-1 w-full"
        type="date"
        name="task_date"
        :value="$taskDate"
    />
    <x-input-error :messages="$errors->get('task_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" :value="__('Description')" />
    <textarea
        id="description"
        name="description"
        rows="4"
        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    >{{ old('description', $task['description'] ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
