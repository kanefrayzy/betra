document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const rightSidebar = document.getElementById('right-sidebar');
    const backdrop = document.getElementById('backdrop');
    const content = document.querySelector('.content');
    const hasSubmenus = document.querySelectorAll('.has-submenu');
    const body = document.body;
	const container = document.querySelectorAll('.container');
    const toggleRightSidebarButton = document.querySelector('.toggle-right-sidebar');
    const messagesContainer = document.getElementById('messages');

    if (toggleButton && sidebar && backdrop && content) {
        toggleButton.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            toggleButton.classList.toggle('active');
            content.classList.toggle('blur-background');
            backdrop.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                body.classList.add('overflow-hidden');
				container.classList.add('overflow-hidden');
            } else {
                body.classList.remove('overflow-hidden');
				container.classList.remove('overflow-hidden');
            }
        });

        toggleRightSidebarButton.addEventListener('click', function () {
            rightSidebar.classList.toggle('active');
            toggleRightSidebarButton.classList.toggle('active');
            content.classList.toggle('blur-background');
            backdrop.classList.toggle('active');

            if (rightSidebar.classList.contains('active')) {
                body.classList.add('overflow-hidden');
                scrollToBottom(messagesContainer);
				container.classList.add('overflow-hidden');
            } else {
                body.classList.remove('overflow-hidden');
				container.classList.remove('overflow-hidden');
            }
        });

        backdrop.addEventListener('click', function () {
            closeMenu();
            closeRightSidebar();
        });

        sidebar.addEventListener('click', function (event) {
            if (!event.target.closest('.has-submenu')) {
                closeSubmenus();
            }
        });

        hasSubmenus.forEach(function (item) {
            const submenu = item.querySelector('.submenu');
            if (submenu) {
                item.addEventListener('click', function (event) {
                    event.preventDefault(); // Отменяем стандартное действие ссылки
                    submenu.classList.toggle('active');
                    const submenuArrow = item.querySelector('.submenu-arrow');
                    submenuArrow.classList.toggle('rotate');
                });

                // Добавляем обработчик события на ссылки в подменю
                const submenuLinks = submenu.querySelectorAll('a');
                submenuLinks.forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.stopPropagation(); // Предотвращаем всплытие события до родительского элемента
                    });
                });
            }
        });

        // Добавляем обработчик для иконки закрытия в правой боковой панели
        const closeRightSidebarIcon = rightSidebar.querySelector('.toggle-right-sidebar');
        if (closeRightSidebarIcon) {
            closeRightSidebarIcon.addEventListener('click', function () {
                closeRightSidebar();
            });
        }
    } else {
        console.error('Menu toggle button, sidebar, backdrop, or content not found.');
    }

    function closeMenu() {
        sidebar.classList.remove('active');
        toggleButton.classList.remove('active');
        content.classList.remove('blur-background');
        backdrop.classList.remove('active');
        body.classList.remove('overflow-hidden');
        closeSubmenus();
    }

    function closeRightSidebar() {
        rightSidebar.classList.remove('active');
        toggleRightSidebarButton.classList.remove('active');
        content.classList.remove('blur-background');
        backdrop.classList.remove('active');
        body.classList.remove('overflow-hidden');
    }

    function closeSubmenus() {
        const submenus = document.querySelectorAll('.submenu');
        submenus.forEach(function (submenu) {
            submenu.classList.remove('active');
            const submenuArrow = submenu.parentElement.querySelector('.submenu-arrow');
            submenuArrow.classList.remove('rotate');
        });
    }

    function scrollToBottom(container) {
        container.scrollTop = container.scrollHeight;
    }
});

     document.addEventListener('DOMContentLoaded', function () {
            const profileMenu = document.querySelector('.profile-menu');
            const subMenu = profileMenu.querySelector('.sub-head-menu-mobile');

            profileMenu.addEventListener('click', function (event) {
                event.preventDefault();
                subMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function (event) {
                if (!profileMenu.contains(event.target)) {
                    subMenu.classList.add('hidden');
                }
            });
        });
// Функция для открытия модального окна
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const closeBtn = modal.querySelector('.close');
    const overlay = document.querySelector('.overlay');

    if (!modal || !closeBtn || !overlay) {
        console.error('Modal, close button, or overlay not found');
        return;
    }

    // При клике на кнопку закрытия, закрываем текущее модальное окно и убираем задний фон
    closeBtn.addEventListener('click', function () {
        closeModal(modal, overlay);
    });

    // При клике на overlay, закрываем текущее модальное окно и убираем задний фон
    overlay.addEventListener('click', function () {
        closeModal(modal, overlay);
    });

    modal.style.display = 'block';
    overlay.style.display = 'block';
}

// Функция для закрытия модального окна
function closeModal(modal, overlay) {
    modal.style.display = 'none';
    overlay.style.display = 'none';
}

// Открываем модальное окно при клике на кнопку
document.addEventListener('DOMContentLoaded', function () {
    const signupBtn = document.querySelector('.signup-btn');
    const loginBtn = document.querySelector('.login-btn');
    const payOpenBtn = document.querySelector('.cash-btn');
    const payOpenBtnMobile = document.querySelector('.cash-btn-mobile');
    const rankBtn = document.querySelector('.rank-btn');

    if (signupBtn) {
        signupBtn.addEventListener('click', function () {
            openModal('register-modal');
        });
    } else {
        console.error('Signup button not found');
    }

    if (loginBtn) {
        loginBtn.addEventListener('click', function () {
            openModal('login-modal');
        });
    } else {
        console.error('Login button not found');
    }

    if (payOpenBtn) {
        payOpenBtn.addEventListener('click', function () {
            openModal('cash-modal');
        });
    } else {
        console.error('Pay open button not found');
    }

    if (payOpenBtnMobile) {
        payOpenBtnMobile.addEventListener('click', function () {
            openModal('cash-modal');
        });
    } else {
        console.error('Pay open button not found');
    }

    if (rankBtn) {
        rankBtn.addEventListener('click', function () {
            openModal('rank-modal');
        });
    } else {
        console.error('Pay open button not found');
    }
});

function openTab(evt, tabName) {
    let i, tabContent, tablinks;
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}


//   slider
// Функция для управления слайдером
function customSlide(gallery, currentSlide, direction) {
    const slides = gallery.querySelectorAll('.custom-slide');
    const slideWidth = slides[0].offsetWidth + 10; // ширина слайда + margin
    const visibleSlides = Math.floor(gallery.offsetWidth / slideWidth);
    const totalSlides = slides.length;

    currentSlide += direction * visibleSlides;

    if (currentSlide < 0) {
        currentSlide = 0;
    } else if (currentSlide > totalSlides - visibleSlides) {
        currentSlide = totalSlides - visibleSlides;
    }

    slides.forEach((slide, index) => {
        if (index < currentSlide || index >= currentSlide + visibleSlides) {
            slide.style.filter = 'blur(5px)';
        } else {
            slide.style.filter = 'none';
        }
    });

    gallery.scrollTo({
        left: currentSlide * slideWidth,
        behavior: 'smooth'
    });

    return currentSlide; // Возвращаем обновленное значение текущего слайда
}

// Функция для обработки нажатий на стрелки
function handleArrowClick(gallery, currentSlide, direction) {
    currentSlide = customSlide(gallery, currentSlide, direction);
}

// Находим кнопки
const backwardButtons = document.querySelectorAll('.custom-arrow.custom-backward');
const forwardButtons = document.querySelectorAll('.custom-arrow.custom-forward');

// Добавляем обработчики событий для каждого слайдера
backwardButtons.forEach((button, index) => {
    button.addEventListener('click', function () {
        const gallery = document.querySelectorAll('.custom-gallery')[index];
        let currentSlide = 0; // Начальное значение текущего слайда для каждого слайдера
        handleArrowClick(gallery, currentSlide, -1);
    });
});

forwardButtons.forEach((button, index) => {
    button.addEventListener('click', function () {
        const gallery = document.querySelectorAll('.custom-gallery')[index];
        let currentSlide = 0; // Начальное значение текущего слайда для каждого слайдера
        handleArrowClick(gallery, currentSlide, 1);
    });
});
// Находим все галереи
const galleries = document.querySelectorAll('.custom-gallery');

// Добавляем обработчик события прокрутки колеса мыши для каждой галереи
// galleries.forEach(gallery => {
//     gallery.addEventListener('wheel', handleScroll);
// });

// Функция для обработки прокрутки колеса мыш
/*

function handleScroll(event) {
    event.preventDefault();
    const direction = event.deltaY > 0 ? 1 : -1; // Определяем направление прокрутки
    const gallery = event.currentTarget; // Получаем текущую галерею
    let currentSlide = 0; // Начальное значение текущего слайда для каждой галереи

    // Вызываем функцию для управления слайдером
    customSlide(gallery, currentSlide, direction);
}


*/
function createRainDrops(container, dropCount) {
    for (let i = 0; i < dropCount; i++) {
        const rainDrop = document.createElement('div');
        rainDrop.classList.add('rain-drop');
        rainDrop.style.left = `${Math.random() * 100}%`;
        rainDrop.style.animationDuration = `${Math.random() * 0.5 + 0.5}s`; // Время анимации от 0.5 до 1 секунды
        rainDrop.style.animationDelay = `${Math.random() * 2}s`; // Задержка анимации до 2 секунд
        container.appendChild(rainDrop);
    }
}

function toggleSelectOptions() {
    document.querySelector('.select-options').classList.toggle('show');
}

function selectCurrency(currency) {
    document.getElementById('currencyInput').value = currency;
    document.getElementById('currencyForm').action = `account/change-currency/${currency}`;
    document.getElementById('currencyForm').submit();
}

// Закрытие выпадающего списка при клике вне его
// document.addEventListener('click', function (e) {
//     const selectWrapper = document.querySelector('.custom-select-wrapper');
//     if (!selectWrapper.contains(e.target)) {
//         document.querySelector('.select-options').classList.remove('show');
//     }
// });
