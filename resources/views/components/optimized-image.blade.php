@props(['src', 'alt' => '', 'class' => '', 'width' => null, 'height' => null, 'lazy' => true])

@php
    $webpSrc = webp_url($src);
    $loading = $lazy ? 'lazy' : 'eager';
    $hasWebP = $webpSrc !== $src && pathinfo($webpSrc, PATHINFO_EXTENSION) === 'webp';
@endphp

<picture>
    @if($hasWebP)
        <source srcset="{{ asset($webpSrc) }}" type="image/webp">
    @endif
    <img 
        src="{{ asset($src) }}" 
        alt="{{ $alt }}" 
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        loading="{{ $loading }}"
        decoding="async"
        class="{{ $class }}"
        {{ $attributes }}
    >
</picture>
