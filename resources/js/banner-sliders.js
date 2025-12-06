import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

window.swiperInstances = window.swiperInstances || {};

export function initBannerSliders() {
    Object.values(window.swiperInstances).forEach(s => s?.destroy?.(true, true));
    window.swiperInstances = {};

    const configs = [
        { id: 'small-slider-1', key: 'small1', opts: {
            modules: [Pagination, Autoplay],
            autoplay: { delay: 6000, disableOnInteraction: false },
            pagination: { el: '#small-pagination-1', clickable: true }
        }},
        { id: 'small-slider-2', key: 'small2', opts: {
            modules: [Pagination, Autoplay],
            autoplay: { delay: 7000, disableOnInteraction: false },
            pagination: { el: '#small-pagination-2', clickable: true }
        }},
        { id: 'small-slider-3', key: 'small3', opts: {
            modules: [Pagination, Autoplay],
            autoplay: { delay: 8000, disableOnInteraction: false },
            pagination: { el: '#small-pagination-3', clickable: true }
        }},
        { id: 'mobile-slider', key: 'mobile', opts: {
            modules: [Pagination, Autoplay],
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '#mobile-pagination', clickable: true }
        }}
    ];

    configs.forEach(({ id, key, opts }) => {
        const el = document.getElementById(id);
        if (el?.querySelectorAll('.swiper-slide').length > 0) {
            window.swiperInstances[key] = new Swiper(`#${id}`, {
                ...opts,
                loop: el.querySelectorAll('.swiper-slide').length > 1
            });
        }
    });
}

window.initBannerSliders = initBannerSliders;

document.addEventListener('DOMContentLoaded', initBannerSliders);
document.addEventListener('livewire:navigated', () => setTimeout(initBannerSliders, 100));
