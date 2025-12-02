<!DOCTYPE html>
<html>
<head>
    <title>Closing Game</title>
    <script>
        // Закрываем окно или возвращаемся на главную
        if (window.opener) {
            window.close();
        } else {
            window.location.href = "{{ route('home') }}";
        }
    </script>
</head>
<body>
    <p>Closing game...</p>
</body>
</html>
