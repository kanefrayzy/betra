<div class="relative w-full max-w-2xl mx-auto mb-6">
    <div class="relative overflow-hidden rounded-xl bg-dark-800/60 border border-gray-800 shadow-lg hover:border-gray-700 transition-all duration-300">
        <div class="relative flex items-center group">
            <div class="absolute left-4 text-gray-400 group-focus-within:text-[#ffb300] transition-colors duration-300 z-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input
                type="text"
                id="search-game"
                wire:model="query"
                name="query"
                placeholder="{{ __('Найти игру...') }}"
                wire:keyup="submitSearch"
                class="w-full h-14 pl-12 pr-12 bg-transparent text-white placeholder-gray-400 group-focus-within:placeholder-gray-500 border-0 focus:outline-none focus:ring-0 transition-all duration-300"
            >
            <div class="absolute right-4 opacity-0 group-focus-within:opacity-100 transition-opacity duration-300 text-[#ffb300]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z"/>
                </svg>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-[#ffb300] to-transparent opacity-0 group-focus-within:opacity-100 transition-opacity duration-300"></div>
    </div>
</div>

<style>
    #search-game:-webkit-autofill,
    #search-game:-webkit-autofill:hover,
    #search-game:-webkit-autofill:focus {
        -webkit-text-fill-color: white;
        -webkit-box-shadow: 0 0 0px 1000px transparent inset;
        transition: background-color 5000s ease-in-out 0s;
    }
</style>
