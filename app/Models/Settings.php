<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

    protected $fillable = [
        'domain', 'sitename',
        'title', 'desc', 'keys',
         'min_rain_amount', 'max_rain_amount', 'min_rain_count', 'max_rain_count', 'min_rain_user_oborot',
         'chat_mess_support', 'chat_status', 'ip_maintenance', 'text_maintenance','withdrawal_commission', 'support_tg',
         'welcome_bonus_enabled', 'welcome_bonus_amount'
    ];
    
    protected $attributes = [
        'support_tg' => '',
        'sitename' => '',
        'domain' => '',
        'title' => '',
        'desc' => '',
        'keys' => '',
        'chat_status' => 1,
        'welcome_bonus_enabled' => false,
        'welcome_bonus_amount' => 0,
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at'
    // ];

    public static function get()
    {
        return Settings::first();
    }

}
