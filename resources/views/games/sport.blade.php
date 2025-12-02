<x-layouts.app>


    <div class="play-slot">
        <div class="iframe-container">
            <div class="preloader" id="preloader">
                <img width="200" src="/assets/images/loader.gif" alt="Loading...">
            </div>
            <iframe id="gameIframe" src="{{ $gameUrl }}" onload="hidePreloader()"></iframe>
        </div>
        <div class="frame-buttons">
            <a href="#" onclick="toggleFullscreen()"><i class="fa-sharp fa-regular fa-expand"></i></a>
        </div>
    </div>
    <script>
    // Сохраняем URL игры в localStorage
    localStorage.setItem('gameUrl', initialGameUrl);

    function loadGame() {
        if (gameLoaded) return; // Предотвращаем повторную загрузку игры

        var iframe = document.getElementById('gameIframe');
        var gameUrl = localStorage.getItem('gameUrl');
        if (gameUrl) {
            iframe.src = gameUrl;
            gameLoaded = true;
        }
    }


    let isFullscreen = false;

    function toggleFullscreen() {
        const container = document.querySelector('.play-slot');
        const icon = document.querySelector('.frame-buttons i');

        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.mozRequestFullScreen) {
                container.mozRequestFullScreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
            isFullscreen = true;
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            isFullscreen = false;
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }

    function handleFullscreenChange() {
        const icon = document.querySelector('.frame-buttons i');
        if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
            isFullscreen = true;
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            isFullscreen = false;
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }

    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);

    function hidePreloader() {
        const preloader = document.getElementById('preloader');
        const iframe = document.getElementById('gameIframe');
        if (preloader) {
            preloader.style.display = 'none';
        }
        if (iframe) {
            iframe.style.display = 'block';
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        loadGame(); // Загружаем игру при первой загрузке страницы

        const modeSwitch = document.querySelector('.switch input');
        if (modeSwitch) {
            modeSwitch.checked = !isDemoMode;
            modeSwitch.addEventListener('change', switchGameMode);
        }

        updateModeText();

        const iframe = document.getElementById('gameIframe');
        if (iframe) {
            iframe.addEventListener('load', function() {
                hidePreloader();
                gameLoaded = true; // Устанавливаем флаг, что игра загружена
            });
        }
    });

    // Обработчик события popstate (нажатие кнопки "назад")
    window.addEventListener('popstate', function(event) {
        if (!gameLoaded) {
            loadGame();
        }
    });

    // Предотвращаем стандартное поведение браузера при нажатии кнопки "назад"
    history.pushState(null, null, location.href);
    window.onpopstate = function(event) {
        history.go(1);
    };


    // Предотвращаем перезагрузку iframe при клике на другие элементы
    document.addEventListener('click', function(event) {
        if (event.target.tagName === 'A' && event.target.getAttribute('onclick')) {
            event.preventDefault();
            const functionCall = event.target.getAttribute('onclick');
            if (functionCall) {
                eval(functionCall);
            }
        }
    });
    </script>
  {{--  <x-layouts.partials.footer/> --}}
</x-layouts.app>
