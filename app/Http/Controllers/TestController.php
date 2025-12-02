<?php

namespace App\Http\Controllers;




use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {

    }

    public function messages()
    {
        $messages = ['Mess 1', 'Mess 2', 'Mess 3', 'Mess 4', 'Mess 5'];

        return $messages;
    }

    public function sendMessage(Request $request)
    {

    }
}
