<button wire:click="toggleFavorite({{ $game->id }})" class="btn-icon">
    @if($this->isFavorite($game->id))
        <i class="fas fa-star text-danger"></i>
    @else
        <i class="far fa-star text-primary"></i>
    @endif
</button>
