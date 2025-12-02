<?php

namespace App\Services;

use App\Models\User;


class UserShareDataService
{

    const array REFERRAL_LEVELS = [
        ['min' => 0, 'max' => 10, 'lvl' => 1, 'perc' => 0.5],
        ['min' => 10, 'max' => 100, 'lvl' => 2, 'perc' => 0.7],
        ['min' => 100, 'max' => 500, 'lvl' => 3, 'perc' => 1.0],
        ['min' => 500, 'max' => PHP_INT_MAX, 'lvl' => 4, 'perc' => 1.5]
    ];

    const array INS_LEVELS = [
        ['min' => 0, 'max' => 100, 'lvl' => 0, 'perc' => 1.0],
        ['min' => 100, 'max' => 1000, 'lvl' => 1, 'perc' => 1.0],
        ['min' => 1000, 'max' => 10000, 'lvl' => 2, 'perc' => 1.2],
        ['min' => 10000, 'max' => 100000, 'lvl' => 3, 'perc' => 1.5],
        ['min' => 100000, 'max' => 500000, 'lvl' => 4, 'perc' => 2.0],
        ['min' => 500000, 'max' => PHP_INT_MAX, 'lvl' => 5, 'perc' => 2.5]
    ];

    public static function getRef(User $user): ?array
    {
        return static::calculateRef($user->affiliate_id) ?? null;
    }

    public static function getIns(User $user): ?array
    {
        return static::calculateIns($user) ?? null;
    }

    protected static function calculateRef($affiliate_id): array
    {
        $refCount = User::where('referred_by', $affiliate_id)->count();
        return static::getLevelData($refCount, self::REFERRAL_LEVELS);
    }

    protected static function calculateIns(User $user): array
    {
        $ins = $user->oborot;
        $data = static::getLevelData($ins, self::INS_LEVELS);
        $data['width'] = min(($ins / $data['max']) * 100, 100);
        return $data;
    }

    private static function getLevelData(int $count, array $levels): array
    {
        foreach ($levels as $level) {
            if ($count >= $level['min'] && $count < $level['max']) {
                return [
                    'count' => $count,
                    'lvl' => $level['lvl'],
                    'perc' => $level['perc'],
                    'max' => $level['max']
                ];
            }
        }
        return [];
    }
}
