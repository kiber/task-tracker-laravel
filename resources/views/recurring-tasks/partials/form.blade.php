@php
    $recurringTask ??= null;
    $selectedFrequency = old('frequency', $recurringTask['frequency'] ?? \App\Enums\TaskFrequency::Daily->value);
    $selectedWeekdays = old('weekly_days', $recurringTask['weekly_days'] ?? []);
@endphp

<div x-data="{ frequency: '{{ $selectedFrequency }}' }" class="space-y-6">
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input
            id="title"
            class="block mt-1 w-full"
            type="text"
            name="title"
            :value="old('title', $recurringTask['title'] ?? '')"
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
                <option value="{{ $categoryId }}" @selected((string) old('category_id', $recurringTask['category']['id'] ?? '') === (string) $categoryId)>
                    {{ $categoryName }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="frequency" :value="__('Frequency')" />
        <select
            id="frequency"
            name="frequency"
            x-model="frequency"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            required
        >
            @foreach ($frequencies as $frequencyValue => $frequencyLabel)
                <option value="{{ $frequencyValue }}">{{ $frequencyLabel }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
    </div>

    <div x-show="frequency === '{{ \App\Enums\TaskFrequency::Weekly->value }}'" class="space-y-2">
        <x-input-label :value="__('Days of Week')" />
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ($weekdays as $dayValue => $dayLabel)
                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input
                        type="checkbox"
                        name="weekly_days[]"
                        value="{{ $dayValue }}"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-indigo-500 dark:focus:ring-indigo-600"
                        @checked(in_array($dayValue, (array) $selectedWeekdays, true))
                    />
                    <span>{{ $dayLabel }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('weekly_days')" class="mt-2" />
        <x-input-error :messages="$errors->get('weekly_days.*')" class="mt-2" />
    </div>

    <div x-show="frequency === '{{ \App\Enums\TaskFrequency::Monthly->value }}'">
        <x-input-label for="monthly_day" :value="__('Day of Month (1-31)')" />
        <x-text-input
            id="monthly_day"
            class="block mt-1 w-full"
            type="number"
            min="1"
            max="31"
            name="monthly_day"
            :value="old('monthly_day', $recurringTask['monthly_day'] ?? '')"
        />
        <x-input-error :messages="$errors->get('monthly_day')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="start_date" :value="__('Start Date (optional)')" />
            <x-text-input
                id="start_date"
                class="block mt-1 w-full"
                type="date"
                name="start_date"
                :value="old('start_date', $recurringTask['start_date'] ?? '')"
            />
            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="end_date" :value="__('End Date (optional)')" />
            <x-text-input
                id="end_date"
                class="block mt-1 w-full"
                type="date"
                name="end_date"
                :value="old('end_date', $recurringTask['end_date'] ?? '')"
            />
            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea
            id="description"
            name="description"
            rows="4"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        >{{ old('description', $recurringTask['description'] ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
</div>
