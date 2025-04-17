{{-- resources/views/livewire/terms-and-conditions.blade.php --}}

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-indigo-950">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <header class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-12 md:px-12 text-center">
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-white mb-2">
                    {{ $language === 'ms' ? 'Terma dan Syarat BKKMTS' : 'BKKMTS Terms and Conditions' }}
                </h1>
                <p class="text-blue-100 mb-8">
                    {{ $language === 'ms' ? 'Sila baca dengan teliti terma dan syarat keahlian' : 'Please read membership terms and conditions carefully' }}
                </p>
                <button wire:click="toggleLanguage"
                    class="bg-white text-blue-800 hover:bg-blue-50 font-semibold px-4 py-2 rounded-md shadow-sm transition duration-200">
                    {{ $language === 'ms' ? 'English' : 'Bahasa Melayu' }}
                </button>
            </div>
        </header>

        <!-- Main Content Card -->
        <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Navigation -->
            <div class="relative border-b border-gray-200" id="terms-navigation">
                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center justify-between p-4">
                    <button wire:click="toggleMobileMenu"
                        class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                        <span class="font-medium">{{ $language === 'ms' ? 'Pilih Bahagian' : 'Select Section' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 ml-2 transform {{ $showMobileMenu ? 'rotate-180' : '' }}" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Search Toggle Button -->
                    <button class="md:hidden p-2 text-gray-600 hover:text-blue-600 focus:outline-none"
                        x-data="{}" @click="$dispatch('toggle-search')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Navigation Options -->
                <div class="{{ $showMobileMenu ? 'block' : 'hidden' }} md:hidden bg-gray-50 border-t border-gray-200">
                    <button x-data="{}"
                        @click="document.getElementById('all-sections').scrollIntoView({ behavior: 'smooth', block: 'start' }); $wire.setSection('all')"
                        class="w-full px-4 py-3 text-left {{ $currentSection === 'all' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-500' : 'text-gray-700' }}">
                        {{ $language === 'ms' ? 'Semua' : 'All' }}
                    </button>

                    @foreach ($sections as $key => $section)
                        <button x-data="{}"
                            @click="document.getElementById('section-{{ $key }}').scrollIntoView({ behavior: 'smooth', block: 'start' }); $wire.setSection('{{ $key }}')"
                            class="w-full px-4 py-3 text-left {{ $currentSection === $key ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-500' : 'text-gray-700' }}">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M{{ $section['id'] + 11 }} 5l7 7-7 7" />
                                </svg>
                                {{ $section['title'] }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex md:flex-wrap px-2 py-1">
                    <button x-data="{}"
                        @click="document.getElementById('all-sections').scrollIntoView({ behavior: 'smooth', block: 'start' }); $wire.setSection('all')"
                        class="px-4 py-3 text-sm font-medium rounded-md m-1 {{ $currentSection === 'all' ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        {{ $language === 'ms' ? 'Semua' : 'All' }}
                    </button>

                    @foreach ($sections as $key => $section)
                        <button x-data="{}"
                            @click="document.getElementById('section-{{ $key }}').scrollIntoView({ behavior: 'smooth', block: 'start' }); $wire.setSection('{{ $key }}')"
                            class="px-4 py-3 text-sm font-medium rounded-md m-1 {{ $currentSection === $key ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M{{ $section['id'] + 11 }} 5l7 7-7 7" />
                                </svg>
                                {{ $section['title'] }}
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>



            <!-- Content Section -->
            <div class="p-6" id="all-sections">
                <div class="space-y-8">
                    <!-- Section 1: Tanggungan Ahli -->
                    <div id="section-tanggungan" class="print-section scroll-mt-24" x-data="{ inView: false }"
                        x-intersect:enter="setTimeout(() => { inView = true }, 100)"
                        x-bind:class="inView ? 'animate-slide-in' : ''">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 p-1.5 rounded-lg mr-3">1</span>
                            {{ $language === 'ms' ? 'Tanggungan Ahli' : 'Member Dependents' }}
                        </h2>
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            @if ($language === 'ms')
                                <p class="mb-4">Tanggungan merujuk kepada individu-individu berikut yang tinggal
                                    serumah dengan ahli:</p>
                                <ul class="list-disc pl-5 mb-4 space-y-2">
                                    <li>Isteri atau suami ahli.</li>
                                    <li>Anak-anak yang belum berkahwin.</li>
                                    <li>Ibubapa ahli (ibu dan bapa kandung).</li>
                                    <li>Ibu bapa mertua ahli (ibu dan bapa pasangan).</li>
                                </ul>
                                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-3">
                                    <h3 class="font-bold text-amber-800">Pengecualian:</h3>
                                    <ul class="list-disc pl-5 text-amber-700">
                                        <li>Anak yang sudah berumah tangga dan tinggal serumah <strong>tidak dianggap
                                                sebagai tanggungan</strong> dan perlu mendaftar sebagai ahli secara
                                            berasingan.</li>
                                        <li>Ibubapa atau ibu bapa mertua yang <strong>tidak tinggal serumah</strong>
                                            dengan ahli <strong>tidak dikira sebagai tanggungan</strong>.</li>
                                    </ul>
                                </div>
                            @else
                                <p class="mb-4">Dependents refer to the following individuals living in the same house
                                    as the member:</p>
                                <ul class="list-disc pl-5 mb-4 space-y-2">
                                    <li>Member's spouse.</li>
                                    <li>Unmarried children.</li>
                                    <li>Member's parents (biological parents).</li>
                                    <li>Member's parents-in-law (spouse's parents).</li>
                                </ul>
                                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-3">
                                    <h3 class="font-bold text-amber-800">Exceptions:</h3>
                                    <ul class="list-disc pl-5 text-amber-700">
                                        <li>Married children living in the same house are <strong>not considered as
                                                dependents</strong> and must register as separate members.</li>
                                        <li>Parents or parents-in-law who <strong>do not live in the same house</strong>
                                            with the member <strong>are not counted as dependents</strong>.</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Section 2: Sumbangan Keahlian -->
                    <div id="section-sumbangan" class="print-section scroll-mt-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 p-1.5 rounded-lg mr-3">2</span>
                            {{ $language === 'ms' ? 'Sumbangan Keahlian' : 'Membership Contribution' }}
                        </h2>
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <ul class="space-y-3">
                                @if ($language === 'ms')
                                    <li class="flex">
                                        <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Setiap ahli dikehendaki membayar <strong class="text-green-700">sumbangan
                                                sebanyak RM100.00 SEKALI SAHAJA</strong> semasa mendaftar sebagai ahli
                                            BKKMTS.</span>
                                    </li>
                                    <li class="flex">
                                        <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Sumbangan ini adalah <strong>tidak tetap</strong> dan mungkin akan
                                            <strong>dikutip semula</strong> sekiranya dana atau wang yang dikumpulkan
                                            oleh BKKMTS mengalami pengurangan atau susut nilai.</span>
                                    </li>
                                    <li class="flex">
                                        <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Ahli yang ingin memberikan sumbangan tambahan adalah <strong>amat
                                                digalakkan dan dihargai</strong>. Sumbangan tambahan ini akan membantu
                                            memperkukuhkan dana BKKMTS untuk manfaat bersama.</span>
                                    </li>
                                @else
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Each member is required to pay a <strong class="text-green-700">contribution of
                                            RM100.00 ONCE ONLY</strong> when registering as a BKKMTS member.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        This contribution is <strong>not fixed</strong> and may be <strong>collected
                                            again</strong> if the funds collected by BKKMTS experience a reduction or
                                        depreciation.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Members who wish to make additional contributions are <strong>highly encouraged
                                            and appreciated</strong>. These additional contributions will help
                                        strengthen BKKMTS funds for mutual benefit.
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Section 3: Nota Penting -->
                    <div id="section-nota" class="print-section scroll-mt-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 p-1.5 rounded-lg mr-3">3</span>
                            {{ $language === 'ms' ? 'Nota Penting' : 'Important Notes' }}
                        </h2>
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <ul class="space-y-3">
                                @if ($language === 'ms')
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Semua maklumat yang diberikan semasa pendaftaran mestilah &nbsp;<b>tepat dan
                                            benar</b>.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Ahli bertanggungjawab untuk memaklumkan sebarang perubahan dalam maklumat
                                        tanggungan (contoh: perkahwinan, perpindahan, atau kematian) kepada pihak
                                        BKKMTS.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Pihak BKKMTS berhak untuk membuat semakan dan pengesahan terhadap maklumat yang
                                        diberikan oleh ahli.
                                    </li>
                                @else
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        All information provided during registration must be <strong>accurate and
                                            true</strong>.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Members are responsible for notifying any changes in dependent information
                                        (e.g., marriage, relocation, or death) to BKKMTS.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        BKKMTS reserves the right to review and verify information provided by members.
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Section 4: Hak dan Tanggungjawab Ahli -->
                    <div id="section-hak" class="print-section scroll-mt-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 p-1.5 rounded-lg mr-3">4</span>
                            {{ $language === 'ms' ? 'Hak dan Tanggungjawab Ahli' : 'Rights and Responsibilities of Members' }}
                        </h2>
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <ul class="space-y-3">
                                @if ($language === 'ms')
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                            </path>
                                        </svg>
                                        Ahli berhak menerima manfaat dan perkhidmatan yang disediakan oleh BKKMTS
                                        mengikut syarat-syarat yang ditetapkan.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                            </path>
                                        </svg>
                                        Ahli bertanggungjawab untuk mematuhi semua peraturan dan syarat yang digariskan
                                        oleh BKKMTS.
                                    </li>
                                @else
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                            </path>
                                        </svg>
                                        Members are entitled to receive benefits and services provided by BKKMTS in
                                        accordance with the established conditions.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-indigo-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                            </path>
                                        </svg>
                                        Members are responsible for complying with all rules and conditions outlined by
                                        BKKMTS.
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Section 5: Perubahan Syarat -->
                    <div id="section-perubahan" class="print-section scroll-mt-24">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="bg-blue-100 text-blue-800 p-1.5 rounded-lg mr-3">5</span>
                            {{ $language === 'ms' ? 'Perubahan Syarat' : 'Changes in Terms' }}
                        </h2>
                        <div class="bg-white rounded-lg p-5 border border-gray-200">
                            <ul class="space-y-3">
                                @if ($language === 'ms')
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        Pihak BKKMTS berhak untuk mengubah atau meminda syarat-syarat ini pada bila-bila
                                        masa tanpa notis terlebih dahulu.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        Ahli akan dimaklumkan mengenai sebarang perubahan melalui saluran komunikasi
                                        yang disediakan.
                                    </li>
                                @else
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        BKKMTS reserves the right to change or amend these terms at any time without
                                        prior notice.
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        Members will be notified of any changes through the provided communication
                                        channels.
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Section -->
            <div class="border-t border-gray-200 p-6 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-500 mb-4 sm:mb-0">
                    @if ($language === 'ms')
                        Dengan menggunakan perkhidmatan ini, anda bersetuju untuk mematuhi syarat-syarat dan peraturan
                        yang telah ditetapkan oleh BKKMTS.
                    @else
                        By using this service, you agree to comply with the terms and conditions set by BKKMTS.
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow-sm transition duration-200 text-sm font-medium flex items-center justify-center"
                        x-data="{}" @click="window.print()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        {{ $language === 'ms' ? 'Muat Turun PDF' : 'Download PDF' }}
                    </button>

                </div>
            </div>
        </div>

        <!-- Version Info -->
        <div class="mt-4 text-center text-xs text-gray-500">
            {{ $language === 'ms' ? 'Versi 1.0 - Dikemas kini pada' : 'Version 1.0 - Updated on' }} 27 Mar 2025
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('animation', {
            animate() {
                // Animation logic can be added here if needed
            }
        });
    });
</script>
