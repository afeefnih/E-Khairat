<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 py-12 flex items-center justify-center">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo centered at the top -->
            <div class="text-center mb-8">
                <div class="mx-auto w-24 h-24 bg-white dark:bg-gray-800 rounded-full p-1 shadow-lg flex items-center justify-center">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
                    </a>
                </div>
                <h2 class="mt-4 text-3xl font-extrabold text-gray-900 dark:text-white">
                    Lupa Kata Laluan
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Masukkan alamat emel anda dan kami akan menghantar pautan untuk menetapkan semula kata laluan anda.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-xl sm:rounded-xl sm:px-10">
                <x-validation-errors class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 p-4 text-red-600 dark:text-red-400" />

                @session('status')
                    <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-sm font-medium">
                        {{ $value }}
                    </div>
                @endsession

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-label for="email" value="{{ __('Emel') }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <x-input id="email" class="block w-full border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 rounded-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan alamat emel anda" />
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('login') }}" class="block mb-4 text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-center">
                            Kembali ke Log Masuk
                        </a>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200">
                            Hantar Pautan Reset Kata Laluan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
