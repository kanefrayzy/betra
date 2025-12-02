<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalRequest;
use App\Models\Withdrawal;

class WithdrawalController extends Controller
{
    public function index()
    {
        return Withdrawal::all();
    }

    public function store(WithdrawalRequest $request)
    {
        return Withdrawal::create($request->validated());
    }

    public function show(Withdrawal $withdrawal)
    {
        return $withdrawal;
    }

    public function update(WithdrawalRequest $request, Withdrawal $withdrawal)
    {
        $withdrawal->update($request->validated());

        return $withdrawal;
    }

    public function destroy(Withdrawal $withdrawal)
    {
        $withdrawal->delete();

        return response()->json();
    }
}
