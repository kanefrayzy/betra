<!-- Hero Banners -->
<div class="container mx-auto px-3 sm:px-4 lg:px-6 pb-2 pt-2 sm:pt-4 lg:pt-6">
    <div class="grid grid-cols-1 gap-4">
        <div class="grid grid-cols-3 gap-4 max-lg:hidden">
            @php
                $thirdPart1 = $smallBanners->take(ceil($smallBanners->count() / 3));
                $thirdPart2 = $smallBanners->skip(ceil($smallBanners->count() / 3))->take(ceil($smallBanners->count() / 3));
                $thirdPart3 = $smallBanners->skip(ceil($smallBanners->count() / 3) * 2);
            @endphp

            <div class="relative group cursor-pointer">
                <div id="small-slider-1" class="swiper overflow-hidden rounded-2xl aspect-[400/150]">
                    <div class="swiper-wrapper">
                        @forelse($thirdPart1 as $banner)
                            <div class="swiper-slide">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <picture>
                                            <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', asset($banner->image)) }}" type="image/webp">
                                            <img src="{{ asset($banner->image) }}" 
                                                 alt="{{ $banner->title }}" 
                                                 loading="lazy" 
                                                 decoding="async" 
                                                 class="w-full h-full object-cover">
                                        </picture>
                                    </a>
                                @else
                                    <div class="h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <picture>
                                            <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', asset($banner->image)) }}" type="image/webp">
                                            <img src="{{ asset($banner->image) }}" 
                                                 alt="{{ $banner->title }}" 
                                                 loading="lazy" 
                                                 decoding="async" 
                                                 class="w-full h-full object-cover">
                                        </picture>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="h-full rounded-2xl overflow-hidden border border-gray-800">
                                    <img src="https://picsum.photos/seed/small1/400/150" 
                                         alt="Small Banner" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10">
                        <div id="small-pagination-1" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                    </div>
                </div>
            </div>

            <div class="relative group cursor-pointer">
                <div id="small-slider-2" class="swiper overflow-hidden rounded-2xl aspect-[400/150]">
                    <div class="swiper-wrapper">
                        @forelse($thirdPart2 as $banner)
                            <div class="swiper-slide">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <picture>
                                            <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', asset($banner->image)) }}" type="image/webp">
                                            <img src="{{ asset($banner->image) }}" 
                                                 alt="{{ $banner->title }}" 
                                                 loading="lazy" 
                                                 decoding="async" 
                                                 class="w-full h-full object-cover">
                                        </picture>
                                    </a>
                                @else
                                    <div class="h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <picture>
                                            <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', asset($banner->image)) }}" type="image/webp">
                                            <img src="{{ asset($banner->image) }}" 
                                                 alt="{{ $banner->title }}" 
                                                 loading="lazy" 
                                                 decoding="async" 
                                                 class="w-full h-full object-cover">
                                        </picture>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="h-full rounded-2xl overflow-hidden border border-gray-800">
                                    <img src="https://picsum.photos/seed/small2/400/150" 
                                         alt="Small Banner" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10">
                        <div id="small-pagination-2" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                    </div>
                </div>
            </div>

            <div class="relative group cursor-pointer">
                <div id="small-slider-3" class="swiper overflow-hidden rounded-2xl aspect-[400/150]">
                    <div class="swiper-wrapper">
                        @forelse($thirdPart3 as $banner)
                            <div class="swiper-slide">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <picture>
                                            <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', asset($banner->image)) }}" type="image/webp">
                                            <img src="{{ asset($banner->image) }}" 
                                                 alt="{{ $banner->title }}" 
                                                 loading="lazy" 
                                                 decoding="async" 
                                                 class="w-full h-full object-cover">
                                        </picture>
                                    </a>
                                @else
                                    <div class="h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <picture>
                                            <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', asset($banner->image)) }}" type="image/webp">
                                            <img src="{{ asset($banner->image) }}" 
                                                 alt="{{ $banner->title }}" 
                                                 loading="lazy" 
                                                 decoding="async" 
                                                 class="w-full h-full object-cover">
                                        </picture>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="h-full rounded-2xl overflow-hidden border border-gray-800">
                                    <img src="https://picsum.photos/seed/small3/400/150" 
                                         alt="Small Banner" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10">
                        <div id="small-pagination-3" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:hidden">
            <div id="mobile-slider" class="swiper overflow-hidden rounded-2xl aspect-[400/150]">
                <div class="swiper-wrapper">
                    @forelse($smallBanners as $banner)
                        <div class="swiper-slide">
                            @if($banner->link)
                                <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block h-full rounded-2xl overflow-hidden border border-gray-800">
                                    <picture>
                                        <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', $banner->mobile_image_url) }}" type="image/webp">
                                        <img src="{{ $banner->mobile_image_url }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </picture>
                                </a>
                            @else
                                <div class="h-full rounded-2xl overflow-hidden border border-gray-800">
                                    <picture>
                                        <source srcset="{{ str_replace(['.jpg', '.jpeg', '.png'], '.webp', $banner->mobile_image_url) }}" type="image/webp">
                                        <img src="{{ $banner->mobile_image_url }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </picture>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="h-full rounded-2xl overflow-hidden border border-gray-800">
                                <img src="https://picsum.photos/seed/mobile1/400/150" 
                                     alt="Small Banner" 
                                     loading="lazy" 
                                     decoding="async" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10">
                    <div id="mobile-pagination" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .swiper-pagination-bullet {
        width: 6px !important;
        height: 6px !important;
        background: rgba(255, 255, 255, 0.4) !important;
        opacity: 1 !important;
    }

    .swiper-pagination-bullet-active {
        background: #47d229 !important;
        width: 20px !important;
        border-radius: 3px !important;
    }
</style>