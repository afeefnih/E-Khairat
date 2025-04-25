<!-- resources/views/livewire/date-range-selector.blade.php -->
<div>

        <div class="space-y-4">
            <h2 class="text-xl font-bold">Tempoh Paparan Dashboard</h2>

            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($presets as $key => $label)
                    <button
                        wire:click="setPresetDates('{{ $key }}')"
                        class="px-3 py-1 text-sm rounded-full {{ $preset === $key ? 'bg-primary-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tarikh Mula</label>
                    <input
                        type="date"
                        id="startDate"
                        wire:model.live="startDate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        max="{{ $endDate }}"
                    >
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tarikh Akhir</label>
                    <input
                        type="date"
                        id="endDate"
                        wire:model.live="endDate"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        min="{{ $startDate }}"
                    >
                </div>
            </div>
        </div>

</div>
