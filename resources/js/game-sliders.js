/**
 * Game Sliders Management
 */

export function initGameSliders() {
    function initGameSlider(sliderId, prevId, nextId) {
        const slider = document.getElementById(sliderId);
        const prevBtn = document.getElementById(prevId);
        const nextBtn = document.getElementById(nextId);
        if (!slider || !prevBtn || !nextBtn) return;

        // Точный расчет ширины одной карточки с gap (2.5 = 10px)
        const firstCard = slider.querySelector('div');
        const cardWidth = firstCard ? firstCard.offsetWidth : 144; // w-36 = 144px
        const gap = 10; // gap-2.5
        const slideWidth = cardWidth + gap;

        // Рассчитываем сколько карточек помещается в видимую область
        const visibleWidth = slider.offsetWidth;
        const cardsToScroll = Math.floor(visibleWidth / slideWidth);
        const scrollAmount = cardsToScroll * slideWidth;

        // Удаляем старые обработчики
        const newPrevBtn = prevBtn.cloneNode(true);
        const newNextBtn = nextBtn.cloneNode(true);
        prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
        nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);

        newPrevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        newNextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }

    // Инициализируем все слайдеры на странице
    // Находим все элементы с pattern: {slug}-slider
    const sliders = document.querySelectorAll('[id$="-slider"]');
    sliders.forEach(slider => {
        const slug = slider.id.replace('-slider', '');
        const prevId = `${slug}-prev`;
        const nextId = `${slug}-next`;
        initGameSlider(slider.id, prevId, nextId);
    });
    
    // Scroll to tabs при клике на мобильных
    const tabButtons = document.querySelectorAll('[\\@click^="activeTab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (window.innerWidth < 768) {
                const tabsContainer = button.closest('.container');
                if (tabsContainer) {
                    setTimeout(() => {
                        tabsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            }
        });
    });
}

/**
 * Handle game click - открытие игры или демо
 */
export function handleGameClick(url, event) {
    if (event) event.preventDefault();
    
    // Проверяем авторизацию для реальной игры
    const config = window.appConfig || {};
    const isAuthenticated = config.user !== null;
    
    // Если это demo (contains 'fun'), открываем без проверки
    if (url.includes('/fun/')) {
        window.location.href = url;
        return;
    }
    
    // Если это реальная игра и не авторизован - показываем модалку логина
    if (!isAuthenticated) {
        if (typeof window.openModal === 'function') {
            window.openModal('login-modal');
        } else {
            window.dispatchEvent(new CustomEvent('open-register-modal'));
        }
        return;
    }
    
    // Авторизован - открываем игру
    window.location.href = url;
}

// Expose globally for backward compatibility
window.initGameSliders = initGameSliders;
window.handleGameClick = handleGameClick;

// Auto-init on DOMContentLoaded and Livewire navigation
document.addEventListener('DOMContentLoaded', initGameSliders);
document.addEventListener('livewire:navigated', initGameSliders);
