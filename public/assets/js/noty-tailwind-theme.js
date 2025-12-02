// Современная тема Noty с Tailwind CSS для Flash/Roobet
export function initNotyTailwindTheme() {
    // Проверяем, загружена ли библиотека Noty
    if (typeof Noty === 'undefined') {
        return;
    }

    if (typeof window.Noty === 'undefined') {
        return;
    }
    // Настройка глобальных параметров Noty
    Noty.overrideDefaults({
        layout: 'topRight',
        theme: 'premium', // Наша новая премиум-тема
        timeout: 4000,
        progressBar: true,
        closeWith: ['click', 'button'],
        animation: {
            open: function(promise) {
                var n = this;
                n.barDom.style.transform = 'translateY(-20px)';
                n.barDom.style.opacity = 0;

                // Добавляем анимацию появления с отскоком
                n.barDom.animate(
                    [
                        { transform: 'translateY(-20px)', opacity: 0 },
                        { transform: 'translateY(5px)', opacity: 1 },
                        { transform: 'translateY(0)', opacity: 1 }
                    ],
                    { duration: 400, easing: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)' }
                ).onfinish = function() {
                    n.barDom.style.transform = 'translateY(0)';
                    n.barDom.style.opacity = 1;
                    promise(function(resolve) { resolve(); });
                };
            },
            close: function(promise) {
                var n = this;

                // Плавная анимация исчезновения с эффектом приподнятия
                n.barDom.animate(
                    [
                        { transform: 'translateY(0)', opacity: 1 },
                        { transform: 'translateY(-10px)', opacity: 0 }
                    ],
                    { duration: 300, easing: 'ease-out' }
                ).onfinish = function() {
                    promise(function(resolve) { resolve(); });
                };
            }
        }
    });

    // Добавление стилей для премиальной темы
    const styleTag = document.createElement('style');
    styleTag.textContent = `
        /* Базовый стиль для всех уведомлений */
        .noty_theme__premium.noty_bar {
            position: relative;
            margin: 12px 0;
            overflow: hidden;
            border-radius: 12px;
            background: rgba(14, 17, 22, 0.85);
            backdrop-filter: blur(10px);
            box-shadow:
                0 20px 25px -5px rgba(0, 0, 0, 0.3),
                0 10px 10px -5px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            width: 350px;
            transform: translateZ(0);
            min-height: 70px;
            will-change: transform, opacity;
        }

        /* Внутренний слой с геометрическим узором */
        .noty_theme__premium.noty_bar:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 15px 15px;
            pointer-events: none;
            z-index: 0;
        }

        /* Светящаяся верхняя граница */
        .noty_theme__premium.noty_bar:after {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg,
                rgba(255, 255, 255, 0),
                rgba(255, 255, 255, 0.2),
                rgba(255, 255, 255, 0));
            z-index: 1;
        }

        /* Внешний вид контейнера сообщения */
        .noty_theme__premium .noty_body {
            color: #f3f4f6;
            font-size: 0.95rem;
            line-height: 1.4;
            font-weight: 500;
            padding: 0;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.01em;
            position: relative;
            z-index: 2;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        /* Общий контейнер для содержимого с фоном типа сообщения и отступами */
        .noty_theme__premium .noty-content-container {
            display: flex;
            padding: 16px 20px;
            position: relative;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        /* Контейнер для иконки */
        .noty_theme__premium .noty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-right: 16px;
            position: relative;
        }

        /* Внутренний круг иконки с эффектом неоморфизма */
        .noty_theme__premium .noty-icon:before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            padding: 1px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.03));
            -webkit-mask: linear-gradient(#000, #000) content-box, linear-gradient(#000, #000);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }

        /* Стилизация иконки */
        .noty_theme__premium .noty-icon i {
            font-size: 1.2rem;
            color: white;
        }

        /* Контейнер текста */
        .noty_theme__premium .noty-text {
            flex-grow: 1;
            padding-right: 16px;
        }

        /* Заголовок с типом уведомления */
        .noty_theme__premium .noty-title {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        /* Текст сообщения */
        .noty_theme__premium .noty-message {
            font-size: 0.95rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.95);
        }

        /* Стиль для списка ошибок */
        .noty_theme__premium .noty-errors {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 150px;
            overflow-y: auto;
        }

        .noty_theme__premium .noty-errors li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .noty_theme__premium .noty-errors li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        /* Скролбар для списка ошибок */
        .noty_theme__premium .noty-errors::-webkit-scrollbar {
            width: 5px;
        }

        .noty_theme__premium .noty-errors::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .noty_theme__premium .noty-errors::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        /* Кнопка закрытия */
        .noty_theme__premium .noty_close_button {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.2);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
            backdrop-filter: blur(5px);
        }

        .noty_theme__premium .noty_close_button:hover {
            color: white;
            background: rgba(0, 0, 0, 0.4);
            transform: scale(1.1);
        }

        /* Прогресс-бар */
        .noty_theme__premium .noty_progressbar {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.15);
            z-index: 10;
        }

        /* Внутренний прогресс с анимированным градиентом */
        .noty_theme__premium .noty_progressbar_inner {
            height: 100%;
            background-size: 200% 200%;
            animation: gradientShift 2s linear infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Специфические стили для разных типов уведомлений */
        .noty_theme__premium.noty_type__success .noty-content-container {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05));
        }

        .noty_theme__premium.noty_type__success .noty-icon {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
        }

        .noty_theme__premium.noty_type__success .noty-title {
            color: #34d399;
        }

        .noty_theme__premium.noty_type__success .noty_progressbar_inner {
            background: linear-gradient(90deg, #059669, #10b981, #34d399, #10b981, #059669);
        }

        .noty_theme__premium.noty_type__error .noty-content-container {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05));
        }

        .noty_theme__premium.noty_type__error .noty-icon {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
        }

        .noty_theme__premium.noty_type__error .noty-title {
            color: #f87171;
        }

        .noty_theme__premium.noty_type__error .noty_progressbar_inner {
            background: linear-gradient(90deg, #dc2626, #ef4444, #f87171, #ef4444, #dc2626);
        }

        .noty_theme__premium.noty_type__warning .noty-content-container {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.05));
        }

        .noty_theme__premium.noty_type__warning .noty-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
        }

        .noty_theme__premium.noty_type__warning .noty-title {
            color: #fbbf24;
        }

        .noty_theme__premium.noty_type__warning .noty_progressbar_inner {
            background: linear-gradient(90deg, #d97706, #f59e0b, #fbbf24, #f59e0b, #d97706);
        }

        .noty_theme__premium.noty_type__info .noty-content-container {
            background: linear-gradient(135deg, rgba(0, 168, 255, 0.15), rgba(0, 168, 255, 0.05));
        }

        .noty_theme__premium.noty_type__info .noty-icon {
            background: linear-gradient(135deg, #00a8ff, #0288d1);
            box-shadow: 0 0 15px rgba(0, 168, 255, 0.4);
        }

        .noty_theme__premium.noty_type__info .noty-title {
            color: #38bdf8;
        }

        .noty_theme__premium.noty_type__info .noty_progressbar_inner {
            background: linear-gradient(90deg, #0288d1, #00a8ff, #38bdf8, #00a8ff, #0288d1);
        }

        /* Hover эффект */
        .noty_theme__premium.noty_bar:hover {
            transform: translateY(-2px);
            box-shadow:
                0 25px 30px -5px rgba(0, 0, 0, 0.4),
                0 15px 15px -5px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.07) inset;
            transition: all 0.3s ease;
        }

        /* СТИЛИ ДЛЯ ТЕМЫ MINT (КОПИЯ PREMIUM) */

        /* Базовый стиль для всех уведомлений */
        .noty_theme__mint.noty_bar {
            position: relative;
            margin: 12px 0;
            overflow: hidden;
            border-radius: 12px;
            background: rgba(14, 17, 22, 0.85);
            backdrop-filter: blur(10px);
            box-shadow:
                0 20px 25px -5px rgba(0, 0, 0, 0.3),
                0 10px 10px -5px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            width: 350px;
            transform: translateZ(0);
            min-height: 70px;
            will-change: transform, opacity;
        }

        /* Внутренний слой с геометрическим узором */
        .noty_theme__mint.noty_bar:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 15px 15px;
            pointer-events: none;
            z-index: 0;
        }

        /* Светящаяся верхняя граница */
        .noty_theme__mint.noty_bar:after {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg,
                rgba(255, 255, 255, 0),
                rgba(255, 255, 255, 0.2),
                rgba(255, 255, 255, 0));
            z-index: 1;
        }

        /* Внешний вид контейнера сообщения */
        .noty_theme__mint .noty_body {
            color: #f3f4f6;
            font-size: 0.95rem;
            line-height: 1.4;
            font-weight: 500;
            padding: 16px 20px;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.01em;
            position: relative;
            z-index: 2;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: flex-start;
        }

        /* Добавляем иконки для темы mint */
        .noty_theme__mint.noty_type__success .noty_body:before {
            content: '\\f00c'; /* Unicode для иконки проверки (check) */
            font-family: 'Font Awesome 5 Free', 'FontAwesome'; /* Поддержка разных версий */
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-right: 16px;
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
            color: white;
            font-size: 1.2rem;
        }

        .noty_theme__mint.noty_type__error .noty_body:before {
            content: '\\f00d'; /* Unicode для иконки X (times) */
            font-family: 'Font Awesome 5 Free', 'FontAwesome';
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-right: 16px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.4);
            color: white;
            font-size: 1.2rem;
        }

        .noty_theme__mint.noty_type__warning .noty_body:before {
            content: '\\f12a'; /* Unicode для иконки восклицательного знака */
            font-family: 'Font Awesome 5 Free', 'FontAwesome';
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-right: 16px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.4);
            color: white;
            font-size: 1.2rem;
        }

        .noty_theme__mint.noty_type__info .noty_body:before {
            content: '\\f129'; /* Unicode для иконки info */
            font-family: 'Font Awesome 5 Free', 'FontAwesome';
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-right: 16px;
            background: linear-gradient(135deg, #00a8ff, #0288d1);
            box-shadow: 0 0 15px rgba(0, 168, 255, 0.4);
            color: white;
            font-size: 1.2rem;
        }

        /* Кнопка закрытия */
        .noty_theme__mint .noty_close_button {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.2);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
            backdrop-filter: blur(5px);
        }

        .noty_theme__mint .noty_close_button:hover {
            color: white;
            background: rgba(0, 0, 0, 0.4);
            transform: scale(1.1);
        }

        /* Прогресс-бар */
        .noty_theme__mint .noty_progressbar {
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.15);
            z-index: 10;
        }

        /* Внутренний прогресс с анимированным градиентом */
        .noty_theme__mint .noty_progressbar > div {
            height: 100%;
            background-size: 200% 200%;
            animation: gradientShift 2s linear infinite;
        }

        /* Специфические стили для разных типов уведомлений */
        .noty_theme__mint.noty_type__success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05));
        }

        .noty_theme__mint.noty_type__success .noty_progressbar > div {
            background: linear-gradient(90deg, #059669, #10b981, #34d399, #10b981, #059669);
        }

        .noty_theme__mint.noty_type__error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05));
        }

        .noty_theme__mint.noty_type__error .noty_progressbar > div {
            background: linear-gradient(90deg, #dc2626, #ef4444, #f87171, #ef4444, #dc2626);
        }

        .noty_theme__mint.noty_type__warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.05));
        }

        .noty_theme__mint.noty_type__warning .noty_progressbar > div {
            background: linear-gradient(90deg, #d97706, #f59e0b, #fbbf24, #f59e0b, #d97706);
        }

        .noty_theme__mint.noty_type__info {
            background: linear-gradient(135deg, rgba(0, 168, 255, 0.15), rgba(0, 168, 255, 0.05));
        }

        .noty_theme__mint.noty_type__info .noty_progressbar > div {
            background: linear-gradient(90deg, #0288d1, #00a8ff, #38bdf8, #00a8ff, #0288d1);
        }

        /* Hover эффект */
        .noty_theme__mint.noty_bar:hover {
            transform: translateY(-2px);
            box-shadow:
                0 25px 30px -5px rgba(0, 0, 0, 0.4),
                0 15px 15px -5px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.07) inset;
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(styleTag);

    // Загрузка Font Awesome, если его нет
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
        document.head.appendChild(link);
    }

    // Функции для создания уведомлений с заголовками
    window.showNotification = function(message, type = 'info', timeout = 4000) {
        let iconClass = '';
        let title = '';

        // Определяем иконку и заголовок в зависимости от типа
        switch(type) {
            case 'success':
                iconClass = 'fas fa-check';
                title = 'Success';
                break;
            case 'error':
                iconClass = 'fas fa-times';
                title = 'Error';
                break;
            case 'warning':
                iconClass = 'fas fa-exclamation';
                title = 'Warning';
                break;
            case 'info':
            default:
                iconClass = 'fas fa-info';
                title = 'Information';
                break;
        }

        // Создаем HTML для уведомления с заголовком
        const html = `
            <div class="noty-content-container">
                <div class="noty-icon">
                    <i class="${iconClass}"></i>
                </div>
                <div class="noty-text">
                    <div class="noty-title">${title}</div>
                    <div class="noty-message">${message}</div>
                </div>
            </div>
        `;

        return new Noty({
            text: html,
            type: type,
            theme: 'premium',
            timeout: timeout,
            progressBar: true,
            closeWith: ['click', 'button']
        }).show();
    };

    window.showSuccessNotification = function(message, timeout = 4000) {
        return window.showNotification(message, 'success', timeout);
    };

    window.showErrorNotification = function(message, timeout = 4000) {
        return window.showNotification(message, 'error', timeout);
    };

    window.showWarningNotification = function(message, timeout = 4000) {
        return window.showNotification(message, 'warning', timeout);
    };

    window.showInfoNotification = function(message, timeout = 4000) {
        return window.showNotification(message, 'info', timeout);
    };

    // Функция для показа множественных ошибок
    window.showErrorsNotification = function(errors, timeout = 5000) {
        if (!Array.isArray(errors) || errors.length === 0) return;

        // Создаем HTML для списка ошибок
        let errorsHtml = '<div class="noty-content-container">';
        errorsHtml += `
            <div class="noty-icon">
                <i class="fas fa-times"></i>
            </div>
            <div class="noty-text">
                <div class="noty-title">Ошибки</div>
                <ul class="noty-errors">
        `;

        errors.forEach(function(error) {
            errorsHtml += `<li>${error}</li>`;
        });

        errorsHtml += `
                </ul>
            </div>
        </div>`;

        return new Noty({
            text: errorsHtml,
            type: 'error',
            theme: 'premium',
            timeout: timeout,
            progressBar: true,
            closeWith: ['click', 'button']
        }).show();
    };

    
    // Глобальная функция для тестирования Noty
    window.testNoty = function() {
        window.showSuccessNotification('Тест успешного уведомления');
        setTimeout(() => window.showErrorNotification('Тест ошибки'), 1000);
        setTimeout(() => window.showWarningNotification('Тест предупреждения'), 2000);
        setTimeout(() => window.showInfoNotification('Тест информационного сообщения'), 3000);
    };
}
