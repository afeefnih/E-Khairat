
<div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 min-h-screen py-12 mt-10">
    <div class="container max-w-screen-xl px-4 mx-auto">

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-md mb-6 relative dark:bg-green-900/30 dark:border-green-700 dark:text-green-300" role="alert">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-bold">Berjaya!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @elseif (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-md mb-6 relative dark:bg-red-900/30 dark:border-red-700 dark:text-red-300" role="alert">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-bold">Gagal!</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 py-6 px-8">
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Infaq Tabung Khairat Kematian
                </h1>
                <p class="text-indigo-100 text-sm md:text-base mt-2">
                    "Sumbangan Anda, Amal Jariah Kita Bersama"
                </p>
            </div>

            <div class="p-6 md:p-8">
                <div class="md:flex md:items-center md:gap-10">
                    <div class="md:w-1/2">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">
                                Menyumbang Untuk Kebajikan
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                Sumbangan anda sangat berharga dan membantu kami mencapai matlamat serta menyokong komuniti kita. Setiap sumbangan, besar atau kecil, sangat kami hargai.
                            </p>
                            <div class="bg-indigo-50 dark:bg-indigo-900/30 border-l-4 border-indigo-500 p-4 mb-6 rounded-r-md">
                                <p class="text-indigo-700 dark:text-indigo-300 text-sm">
                                    Semua sumbangan akan digunakan untuk membiayai:
                                </p>
                                <ul class="list-disc ml-5 mt-2 text-indigo-600 dark:text-indigo-400 text-sm">
                                    <li>Bantuan kewangan kepada keluarga yang berduka</li>
                                    <li>Pengurusan jenazah ahli khairat</li>
                                    <li>Program kebajikan masyarakat setempat</li>
                                </ul>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('infaq.store') }}" class="space-y-6">
                            @csrf
                            <div>
                                <label for="amount" class="block text-lg font-medium mb-3 text-gray-900 dark:text-white">
                                    Pilih Jumlah Sumbangan (RM):
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach ([10, 20, 50, 100] as $amount)
                                        <label class="relative">
                                            <input type="radio" id="amount-{{ $amount }}" name="amount" value="{{ $amount }}" required
                                                   class="peer absolute h-0 w-0 opacity-0">
                                            <div class="bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600
                                                        hover:border-indigo-500 dark:hover:border-indigo-400 rounded-lg p-4 text-center
                                                        cursor-pointer transition-all duration-200
                                                        peer-checked:border-indigo-500 dark:peer-checked:border-indigo-400
                                                        peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30">
                                                <span class="font-bold text-xl text-gray-900 dark:text-white">RM {{ $amount }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    <label class="flex items-center space-x-3">
                                        <input type="radio" id="amount-other" name="amount" value="other"
                                               class="w-5 h-5 accent-indigo-600 dark:accent-indigo-400" onclick="toggleOtherAmountInput()">
                                        <span class="text-lg text-gray-900 dark:text-white">Jumlah Lain:</span>
                                    </label>
                                    <div id="other_amount_container" class="hidden mt-3 pl-8">
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-900 dark:text-white">
                                                RM
                                            </span>
                                            <input type="number" name="other_amount" id="other_amount" min="1" step="any"
                                                  class="pl-10 block w-full md:w-1/2 rounded-md border border-gray-300 dark:border-gray-600
                                                         py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                                                         focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                                                  placeholder="Masukkan jumlah">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 mt-6">
                                <a href="{{ route('home') }}"
                                   class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:text-white dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 rounded-lg transition dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:focus:ring-indigo-800">
                                    Infaq Sekarang
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="md:w-1/2 mt-10 md:mt-0 flex justify-center items-center">
                        <div class="relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg blur-lg opacity-60 dark:opacity-40"></div>
                            <div class="relative bg-white dark:bg-gray-800 rounded-lg p-2 shadow-xl flex justify-center">
                                <img class="rounded-lg w-full h-auto max-w-md " src="{{ asset('images/QR_Code.PNG') }}" alt="Khairat Donation Image">
                                <div class="absolute bottom-4 left-4 right-4 bg-indigo-600/90 dark:bg-indigo-900/90 text-white p-3 rounded">
                                    <p class="text-center text-sm md:text-base font-medium">
                                        "Sebaik-baik manusia adalah yang paling bermanfaat bagi manusia lain"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Maklumat Tambahan
            </h3>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-indigo-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="font-bold text-indigo-700 dark:text-indigo-300 mb-2">Kaedah Pembayaran</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Pembayaran diproses melalui ToyyibPay. Anda boleh membayar menggunakan pelbagai bank tempatan.</p>
                </div>
                <div class="bg-indigo-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="font-bold text-indigo-700 dark:text-indigo-300 mb-2">Pembayaran Selamat</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Semua transaksi dibuat melalui platform pembayaran yang selamat dan disulitkan.</p>
                </div>
                <div class="bg-indigo-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="font-bold text-indigo-700 dark:text-indigo-300 mb-2">Hubungi Kami</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Sebarang pertanyaan, sila hubungi kami di 03-8735 4225 atau email ke info@khairatsutera.com</p>
                </div>
            </div>
        </div>

    </div>

</div>



<script>
    function toggleOtherAmountInput() {
        const otherAmountRadio = document.getElementById('amount-other');
        const otherAmountContainer = document.getElementById('other_amount_container');
        const otherAmountInput = document.getElementById('other_amount');

        if (otherAmountRadio.checked) {
            otherAmountContainer.classList.remove('hidden');
            otherAmountInput.focus();
        } else {
            otherAmountContainer.classList.add('hidden');
        }
    }
</script>
