<!-- Social Login Buttons Component -->
<style>
/* Переопределяем стили uLogin */
#uLogin .ulogin-buttons-container,
#uLogin2 .ulogin-buttons-container {
    display: flex !important;
    gap: 12px !important;
    justify-content: center !important;
    flex-wrap: wrap !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: normal !important;
}

/* Скрываем все кнопки по умолчанию */
#uLogin .ulogin-buttons-container > div,
#uLogin2 .ulogin-buttons-container > div {
    display: none !important;
}

/* Показываем и стилизуем только Google и Steam */
#uLogin .ulogin-button-google,
#uLogin2 .ulogin-button-google,
#uLogin .ulogin-button-steam,
#uLogin2 .ulogin-button-steam {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 12px !important;
    width: auto !important;
    height: auto !important;
    margin: 0 !important;
    padding: 12px 16px !important;
    border-radius: 12px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    position: relative !important;
    overflow: hidden !important;
    float: none !important;
    flex: 1 !important;
    min-width: 140px !important;
    background-position: center !important;
    background-size: 0 !important;
}

#uLogin .ulogin-button-google,
#uLogin2 .ulogin-button-google {
    background: white !important;
    color: #1f2937 !important;
    border: 1px solid #e5e7eb !important;
}

#uLogin .ulogin-button-google:hover,
#uLogin2 .ulogin-button-google:hover {
    background: #f9fafb !important;
    transform: scale(1.05) !important;
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15) !important;
}

#uLogin .ulogin-button-steam,
#uLogin2 .ulogin-button-steam {
    background: linear-gradient(135deg, #171a21 0%, #1b2838 100%) !important;
    color: white !important;
    border: 1px solid #2a475e !important;
}

#uLogin .ulogin-button-steam:hover,
#uLogin2 .ulogin-button-steam:hover {
    background: linear-gradient(135deg, #1b2838 0%, #171a21 100%) !important;
    transform: scale(1.05) !important;
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3) !important;
}

/* Добавляем иконки через CSS */
#uLogin .ulogin-button-google::before,
#uLogin2 .ulogin-button-google::before,
#uLogin .ulogin-button-steam::before,
#uLogin2 .ulogin-button-steam::before {
    content: '';
    width: 20px !important;
    height: 20px !important;
    background-size: contain !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    flex-shrink: 0 !important;
    display: block !important;
}

#uLogin .ulogin-button-google::before,
#uLogin2 .ulogin-button-google::before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%234285F4' d='M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z'/%3E%3Cpath fill='%2334A853' d='M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z'/%3E%3Cpath fill='%23FBBC05' d='M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z'/%3E%3Cpath fill='%23EA4335' d='M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z'/%3E%3C/svg%3E") !important;
}

#uLogin .ulogin-button-steam::before,
#uLogin2 .ulogin-button-steam::before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z'/%3E%3C/svg%3E") !important;
}

/* Добавляем текст через CSS */
#uLogin .ulogin-button-google::after,
#uLogin2 .ulogin-button-google::after {
    content: 'Google' !important;
    color: #1f2937 !important;
}

#uLogin .ulogin-button-steam::after,
#uLogin2 .ulogin-button-steam::after {
    content: 'Steam' !important;
    color: white !important;
}

/* Анимация при клике */
#uLogin .ulogin-button-google:active,
#uLogin2 .ulogin-button-google:active,
#uLogin .ulogin-button-steam:active,
#uLogin2 .ulogin-button-steam:active {
    transform: scale(0.95) !important;
}
</style>


