<?php


namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
