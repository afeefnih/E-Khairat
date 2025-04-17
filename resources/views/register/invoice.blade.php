<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 py-8">
        <div class="container max-w-screen-xl mx-auto px-4">
            <!-- Page Header -->
            <div class="text-center mb-8 mt-10">
                <div class="flex justify-center mx-auto mb-4">
                    <a wire:navigate href="/">
                        <img src="{{asset ('images/logo.png')}}" alt="Logo" class="h-24 w-24 object-contain">
                    </a>
                </div>
    
                <h1 class="mt-4 text-3xl md:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Invois dan Pembayaran
                </h1>
                <p class="mt-3 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Semak dan sahkan maklumat pendaftaran anda sebelum membuat pembayaran
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
