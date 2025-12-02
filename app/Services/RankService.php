<?php

namespace App\Services;

use App\Models\Rank;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class RankService
{
    public static function progress(User $user)
    {
        // Кэширование рангов на 1 день
        $ranks = Cache::remember('all_ranks', 86400, function () {
            return Rank::all();
        });

        $currentRank = $user->rank;
        if (!$currentRank) {
            return [
                'oborot' => moneyFormat($user->oborot),
                'ranks' => $ranks,
                'current_rank' => $user->rank ?? null,
                'next_rank' => null,
                'percentage' => 0,
                'remaining_range' => 0,
                'max_rank_message' => __('Ранг не определен'),
            ];
        }

        $nextRank = $ranks->where('id', '>', $user->rank->id)->first();
        if (!$nextRank) {
            return [
                'oborot' => $user->oborot,
                'ranks' => $ranks,
                'current_rank' => $user->rank,
                'next_rank' => null,
                'percentage' => 100,
                'remaining_range' => 0,
                'max_rank_message' => __('Вы достигли максимального ранга'),
            ];
        }

        $currentRange = $user->oborot - $user->rank->oborot_min;
        $totalRange = $nextRank->oborot_min - $user->rank->oborot_min;
        $percentage = 0;
        if ($totalRange > 0) {
            $percentage = ($currentRange / $totalRange) * 100;
        }
        $remainingRange = $nextRank->oborot_min - $user->oborot;

        return [
            'oborot' => $user->oborot,
            'ranks' => $ranks,
            'current_rank' => $user->rank,
            'next_rank' => $nextRank,
            'percentage' => round($percentage, 2),
            'remaining_range' => $remainingRange,
            'max_rank_message' => null,
        ];
    }
}
