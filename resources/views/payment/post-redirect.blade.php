<!DOCTYPE html>
<html>
<head>
    <title>Redirect...</title>
</head>
<body>
    <form id="payment-form" method="POST" action="{{ $url }}">
        @foreach($fields as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>
        document.getElementById('payment-form').submit();
    </script>
</body>
</html>
