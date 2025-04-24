<div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950 min-h-screen mt-7">
    <!-- Hero Section -->
    <div class="container max-w-screen-xl px-4 py-12 mx-auto lg:py-16">
        <div class="grid lg:grid-cols-12 gap-8 items-center mt-20">
            <div class="lg:col-span-7">
                <div class="mb-6">
                    <span class="inline-block px-3 py-1 text-sm font-medium text-indigo-700 bg-indigo-100 dark:bg-indigo-900 dark:text-indigo-300 rounded-full mb-3">Amanah Khairat Masyarakat</span>
                    <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">
                        Biro Khairat Kematian <span class="text-indigo-600 dark:text-indigo-400">Masjid Taman Sutera</span>
                    </h1>
                </div>
                <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl">
                    "Bersama Menghulurkan Kasih, Menyantuni yang Berduka."
                </p>
                <p class="text-base md:text-lg text-gray-500 dark:text-gray-400 mb-8 max-w-2xl">
                    Kami menyediakan perkhidmatan pengurusan kematian yang lengkap, pantas, dan penuh hormat untuk komuniti Taman Sutera.
                    Daftar sebagai ahli khairat dan mari kita saling membantu di saat-saat sukar.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-10">
                    <a wire:navigate href="/register" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 rounded-lg transition dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:focus:ring-indigo-800">
                        Mula Sekarang
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                    <a wire:navigate href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:text-white dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-700 transition">
                        Log Masuk
                    </a>
                </div>
            </div>
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full blur-lg opacity-60 dark:opacity-40"></div>
                    <div class="relative bg-white dark:bg-gray-800 rounded-full p-2">
                        <img src="{{ asset('images/icon2.gif') }}" alt="Icon GIF" class="w-full h-auto rounded-full">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Us Section -->
    <div class="container max-w-screen-xl px-4 py-12 mx-auto mt-32">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Tentang Kami</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Biro Khairat Kematian Masjid Taman Sutera merupakan institusi yang ditubuhkan untuk menyediakan bantuan dan perkhidmatan pengurusan kematian kepada komuniti Taman Sutera dan kawasan sekitarnya.
                </p>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Sejak ditubuhkan, institusi kami telah membantu ramai keluarga menguruskan hal ehwal kematian dengan pantas, efisien dan penuh penghormatan, mengikut ajaran Islam.
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    Kami percaya bahawa setiap individu berhak mendapat pengurusan jenazah yang sempurna dan keluarga yang ditinggalkan mendapat sokongan yang diperlukan pada masa-masa sukar.
                </p>
            </div>
            <div class="rounded-lg overflow-hidden shadow-lg">
                <img src="{{ asset('images/MTS02.jpg') }}" alt="Masjid Taman Sutera" class="w-full h-auto object-cover">
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container max-w-screen-xl px-4 py-12 mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Perkhidmatan Kami</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Komited memberikan sokongan menyeluruh ketika diperlukan</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Pengurusan Jenazah</h3>
                <p class="text-gray-600 dark:text-gray-400">Perkhidmatan lengkap untuk pengendalian jenazah dengan penuh hormat dan mengikut syariat Islam.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Khairat Kematian</h3>
                <p class="text-gray-600 dark:text-gray-400">Bantuan kewangan segera untuk meringankan beban keluarga yang berduka dalam masa yang sukar.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Perkhidmatan Sokongan</h3>
                <p class="text-gray-600 dark:text-gray-400">Bimbingan dan sokongan emosi untuk keluarga yang sedang berduka, termasuk nasihat dalam hal ehwal pengurusan pusaka.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Perlindungan Keluarga</h3>
                <p class="text-gray-600 dark:text-gray-400">Pelan perlindungan komprehensif untuk anda dan keluarga dengan yuran tahunan yang berpatutan.</p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-indigo-600 dark:bg-indigo-900">
        <div class="container max-w-screen-xl px-4 py-12 mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <!-- Stat 1 -->
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-white mb-2">500+</h3>
                    <p class="text-indigo-200">Ahli Berdaftar</p>
                </div>

                <!-- Stat 2 -->
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-white mb-2">10</h3>
                    <p class="text-indigo-200">Tahun Pengalaman</p>
                </div>

                <!-- Stat 3 -->
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-white mb-2">24/7</h3>
                    <p class="text-indigo-200">Perkhidmatan</p>
                </div>

                <!-- Stat 4 -->
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-white mb-2">100%</h3>
                    <p class="text-indigo-200">Kepuasan Ahli</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="container max-w-screen-xl px-4 py-16 mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Bagaimana Kami Beroperasi</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Proses mudah untuk mendapatkan perkhidmatan kami</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 relative">
                <div class="absolute -top-4 -left-4 w-12 h-12 bg-indigo-600 dark:bg-indigo-700 rounded-full flex items-center justify-center text-white font-bold text-xl">1</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Daftar Sebagai Ahli</h3>
                <p class="text-gray-600 dark:text-gray-400">Lengkapkan pendaftaran anda dengan mengisi borang dalam talian atau mengunjungi pejabat kami. Bayar yuran tahunan yang berpatutan.</p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 relative">
                <div class="absolute -top-4 -left-4 w-12 h-12 bg-indigo-600 dark:bg-indigo-700 rounded-full flex items-center justify-center text-white font-bold text-xl">2</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Aktifkan Keahlian</h3>
                <p class="text-gray-600 dark:text-gray-400">Setelah pendaftaran disahkan, anda dan keluarga akan dilindungi oleh khairat kematian kami dengan segera.</p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 relative">
                <div class="absolute -top-4 -left-4 w-12 h-12 bg-indigo-600 dark:bg-indigo-700 rounded-full flex items-center justify-center text-white font-bold text-xl">3</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 mt-2">Dapatkan Bantuan</h3>
                <p class="text-gray-600 dark:text-gray-400">Pada masa yang diperlukan, hubungi kami 24/7 untuk mendapatkan bantuan pengurusan jenazah dan bantuan kewangan segera.</p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="container max-w-screen-xl px-4 py-16 mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="grid md:grid-cols-2 items-center">
                <div class="p-8 md:p-12">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4">Sertai Kami Hari Ini</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Dengan yuran tahunan yang berpatutan, anda dan keluarga akan mendapat perlindungan khairat kematian yang komprehensif. Bersama-sama kita memastikan perjalanan akhir ahli keluarga kita diuruskan dengan baik.</p>
                    <div class="flex space-x-4">
                        <a wire:navigate href="/register" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 rounded-lg transition dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:focus:ring-indigo-800">
                            Daftar Sekarang
                        </a>
                        <a href="#" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-indigo-600 bg-transparent hover:bg-indigo-50 focus:ring-4 focus:ring-indigo-300 rounded-lg border border-indigo-600 transition dark:text-indigo-400 dark:border-indigo-400 dark:hover:bg-gray-700 dark:focus:ring-indigo-800">
                            Ketahui Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="hidden md:block relative overflow-hidden rounded-lg shadow-xl">
                    <img src="{{ asset('images/masjid.png') }}" alt="Masjid Taman Sutera" class="w-full h-64 md:h-80 lg:h-96 object-cover object-center transform hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="container max-w-screen-xl px-4 py-16 mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Soalan Lazim</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Jawapan kepada pertanyaan umum mengenai khairat kematian</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-6">
            <!-- FAQ Item 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Siapa yang layak menjadi ahli?</h3>
                <p class="text-gray-600 dark:text-gray-400">Semua penduduk Taman Sutera dan kawasan sekitarnya yang beragama Islam boleh menjadi ahli.</p>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Berapa bayaran pendaftran untuk menjadi ahli?</h3>
                <p class="text-gray-600 dark:text-gray-400">bayaran pendaftaran adalah RM100 bagi setiap keluarga, meliputi ahli utama dan tanggungan.</p>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Bagaimana cara untuk menghubungi pihak khairat jika berlaku kematian?</h3>
                <p class="text-gray-600 dark:text-gray-400">Ahli boleh menghubungi talian kecemasan kami di 03-8735 4225 yang beroperasi 24 jam sehari, 7 hari seminggu.</p>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Apakah bantuan yang disediakan oleh khairat kematian?</h3>
                <p class="text-gray-600 dark:text-gray-400">Bantuan termasuk pengurusan jenazah lengkap, kain kafan, keranda, pengangkutan jenazah, dan bantuan kewangan segera sebanyak RM1,050 kepada keluarga si mati.</p>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="bg-gray-50 dark:bg-gray-900">
        <div class="container max-w-screen-xl px-4 py-12 mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Hubungi Kami</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">Ada soalan? Jangan ragu untuk menghubungi kami. Tim kami sentiasa bersedia membantu anda.</p>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Telefon</h3>
                                <p class="text-gray-600 dark:text-gray-400">03-8735 4225</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Email</h3>
                                <p class="text-gray-600 dark:text-gray-400">info@khairatsutera.com</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Alamat</h3>
                                <p class="text-gray-600 dark:text-gray-400">Masjid Taman Sutera, Jalan Sutera 1, Taman Sutera, 43000 Kajang, Selangor</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg overflow-hidden shadow-md h-64 md:h-80">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.320862194591!2d101.76243772497052!3d3.0080975469678752!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdcb7c3608a551%3A0x6d42402a443678cf!2sMasjid%20Taman%20Sutera!5e0!3m2!1sen!2smy!4v1744843068824!5m2!1sen!2smy"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Location of Masjid Taman Sutera"
                        class="w-full h-full">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->

</div>
