<div id="winning-info-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <div class="profile-header">
            <div class="profile-info">
                <div id="winning-title" class="user-name">{{ __('Ставка') }}</div>
            </div>
        </div>
        <div class="profile-stats">
            <div class="stat">
                <div id="game-name" class="stat-value large">000</div>
                <div class="stat-label">#<span id="winning-id">123</span></div>
                <div class="stat-label">{{ __('Дата') }} <span id="username" class="username">login</span></div>
                <div class="stat-label" id="winning-date">01.01.1970 00:00</div>
            </div>
            <div class="stat">
                <div class="stat-label">{{ __('Ставка') }}</div>
                <div id="bet-amount" class="stat-value">100 <span class="currency-icon">₽</span></div>
            </div>
            <div class="stat">
                <div class="stat-label">{{ __('Коэффициент') }}</div>
                <div class="stat-value"><span id="coefficient">1</span> x</div>
            </div>
            <div class="stat">
                <div class="stat-label">{{ __('Выплата') }}</div>
                <div id="win-amount" class="stat-value">100 <span class="currency-icon">₽</span></div>
            </div>
        </div>
        <div class="profile-actions">
              <a href="#" id="play-game-button" class="btn play-button">{{ __('Играть в') }} <span id="game-link-name"></span></a>
        </div>
    </div>
</div>

<style>


#winning-info-modal .close-button {
    color: #fff;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

#winning-info-modal .profile-header {
    text-align: center;
    margin-bottom: 20px;
}

#winning-info-modal .user-name {
    font-size: 24px;
    font-weight: bold;
}

#winning-info-modal .profile-stats {
    display: flex;
    flex-direction: column;
}

#winning-info-modal .stat {
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}

#winning-info-modal .stat-label {
    font-size: 14px;
    color: #8b949e;
}

#winning-info-modal .stat-value {
    font-size: 18px;
    font-weight: bold;
    margin-top: 5px;
}

#winning-info-modal .large {
    font-size: 20px;
}

#winning-info-modal .username {
    color: #00bfff;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    display: block;
    margin: 5px auto;
}
#winning-info-modal .play-button {
    background-color: #00bfff;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
}

#winning-info-modal .play-button:hover {
    background-color: #008fcc;
}
</style>
