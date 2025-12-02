<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('home.password_reset_layout.subject') }}</title>
    <style type="text/css">
        body {
            font-family: "Nunito Sans", Helvetica, Arial, sans-serif;
            background-color: #F2F4F6;
            color: #51545E;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: 100%;
        }
        .email-wrapper {
            width: 100%;
            background-color: #F2F4F6;
            padding: 0;
        }
        .email-content {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .email-body {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .email-body_inner {
            width: 100%;
            max-width: 570px;
            margin: 0 auto;
            padding: 0;
            background-color: #FFFFFF;
            box-sizing: border-box;
        }
        .content-cell {
            padding: 20px;
            box-sizing: border-box;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            color: #333333;
            margin-top: 0;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            line-height: 1.625;
            margin: .4em 0 1.1875em;
        }
        .button {
            background-color: #3869D4;
            border-top: 12px solid #3869D4;
            border-right: 20px solid #3869D4;
            border-bottom: 12px solid #3869D4;
            border-left: 20px solid #3869D4;
            display: inline-block;
            color: #FFF;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
            -webkit-text-size-adjust: none;
            box-sizing: border-box;
            text-align: center;
            font-size: 18px;
            padding: 15px 25px;
        }

        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-body_inner {
                width: 100% !important;
                padding: 10px !important;
            }
            .content-cell {
                padding: 15px !important;
            }
            h1 {
                font-size: 22px !important;
            }
            p {
                font-size: 16px !important;
            }
            .button {
                width: 100% !important;
                text-align: center !important;
                border: none !important;
                border-radius: 5px !important;
                padding: 15px !important;
                font-size: 18px !important;
            }
        }
    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td class="email-body">
                            <table class="email-body_inner" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="content-cell">
                                        <h1>{{ __('home.password_reset_layout.greeting') }}</h1>
                                        <p>{{ __('home.password_reset_layout.message') }}</p>
                                        <p><a href="{{ $resetLink }}" class="button">{{ __('home.password_reset_layout.button') }}</a></p>
                                        <p>{{ __('home.password_reset_layout.ignore') }}</p>
                                        <p>{{ __('home.password_reset_layout.regards') }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <p class="f-fallback sub align-center">
                                {{ __('home.password_reset_layout.footer', ['year' => date('Y')]) }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
