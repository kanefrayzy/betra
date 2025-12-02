<?php

namespace App\Factories;

use App\Interfaces\PaymentSystemInterface;
use App\Services\PayKassaService;
use App\Services\FreeKassaService;

class PaymentSystemFactory
{
    public static function create($system): PaymentSystemInterface
    {
        return match ($system) {
            'paykassa' => app(PayKassaService::class),
            'freekassa' => app(FreeKassaService::class),
            default => throw new \InvalidArgumentException('Неподдерживаемая платежная система'),
        };
    }
}
