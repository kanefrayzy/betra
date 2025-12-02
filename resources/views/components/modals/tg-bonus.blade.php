@auth
<div class="modal" id="tg-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Доступ к чату') }}</h5>
                <button onclick="closeTgModal()" type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="chat-access">
                    <h2 class="chat-access__title">{{ __('Требования для доступа к чату') }}</h2>
                    <p class="chat-access__description">
                        {{ __('Для отправки сообщений в чат необходимо выполнить одно из следующих условий:') }}
                    </p>
                    <ul class="chat-access__list">
                        <li>{{ __('Подтвердить свой аккаунт Telegram в настройках и подпищитесь на нашу группу.') }}<br> <a href="https://t.me/{{ $settings->support_tg }}" target="_blank" style="color: #63cbff;font-size: 20px;font-weight: bold;"><i class="fa-brands fa-telegram" aria-hidden="true"></i> Flash</a></li>
                        <div class="chat-access__actions">
                            <a href="{{ route('account') }}" class="chat-access__button chat-access__button--telegram">
                                {{ __('Подтвердить Telegram') }}
                            </a>
                        </div>
                        <li>{{ __('Достичь 1-го уровня на Flash') }}</li>
                        <div alt="rank" class="rank-container2">
                            <img src="/storage/ranks/ogg6kZbrK4YHIyMUIISxeLxokPW5V3hpupMbbWDC.png" alt="" class="img-fluid">
                            <div class="rank-name2">1</div>
                        </div>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</div>
@endauth
