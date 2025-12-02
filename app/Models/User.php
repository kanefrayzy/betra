<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;


    protected $fillable = [
        'username', 'avatar',
        'user_id', 'email',
        'rank_id', 'currency_id',
        'password', 'balance', 'ref_balance',
        'ip', 'is_admin', 'is_moder',
        'is_youtuber', 'is_chat_moder', 'is_widthraw_moder', 'banchat',
        'fake', 'ban', 'affiliate_id',
        'referred_by', 'ref_money',
        'ref_money_history',
        'network_id', 'network_type', 'last_login_at', 'banned_until', 'payment_ban_at', 'ref_percentage',
        'wagering_requirement', 'wagered_amount', 'need_verify', 'aes_user_code',
        'telegram_id', 'first_name', 'last_name', 'photo_url', 'language_code',
        'total_games', 'total_wins', 'total_bets_amount', 'total_wins_amount'
    ];

    protected $hidden = ['password'];
    protected $dates = [
        'banned_until',
        'last_login_at',
        'payment_ban_at'
    ];


    protected static function boot(): void
    {
        parent::boot();

        static::updated(function ($user) {
            if ($user->isDirty('currency_id')) {
                $user->gameSession()->delete();
            }
        });
    }

    public function getAvatar(): string
    {
        if (!$this->avatar) {
            return '/assets/images/avatar-placeholder.png';
        }

        if (strtolower(substr($this->avatar, 0, 4)) === 'https') {
            return $this->avatar;
        }

        return asset($this->avatar());
    }

    public static function avatarUrl($path = ''): string
    {
        if (!$path) {
            return '/assets/images/avatar-placeholder.png';
        }

        if (strtolower(substr($path, 0, 4)) === 'https') {
            return $path;
        }

        return asset($path);
    }


    public function bonusLogs(): HasMany
    {
        return $this->hasMany(BonusLog::class, 'user_id', 'user_id');
    }


    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }

    public function gameSession(): HasOne
    {
        return $this->hasOne(GameSession::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by', 'user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by', 'user_id');
    }


    public function favoriteGames(): BelongsToMany
    {
        return $this->belongsToMany(SlotegratorGame::class, 'favorite_games', 'user_id', 'slotegrator_game_id')
            ->withTimestamps();
    }

    public function gamesHistory(): HasMany
    {
        return $this->hasMany(UserGameHistory::class);
    }

    public function getTotalWinsByCurrency($currencyCode)
    {
        switch ($currencyCode) {
            case 'AZN':
                return $this->total_win_AZN;
            case 'USD':
                return $this->total_win_USD;
            case 'RUB':
                return $this->total_win_RUB;
            case 'TRY':
                return $this->total_win_TRY;
            case 'KZT':
                return $this->total_win_KZT;
            default:
                return 0;
        }
      }

      public function bans()
      {
          return $this->hasMany(Ban::class);
      }

      public function isBanned()
      {
          if (!$this->banned_until) {
              return false;
          }

          // Преобразуем строку в объект Carbon, если это необходимо
          $bannedUntil = $this->banned_until instanceof Carbon
              ? $this->banned_until
              : Carbon::parse($this->banned_until);

          return $bannedUntil->isFuture();
      }

      public function hasActiveWagering(): bool
      {
          return $this->wagering_requirement > $this->wagered_amount;
      }

      public function getWageringProgressAttribute(): float
      {
          if ($this->wagering_requirement <= 0) {
              return 100;
          }
          return ($this->wagered_amount / $this->wagering_requirement) * 100;
      }

      public function addToWageringAmount(float $amount): void
      {
          $this->increment('wagered_amount', $amount);

          // Если отыгрыш завершен, обновляем статус бонуса
          if ($this->wagered_amount >= $this->wagering_requirement) {
              $activeBonus = json_decode($this->active_bonuses, true);
              if ($activeBonus) {
                  UserDepositBonus::where('id', $activeBonus['id'])
                      ->update(['completed_at' => now()]);
              }
              $this->active_bonuses = null;
              $this->wagering_requirement = 0;
              $this->wagered_amount = 0;
              $this->save();
          }
      }

      public function verification()
      {
          return $this->hasOne(UserVerification::class);
      }

      public function isVerified(): bool
      {
          return $this->verification && $this->verification->status === 'approved';
      }

      public function coinFlipGames()
      {
          return $this->hasMany(CoinFlipGame::class);
      }

    public function depositBonuses()
    {
        return $this->hasMany(UserDepositBonus::class, 'user_id');
    }

    public function cryptoWallets(): HasMany
    {
        return $this->hasMany(UserCryptoWallet::class);
    }


}
