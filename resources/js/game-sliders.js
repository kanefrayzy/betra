export function initGameSliders() {
    function initGameSlider(sliderId, prevId, nextId) {
        const slider = document.getElementById(sliderId);
        const prevBtn = document.getElementById(prevId);
        const nextBtn = document.getElementById(nextId);
        if (!slider || !prevBtn || !nextBtn) return;

        const firstCard = slider.querySelector('div');
        const cardWidth = firstCard ? firstCard.offsetWidth : 144;
        const gap = 10;
        const slideWidth = cardWidth + gap;
        const visibleWidth = slider.offsetWidth;
        const cardsToScroll = Math.floor(visibleWidth / slideWidth);
        const scrollAmount = cardsToScroll * slideWidth;

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

    const sliders = document.querySelectorAll('[id$="-slider"]');
    sliders.forEach(slider => {
        const slug = slider.id.replace('-slider', '');
        initGameSlider(slider.id, `${slug}-prev`, `${slug}-next`);
    });
    
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

export function handleGameClick(url, event) {
    if (event) event.preventDefault();
    
    const config = window.appConfig || {};
    const isAuthenticated = config.user !== null;
    
    if (url.includes('/fun/')) {
        window.location.href = url;
        return;
    }
    
    if (!isAuthenticated) {
        if (typeof window.openModal === 'function') {
            window.openModal('login-modal');
        } else {
            window.dispatchEvent(new CustomEvent('open-register-modal'));
        }
        return;
    }
    
    window.location.href = url;
}

window.initGameSliders = initGameSliders;
window.handleGameClick = handleGameClick;

document.addEventListener('DOMContentLoaded', initGameSliders);
document.addEventListener('livewire:navigated', initGameSliders);
