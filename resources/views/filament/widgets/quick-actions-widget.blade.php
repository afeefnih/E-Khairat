<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-wrap gap-4 justify-between">
            <a href="{{ route('filament.admin.resources.users.create') }}" class="flex items-center gap-3 p-4 flex-1 min-w-[180px] bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 hover:border-primary-100 transition duration-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:border-primary-800">
                <div class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 bg-primary-100 rounded-lg dark:bg-primary-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-medium">Daftar Ahli</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Register new member</span>
                </div>
            </a>

            <a href="{{ route('filament.admin.resources.payments.create') }}" class="flex items-center gap-3 p-4 flex-1 min-w-[180px] bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 hover:border-success-100 transition duration-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:border-success-800">
                <div class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 bg-success-100 rounded-lg dark:bg-success-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-medium">Rekod Bayaran</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Record payment</span>
                </div>
            </a>

            <a href="{{ route('filament.admin.resources.death-records.create') }}" class="flex items-center gap-3 p-4 flex-1 min-w-[180px] bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 hover:border-danger-100 transition duration-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:border-danger-800">
                <div class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 bg-danger-100 rounded-lg dark:bg-danger-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-medium">Rekod Kematian</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Record death</span>
                </div>
            </a>

            <a href="{{ route('filament.admin.resources.payment-categories.create') }}" class="flex items-center gap-3 p-4 flex-1 min-w-[180px] bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 hover:border-warning-100 transition duration-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:border-warning-800">
                <div class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 bg-warning-100 rounded-lg dark:bg-warning-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-warning-600 dark:text-warning-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <span class="block text-sm font-medium">Cipta Kutipan</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Create collection</span>
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
