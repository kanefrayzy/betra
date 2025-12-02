<div>
    @if($gameId !== null)
        <button wire:click="toggleFavorite('{{ $gameId }}')" class="btn-icon">
            @if($isFavorite)
                <i class="fas fa-star text-danger" aria-hidden="true"></i>
            @else
                <i class="far fa-star text-primary" aria-hidden="true"></i>
            @endif
        </button>

        <button wire:click="toggleLike('{{ $gameId }}')" class="btn-icon">
            @if($isLiked)
                <i class="fas fa-thumbs-up text-danger" aria-hidden="true"></i>
            @else
                <i class="far fa-thumbs-up text-primary" aria-hidden="true"></i>
            @endif
            <span>{{ $likeCount }}</span>
        </button>
    @endif
</div>
