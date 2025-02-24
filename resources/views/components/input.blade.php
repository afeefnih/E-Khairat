@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark: dark:bg-gray-700 dark:text-white dark:border-gray-600focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
