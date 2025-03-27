<div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
    <div class="mb-6">
        {{ $logo }}
    </div>
    <div class="w-full sm:max-w-2xl px-6 py-8 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        {{ $slot }}
    </div>
</div>
