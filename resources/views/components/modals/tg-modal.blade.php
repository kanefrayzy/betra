<div class="modal" id="tg-slider">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Подписка на Telegram канал') }}</h5>
                <button onclick="closeTgModal()" type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="chat-access">
                    <p class="chat-access__description">
                        {{ __('Выполните следующие шаги, чтобы получить ежедневный бонус:') }}
                    </p>
                    <ul class="chat-access__list">
                        <li>{{ __('Подтвердите свой аккаунт Telegram в настройках профиля') }}</li>
                        <li>{{ __('Подпишитесь на наш Telegram канал') }}</li>
                    </ul>
                    <div class="chat-access__actions">
                        <a href="{{ route('account') }}" class="chat-access__button chat-access__button--telegram">
                            {{ __('Подтвердить Telegram') }}
                        </a>
                        <a href="https://t.me/{{ $settings->support_tg }}" target="_blank" class="chat-access__button chat-access__button--info">
                            <i class="fa-brands fa-telegram" aria-hidden="true"></i> {{ __('Подписаться на канал') }}
                        </a>
                    </div>
                    <p class="chat-access__note">
                        {{ __('После выполнения всех шагов вы начнете получать ежедневный бонус на баланс!') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#tg-slider .modal-header {
    border-bottom: none;
    padding: 20px 20px 0;
}

#tg-slider .modal-title {
    color: #FFFFFF;
    font-size: 1.5rem;
    font-weight: bold;
}

#tg-slider .close {
    color: #FFFFFF;
    opacity: 1;
}

#tg-slider .modal-body {
    padding: 20px;
}

.chat-access__title {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 15px;
    color: #00E8BA;
}

.chat-access__description {
    font-size: 1rem;
    margin-bottom: 20px;
    color: #B3B3B3;
}

.chat-access__list {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 20px;
}

.chat-access__list li {
    padding-left: 25px;
    position: relative;
    margin-bottom: 10px;
    color: #B3B3B3;
}

.chat-access__list li:before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #00E8BA;
}

.chat-access__actions {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-top: 20px;
}

.chat-access__button {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 3px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease;
    margin-bottom: 15px;
    text-align: center;
    width: 100%;
}

.chat-access__button--telegram {
    background-color: #0088CC;
    color: white;
}

.chat-access__button--telegram:hover {
    background-color: #0099E6;
    color: white;
}

.chat-access__button--info {
    background-color: #F14668;
    color: white;
}

.chat-access__button--info:hover {
    background-color: #F25D7D;
    color: white;
}

.chat-access__note {
    margin-top: 20px;
    font-size: 0.9rem;
    color: #00E8BA;
    text-align: center;
}
</style>
