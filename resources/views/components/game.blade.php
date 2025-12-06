<div class="game bg-dark-900 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 hover:transform hover:scale-105 relative">
  @if(!$isMobile)
    <a href="{{ route('slots.play', $game->name) }}" class="block">
        <picture>
            <source srcset="{{ webp_url($game->image) }}" type="image/webp">
            <img src="{{ $game->image }}" alt="{{ $game->name }}" class="w-full h-40 object-cover">
        </picture>
        <div class="absolute inset-0 bg-gradient-to-t from-dark-950/70 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-end">
            <p class="text-white px-3 py-2 font-medium text-sm truncate w-full">{{ $game->name }}</p>
        </div>
    </a>
    <div class="flex justify-between items-center px-2 py-2">
        <x-UI.favoriteButton :game="$game" class="btn-icon text-gray-400 hover:text-primary transition-colors"/>
    </div>
  @else
    <a onclick="openModal('game-modal');" class="block cursor-pointer">
        <picture>
            <source srcset="{{ webp_url($game->image) }}" type="image/webp">
            <img src="{{ $game->image }}" alt="{{ $game->name }}" class="w-full h-40 object-cover">
        </picture>
        <div class="absolute inset-0 bg-gradient-to-t from-dark-950/70 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-end">
            <p class="text-white px-3 py-2 font-medium text-sm truncate w-full">{{ $game->name }}</p>
        </div>
    </a>
  @endif
</div>
