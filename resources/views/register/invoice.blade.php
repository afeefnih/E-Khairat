<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 py-8 mt-10">
        <div class="container max-w-screen-xl mx-auto px-4">
            <!-- Page Header -->
            <div class="text-center mb-10">
                <div class="inline-block p-2 bg-white dark:bg-gray-800 rounded-full shadow-md mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-16 h-16 object-contain">
                </div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Pendaftaran Ahli Khairat
                </h1>
                <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                    Sila lengkapkan maklumat anda untuk mendaftar
                </p>


                <!-- Progress Bar -->
                <div class="max-w-3xl mx-auto mt-8 px-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Langkah 3 dari 3</span>
                        <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Invois & Pembayaran</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <livewire:invoice-and-payment />
        </div>
    </div>
</x-guest-layout>
