<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 w-full">
        <div class="overflow-hidden shadow-lg rounded-lg bg-white dark:bg-gray-900 p-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @elseif (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="md:flex md:items-center gap-10">

                <div class="md:w-1/2">
                    <h1 class="text-3xl font-bold mb-4 md:text-4xl dark:text-white">
                        Infaq Badan Khairat Kebajikan
                    </h1>
                    <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                        Sumbangan anda sangat berharga dan membantu kami mencapai matlamat serta menyokong komuniti kita. Setiap sumbangan, besar atau kecil, sangat kami hargai.
                    </p>

                    <form method="POST" action="#">  @csrf
                        <div class="mb-4">
                            <label for="amount" class="block text-lg font-medium mb-2 dark:text-white">
                                Pilih Jumlah (RM):
                            </label>
                            <div class="flex flex-wrap gap-4">
                                @foreach ([10, 20, 50, 100] as $amount)
                                    <label class="flex items-center gap-2">
                                        <input type="radio" id="amount-{{ $amount }}" name="amount" value="{{ $amount }}" required class="accent-blue-500 dark:accent-blue-400">
                                        <span class="text-lg dark:text-white">RM {{ $amount }}</span>
                                    </label>
                                @endforeach
                                <label class="flex items-center gap-2">
                                    <input type="radio" id="amount-other" name="amount" value="other" class="accent-blue-500 dark:accent-blue-400"  onclick="toggleOtherAmountInput()">
                                    <span class="text-lg dark:text-white">Jumlah Lain:</span>
                                    <input type="number" name="other_amount" id="other_amount" min="1" step="any" class="border border-gray-300 rounded px-2 py-1 w-24 dark:bg-gray-700 dark:border-gray-600 dark:text-white hidden" placeholder="Enter Amount">
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <a href="{{ route('home') }}" class="py-2 px-6 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">Batal</a>
                            <button type="submit" class="py-2 px-6 bg-blue-700 text-white rounded-md hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700">
                                Infaq Sekarang
                            </button>
                        </div>
                    </form>
                </div>

                <div class="md:w-1/2 flex justify-center items-center">
                    <div class="w-full h-auto md:w-[300px] md:h-[300px] bg-gray-100 dark:bg-gray-700 flex justify-center items-center rounded-md shadow-md overflow-hidden">
                        <img class="max-w-full max-h-full object-cover" src="https://via.placeholder.com/300x300" alt="Donation Image">
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    function toggleOtherAmountInput() {
        const otherAmountRadio = document.getElementById('amount-other');
        const otherAmountInput = document.getElementById('other_amount');

        if (otherAmountRadio.checked) {
            otherAmountInput.classList.remove('hidden');
        } else {
            otherAmountInput.classList.add('hidden');
        }
    }
</script>
