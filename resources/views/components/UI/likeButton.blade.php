<button wire:click="toggleLike({{ $id }})" class="btn-icon">
    @if($this->checkIsLiked($id ))
        <i class="fas fa-thumbs-up text-primary"></i>
    @else
        <i class="far fa-thumbs-up text-primary"></i>
    @endif
</button>
<span>{{ $this->getLikeCount($id) }}</span>
