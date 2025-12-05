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
        { id: 'main-slider-container', key: 'mainDesktop', opts: {
            modules: [Navigation, Pagination, Autoplay, EffectFade],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoplay: { delay: 5000, disableOnInteraction: false },
            navigation: { nextEl: '#main-next', prevEl: '#main-prev' },
            pagination: { el: '#main-pagination', clickable: true }
        }},
        { id: 'main-slider-mobile', key: 'mainMobile', opts: {
            modules: [Pagination, Autoplay, EffectFade],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '#main-pagination-mobile', clickable: true }
        }},
        { id: 'small-slider-1', key: 'small1', opts: {
            modules: [Pagination, Autoplay],
            autoplay: { delay: 6000, disableOnInteraction: false },
            pagination: { el: '#small-pagination-1', clickable: true }
        }},
        { id: 'small-slider-2', key: 'small2', opts: {
            modules: [Pagination, Autoplay],
            autoplay: { delay: 7000, disableOnInteraction: false },
            pagination: { el: '#small-pagination-2', clickable: true }
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
