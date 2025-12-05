<!-- Hero Banners -->
<div class="container mx-auto px-3 sm:px-4 lg:px-6 pb-6 pt-2 sm:pt-4 lg:pt-6">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:items-stretch">
        <!-- Main Slider -->
        <div class="lg:col-span-1 relative">
            <div id="main-slider-container" class="swiper overflow-hidden rounded-2xl aspect-video">
                <div class="swiper-wrapper">
                    @forelse($mainBanners as $banner)
                        <div class="swiper-slide">
                            @if($banner->link)
                                <a href="{{ $banner->link }}" class="block relative h-full rounded-2xl overflow-hidden border border-gray-800">
                                    <img src="{{ $banner->image_url }}" 
                                         alt="{{ $banner->title }}" 
                                         loading="lazy" 
                                         decoding="async" 
                                         class="w-full h-full object-cover">
                                </a>
                            @else
                                <div class="relative h-full rounded-2xl overflow-hidden border border-gray-800">
                                    <img src="{{ $banner->image_url }}" 
                                         alt="{{ $banner->title }}" 
                                         loading="lazy" 
                                         decoding="async" 
                                         class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="relative h-full rounded-2xl overflow-hidden border border-gray-800">
                                <img src="https://jetvora.life/_next/image?url=https%3A%2F%2Fgames.cloudfire.app%2Fimages%2Ftournaments%2Fd21ba6fb-2a77-4d27-b8f2-f6951f29368a.png&w=1920&q=75" 
                                     alt="Banner" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="absolute top-1/2 -translate-y-1/2 left-4 z-10">
                    <button id="main-prev" class="w-10 h-10 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center text-white hover:bg-black/70 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/></svg>
                    </button>
                </div>
                <div class="absolute top-1/2 -translate-y-1/2 right-4 z-10">
                    <button id="main-next" class="w-10 h-10 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center text-white hover:bg-black/70 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                    </button>
                </div>

                <div class="absolute bottom-3 left-4 z-10">
                    <div id="main-pagination" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 max-lg:hidden h-full">
        @if($smallBanners->count() >= 2)
            @php
                $firstHalf = $smallBanners->take(ceil($smallBanners->count() / 2));
                $secondHalf = $smallBanners->skip(ceil($smallBanners->count() / 2));
            @endphp

            <div class="relative group cursor-pointer h-full">
                <div id="small-slider-1" class="swiper overflow-hidden rounded-2xl h-full">
                    <div class="swiper-wrapper h-full">
                        @foreach($firstHalf as $banner)
                            <div class="swiper-slide h-full">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <img src="{{ asset($banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <div class="h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <img src="{{ asset($banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10">
                        <div id="small-pagination-1" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                    </div>
                </div>
            </div>

            <div class="relative group cursor-pointer h-full">
                <div id="small-slider-2" class="swiper overflow-hidden rounded-2xl h-full">
                    <div class="swiper-wrapper h-full">
                        @foreach($secondHalf as $banner)
                            <div class="swiper-slide h-full">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <img src="{{ asset($banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <div class="h-full rounded-2xl overflow-hidden hover:-translate-y-2 transition-transform duration-300">
                                        <img src="{{ asset($banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10">
                        <div id="small-pagination-2" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                    </div>
                </div>
            </div>
        @else
            <div class="relative group cursor-pointer h-full">
                <div id="small-slider-1" class="swiper overflow-hidden rounded-2xl h-full">
                    <div class="swiper-wrapper h-full">
                        @forelse($smallBanners as $banner)
                            <div class="swiper-slide h-full">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block h-full rounded-2xl overflow-hidden border border-gray-800 hover:-translate-y-2 transition-transform duration-300">
                                        <img src="{{ asset($banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <div class="h-full rounded-2xl overflow-hidden border border-gray-800 hover:-translate-y-2 transition-transform duration-300">
                                        <img src="{{ asset($banner->image) }}" 
                                             alt="{{ $banner->title }}" 
                                             loading="lazy" 
                                             decoding="async" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="swiper-slide h-full">
                                <div class="h-full rounded-2xl overflow-hidden border border-gray-800 hover:-translate-y-2 transition-transform duration-300">
                                    <img src="https://jetvora.life/_next/image?url=https%3A%2F%2Fgames.cloudfire.app%2Fimages%2Ftournaments%2F6e868950-0f4d-4e18-a176-c80160e259c7.webp&w=640&q=75" 
                                         alt="Small Banner" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10">
                        <div id="small-pagination-1" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                    </div>
                </div>
            </div>

            <div class="relative group cursor-pointer h-full">
                <div id="small-slider-2" class="swiper overflow-hidden rounded-2xl h-full">
                    <div class="swiper-wrapper h-full">
                        <div class="swiper-slide h-full">
                            <div class="h-full rounded-2xl overflow-hidden border border-gray-800 hover:-translate-y-2 transition-transform duration-300">
                                <img src="https://picsum.photos/seed/small3/400/533" 
                                     alt="Small Banner" 
                                     loading="lazy" 
                                     decoding="async" 
                                     class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-10">
                        <div id="small-pagination-2" class="flex gap-1 bg-white/10 backdrop-blur-sm rounded-full p-1"></div>
                    </div>
                </div>
            </div>
        @endif
        </div>

        <div class="flex gap-4 overflow-x-auto lg:hidden snap-x snap-mandatory scrollbar-hide pb-2">
            @forelse($smallBanners as $banner)
                <div class="flex-shrink-0 w-40 snap-start">
                    @if($banner->link)
                        <a href="{{ $banner->link }}" target="_blank" rel="noopener" class="block relative aspect-[162/104] rounded-2xl overflow-hidden border border-gray-800">
                            <img src="{{ $banner->mobile_image_url }}" 
                                 alt="{{ $banner->title }}" 
                                 loading="lazy" 
                                 decoding="async" 
                                 class="w-full h-full object-cover">
                        </a>
                    @else
                        <div class="relative aspect-[162/104] rounded-2xl overflow-hidden border border-gray-800">
                            <img src="{{ $banner->mobile_image_url }}" 
                                 alt="{{ $banner->title }}" 
                                 loading="lazy" 
                                 decoding="async" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endif
                </div>
            @empty
                <div class="flex-shrink-0 w-40 snap-start">
                    <div class="relative aspect-[162/104] rounded-2xl overflow-hidden border border-gray-800">
                        <img src="https://picsum.photos/seed/mobile1/400/533" 
                             alt="Small Banner" 
                             loading="lazy" 
                             decoding="async" 
                             class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="flex-shrink-0 w-40 snap-start">
                    <div class="relative aspect-[162/104] rounded-2xl overflow-hidden border border-gray-800">
                        <img src="https://picsum.photos/seed/mobile2/400/533" 
                             alt="Small Banner" 
                             loading="lazy" 
                             decoding="async" 
                             class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="flex-shrink-0 w-40 snap-start">
                    <div class="relative aspect-[162/104] rounded-2xl overflow-hidden border border-gray-800">
                        <img src="https://picsum.photos/seed/mobile3/400/533" 
                             alt="Small Banner" 
                             loading="lazy" 
                             decoding="async" 
                             class="w-full h-full object-cover">
                    </div>
                </div>
            @endforelse
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