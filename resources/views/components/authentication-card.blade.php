<div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-xl mt-6 px-10 py-8 bg-white dark:bg-gray-800 shadow-lg overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
