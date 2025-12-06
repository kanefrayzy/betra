<div class="relative w-full mb-6" x-data="{ searchQuery: '' }">
    <div class="relative">
        <input
            type="text"
            id="search-game"
            wire:model.live.debounce.300ms="query"
            name="query"
            placeholder="{{ __('Введите название игры...') }}"
            x-model="searchQuery"
            class="w-full h-12 pl-11 pr-11 bg-[#1a2c38] text-white placeholder-gray-600 border-2 border-gray-700 rounded-md focus:outline-none focus:border-gray-500 transition-all text-sm"
            autocomplete="off"
        >
        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        
        <button
            x-show="searchQuery"
            @click="searchQuery = ''; $wire.set('query', '')"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
