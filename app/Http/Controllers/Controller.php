<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class Controller extends BaseController
{
    public  $redis;
}
