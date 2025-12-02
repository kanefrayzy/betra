<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentHandler;
use App\Models\PaymentSystem;
use Illuminate\Http\Request;

class PaymentHandlerController extends Controller
{
    public function index()
    {
        $paymentHandlers = PaymentHandler::with('system')->paginate(30);
        return view('admin.payment_handlers.index', compact('paymentHandlers'));
    }

    public function create()
    {
        $paymentSystems = PaymentSystem::all();
        $currencies = ['USD', 'EUR', 'RUB', 'BTC', 'ETH', 'AZN', 'KZT', 'TRY', 'UZS', 'PLN'];
        return view('admin.payment_handlers.create', compact('paymentSystems', 'currencies'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:55',
            'payment_system_id' => 'required|exists:payment_systems,id',
            'currency' => 'required|string|max:10',
            'withdrawal_fee' => 'nullable|numeric|min:0',
            'deposit_fee' => 'nullable|numeric|min:0',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'min_withdrawal_limit' => 'nullable|numeric|min:0',
            'max_withdrawal_limit' => 'nullable|numeric|min:0',
            'min_deposit_limit' => 'nullable|numeric|min:0',
            'max_deposit_limit' => 'nullable|numeric|min:0',
            'active' => 'required|boolean',
            'withdrawal_mode' => 'nullable|in:manual,semi_auto,instant',
            'auto_withdrawal_enabled' => 'boolean',
            'daily_auto_withdrawal_limit' => 'nullable|numeric|min:0',
            'require_admin_approval' => 'boolean'
        ]);

        // Устанавливаем дефолтные значения для полей автовыплат
        $validatedData['withdrawal_mode'] = $validatedData['withdrawal_mode'] ?? 'manual';
        $validatedData['auto_withdrawal_enabled'] = $validatedData['auto_withdrawal_enabled'] ?? false;
        $validatedData['require_admin_approval'] = $validatedData['require_admin_approval'] ?? true;

        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('payment_handlers', 'public');
            $validatedData['icon'] = $iconPath;
        }

        PaymentHandler::create($validatedData);

        return redirect()->route('admin.payment_handlers.index')
            ->with('success', __('Обработчик платежей успешно добавлен.'));
    }

    public function edit(PaymentHandler $paymentHandler)
    {
        $paymentSystems = PaymentSystem::all();
        $currencies = ['USD', 'EUR', 'RUB', 'BTC', 'ETH', 'AZN', 'KZT', 'TRY', 'UZS', 'PLN'];
        return view('admin.payment_handlers.edit', compact('paymentHandler', 'paymentSystems', 'currencies'));
    }

    public function update(Request $request, PaymentHandler $paymentHandler)
    {
        $validatedData = $request->validate([
            'payment_system_id' => 'required|exists:payment_systems,id',
            'currency' => 'required|string|max:10',
            'withdrawal_fee' => 'nullable|numeric|min:0',
            'deposit_fee' => 'nullable|numeric|min:0',
            'url' => 'nullable|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'min_withdrawal_limit' => 'nullable|numeric|min:0',
            'max_withdrawal_limit' => 'nullable|numeric|min:0',
            'min_deposit_limit' => 'nullable|numeric|min:0',
            'max_deposit_limit' => 'nullable|numeric|min:0',
            'active' => 'required|boolean',
            'withdrawal_mode' => 'nullable|in:manual,semi_auto,instant',
            'auto_withdrawal_enabled' => 'boolean',
            'daily_auto_withdrawal_limit' => 'nullable|numeric|min:0',
            'require_admin_approval' => 'boolean'
        ]);

        // Устанавливаем дефолтные значения для полей автовыплат (если не переданы)
        $validatedData['withdrawal_mode'] = $validatedData['withdrawal_mode'] ?? $paymentHandler->withdrawal_mode ?? 'manual';
        $validatedData['auto_withdrawal_enabled'] = $validatedData['auto_withdrawal_enabled'] ?? $paymentHandler->auto_withdrawal_enabled ?? false;
        $validatedData['require_admin_approval'] = $validatedData['require_admin_approval'] ?? $paymentHandler->require_admin_approval ?? true;

        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('payment_handlers', 'public');
            $validatedData['icon'] = $iconPath;
        }

        $paymentHandler->update($validatedData);

        return redirect()->route('admin.payment_handlers.index')
            ->with('success', __('Обработчик платежей успешно обновлен.'));
    }

    public function destroy(PaymentHandler $paymentHandler)
    {
        $paymentHandler->delete();
        return redirect()->route('admin.payment_handlers.index')
            ->with('success', __('Обработчик платежей успешно удален.'));
    }
}
