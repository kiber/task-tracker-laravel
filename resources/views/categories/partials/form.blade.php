@php
    $category ??= null;
@endphp

<div>
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input
        id="name"
        class="block mt-1 w-full"
        type="text"
        name="name"
        :value="old('name', $category['name'])"
        required
        autofocus
        autocomplete="off"
    />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

{{--<div>--}}
{{--    <x-input-label for="description" :value="__('Description')" />--}}
{{--    <textarea--}}
{{--        id="description"--}}
{{--        name="description"--}}
{{--        rows="4"--}}
{{--        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"--}}
{{--    >{{ old('description', $category?->description) }}</textarea>--}}
{{--    <x-input-error :messages="$errors->get('description')" class="mt-2" />--}}
{{--</div>--}}
