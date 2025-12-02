<x-layouts.app>
    <style>
        .card-bonus {
            background: #212b40;
            border-radius: 15px;
            padding: 20px;
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            margin: 20px 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
        }
        .card-bonus:hover {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        }
        .card-bonus-header {
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #00d1b2;
            padding-bottom: 10px;
        }
        .card-bonus-body p {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #b3b3b3;
        }
        .btn-primary {
            background-color: #2196f3;
            border-color: #2196f3;
            font-size: 1.1rem;
            padding: 12px 24px;
            border-radius: 25px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            display: block;
            margin: 20px auto;
            margin-bottom: 0;
            max-width: 300px;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #00a8a0;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .btn-secondary {
            background-color: #3273dc;
            border-color: #3273dc;
            color: #ffffff;
        }
        .btn-secondary:hover {
            background-color: #2366d1;
        }
    </style>
    <div class="card-bonus">
        <div class="card-bonus-header">{{ __('Бонус за подписку на Telegram группу') }}</div>
        <div class="card-bonus-body">
            <p>{{ __('Подпишитесь на нашу Telegram группу и получите бонус в размере') }} <b style="color:#0af">{{ moneyFormat(toUSD(0.588, $u->currency->symbol))}} {{$u->currency->symbol}}</b></p>
            @if(!$user->telegram_id)
                <p>{{ __('Сначала привяжите свой Telegram аккаунт:') }}</p>
                <a href="{{ route('telegram.connect') }}" target="_blank" class="btn btn-primary"><i class="fa fa-telegram"></i> {{ __('Привязать Telegram') }}</a>
            @elseif(!$isSubscribed)
                <p>{{ __('Подпишитесь на нашу группу, чтобы получить бонус:') }}</p>
                <a href="{{ $groupLink }}" target="_blank" class="btn btn-primary"><i class="fa fa-telegram"></i> {{ __('Подписаться на группу') }}</a>
            @else
                <form action="{{ route('telegram-bonus.claim') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary"> {{ __('Получить бонус') }} <i class="fa-solid fa-chevrons-right"></i></button>
                      <a onclick="openModal('tg-modal')" class="btn btn-primary"> {{ __('Получить бонус2') }} <i class="fa-solid fa-chevrons-right"></i></a>
                </form>
            @endif
        </div>
    </div>
</x-layouts.app>
