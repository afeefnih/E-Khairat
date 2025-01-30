
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
                <div class="p-8 text-gray-900 dark:text-gray-100">

                    <!-- Success or Error Messages -->
                    @if (session('success'))
                        <div class="p-4 mb-6 bg-green-100 text-green-800 rounded-lg shadow">
                            {{ session('success') }}
                        </div>
                    @elseif (session('error'))
                        <div class="p-4 mb-6 bg-red-100 text-red-800 rounded-lg shadow">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Content Section -->
                    <div class="flex flex-col md:flex-row items-center gap-10">
                        <!-- Left Content -->
                        <div class="flex-1">
                            <h1 class="text-3xl md:text-4xl font-bold mb-6">
                                Infaq Badan Khairat Kebajikan
                            </h1>
                            <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                                Your generous donation helps us in achieving our goals and supporting the community.
                            </p>

                            <!-- Donation Form -->
                            <form method="POST" action="#">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-lg font-medium mb-2">
                                        Select Amount:
                                    </label>
                                    <div class="flex flex-wrap gap-4">
                                        @foreach ([10, 20, 50, 100] as $amount)
                                            <label class="flex items-center gap-2">
                                                <input type="radio" id= "amount" name="amount" value="{{ $amount }}" required class="accent-black">
                                                <span class="text-lg">RM {{ $amount }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex gap-4">
                                    <a href="{{ route('home') }}" class="py-2 px-6 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</a>
                                    <button type="submit" class="py-2 px-6 bg-black text-white rounded-md hover:bg-gray-900">
                                        Donate Now
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Right Content (Image) -->
                        <div class="flex-1 flex justify-center items-center">
                            <div class="w-[300px] h-[300px] bg-gray-100 dark:bg-gray-700 flex justify-center items-center rounded-md shadow-md">
                                <img class="max-w-full max-h-full" src="https://via.placeholder.com/300x300" alt="Placeholder Image">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

