<?php

namespace App\Interfaces;

interface PaymentSystemInterface
{
    public function createOrder($orderId, $amount, $currency, $systemId);
}
