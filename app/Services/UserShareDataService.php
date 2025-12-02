<?php

namespace App\Services;

use App\Models\User;


class UserShareDataService
{

    public static function getRef(User $user): ?array
    {
        return static::calculateRef($user->affiliate_id) ?? null;
    }

    protected static function calculateRef($affiliate_id): array
    {
        $refCount = User::where('referred_by', $affiliate_id)->count();
        return static::getLevelData($refCount, self::REFERRAL_LEVELS);
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
