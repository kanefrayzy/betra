<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */
 // if ($_SERVER['HTTP_HOST'] !== 'teybet2.com' && $_SERVER['HTTP_HOST'] !== 'www.teybet2.com') {
 //     $protocol = "https";
 //     $newUrl = $protocol . "://teybet2.com" . $_SERVER['REQUEST_URI'];
 //     header("Location: " . $newUrl);
 //     exit();
 // }

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance</title>
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            padding: 20px;
            border: 2px solid #ffffff;
            border-radius: 10px;
            background-color: #1e1e1e;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }
        .footer {
            font-size: 0.9em;
            color: #aaaaaa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Maintenance in Progress</h1>
        <p>We are currently performing scheduled maintenance. We should be back online shortly.</p>
        <p>Thank you for your patience.</p>
        <div class="footer">&copy; 2024 Flash. All rights reserved.</div>
    </div>
</body>
</html>

*/

require __DIR__.'/../vendor/autoload.php';

//require_once __DIR__ . "/../vendor/PaykassaSCI.php";

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/
// $ipp = request()->ip();
// echo $_SERVER['SERVER_ADDR'];

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
