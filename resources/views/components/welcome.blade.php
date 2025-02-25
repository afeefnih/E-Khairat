

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                   <!-- Main Content -->
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 space-y-8">
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <!-- Welcome Section -->
    <div class="bg-gray-800 p-6 rounded-lg flex flex-wrap items-center gap-6">
        <div class="w-20 h-20 bg-gray-600 rounded-full flex items-center justify-center">
            <span class="text-white text-xl font-bold uppercase">{{ substr(auth()->user()->name, 0, 2) }}</span>
        </div>
        <div class="flex-grow">
            <h2 class="text-white text-2xl font-bold">{{ auth()->user()->name }}</h2>
            <div class="flex gap-2 mt-2">
                <span class="px-3 py-1 bg-green-500 text-white text-xs rounded">Customer</span>
                <span class="px-3 py-1 bg-yellow-500 text-white text-xs rounded">Premium</span>
            </div>
            <p class="text-gray-300 mt-2 text-sm">Selamat datang ke papan dashboard peribadi anda!</p>
        </div>

    </div>

    <!-- Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 bg-gray-900 text-white rounded-lg shadow">
            <h3 class="text-gray-400 text-sm font-medium">Jumlah Bayaran (RM)</h3>
            <p class="text-white text-3xl font-semibold mt-4">100</p>
        </div>
        <div class="p-6 bg-gray-900 text-white rounded-lg shadow">
            <h3 class="text-gray-400 text-sm font-medium">Jumlah Tunggakan (RM)</h3>
            <p class="text-white text-3xl font-semibold mt-4">00</p>
        </div>
    </div>

    <!-- Recent Payments Section -->
    <div>
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">Recent Payments</h3>
        <div class="mt-4">
            <table class="w-full bg-gray-800 text-gray-200 rounded-lg shadow overflow-hidden">
                <thead class="bg-gray-700 text-gray-400">
                    <tr>
                        <th class="py-3 px-4 text-left">Yuran</th>
                        <th class="py-3 px-4 text-left">Jumlah (RM)</th>
                        <th class="py-3 px-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr>
                        <td class="py-3 px-4">Pendaftaran</td>
                        <td class="py-3 px-4">100</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-green-500 text-white rounded-md">Successful</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4">Kutipan 2025</td>
                        <td class="py-3 px-4">50</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-green-500 text-white rounded-md">Successful</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

