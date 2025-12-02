<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=REM:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/sport.css?v2">
    <script src="https://kit.fontawesome.com/4e6bda1a43.js" crossorigin="anonymous"></script>
    <title>Flash</title>
</head>
<body>
    <header class="header">
        <a href="/" class="head-logo pc">
            <img class="logo" src="/assets/images/logo.png" width="170">
        </a>
        <div class="top-bar">
            <div class="pc">&nbsp;</div>
            <a href="/" class="head-logo mob">
                <img class="logo" src="/assets/images/logo.png" width="170">
            </a>
            <livewire:balance/>
            <form id="currencyForm" method="POST" action="{{route('account.change-currency', ['currency' => 'USD'])}}" style="display: none;">
                @csrf
                <input type="hidden" name="currency" id="currencyInput" value="USD">
            </form>
            <livewire:notifications/>
        </div>
    </header>
    <div class="iframe-container" style="background: #131a28">
        <div class="preloader" id="preloader">
            <img width="200" src="/assets/images/loader.gif" alt="Loading...">
        </div>
        <iframe id="gameIframe" src="{{ $gameUrl }}" allowfullscreen></iframe>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var iframe = document.getElementById('gameIframe');
            var preloader = document.getElementById('preloader');

            function hidePreloader() {
                preloader.style.display = 'none';
                iframe.style.display = 'block';
                adjustIframeSize();
            }

            function adjustIframeSize() {
                var container = document.querySelector('.iframe-container');
                var iframe = document.getElementById('gameIframe');
                var windowHeight = window.innerHeight;
                var windowWidth = window.innerWidth;
                var headerHeight = 57; // Высота шапки
                var additionalTopOffset = 9;
                var topOffset = headerHeight + additionalTopOffset;
                if (windowWidth <= 768) {
                    topOffset = headerHeight;
                }
                var containerHeight = windowHeight - topOffset;
                container.style.top = topOffset + 'px';
                container.style.height = containerHeight + 'px';
                iframe.style.height = containerHeight + 'px';
            }

            iframe.addEventListener('load', hidePreloader);


            adjustIframeSize();

            window.addEventListener('resize', adjustIframeSize);

            window.addEventListener('orientationchange', () => {
                setTimeout(adjustIframeSize, 100); 
            });
        });
    </script>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .iframe-container {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            background-color: #131a28;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: none;
        }
        .preloader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        @media (max-width: 768px) {
            .iframe-container {
                top: 57px;
            }
            .footer {
                display: none;
            }
        }
        .mobile-menu {
            display: none;
        }
    </style>
</body>
</html>
