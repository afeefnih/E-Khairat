<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="py-6">
        <!-- Place for Dashboard content -->
        <h2 class="text-3xl font-semibold">Welcome back, {{ Auth::user()->name }}</h2>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Example Card 1 -->
            <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800 dark:text-white">
                <h3 class="text-lg font-semibold">Card 1</h3>
                <p class="mt-2">Content for your dashboard card.</p>
            </div>

            <!-- Example Card 2 -->
            <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800 dark:text-white">
                <h3 class="text-lg font-semibold">Card 2</h3>
                <p class="mt-2">Content for your dashboard card.</p>
            </div>

            <!-- Example Card 3 -->
            <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800 dark:text-white">
                <h3 class="text-lg font-semibold">Card 3</h3>
                <p class="mt-2">Content for your dashboard card.</p>
            </div>
        </div>
    </div>
</div>
