<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSystem;
use Illuminate\Http\Request;

class PaymentSystemController extends Controller
{
    public function index()
    {
        $paymentSystems = PaymentSystem::all();
        return view('admin.payment_systems.index', compact('paymentSystems'));
    }

    public function create()
    {
        return view('admin.payment_systems.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'merchant_id' => 'required|string|max:255',
            'merchant_secret_1' => 'required|string|max:255',
            'merchant_secret_2' => 'nullable|string|max:255',
            'active' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $imagePath = $request->file('logo')->store('payment_systems', 'public');
            $validatedData['logo'] = $imagePath;
        }

        PaymentSystem::create($validatedData);

        return redirect()->route('admin.payment_systems.index')->with('success', 'Payment system added successfully.');
    }

    public function edit(PaymentSystem $paymentSystem)
    {
        return view('admin.payment_systems.edit', compact('paymentSystem'));
    }

    public function update(Request $request, PaymentSystem $paymentSystem)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'merchant_id' => 'required|string|max:255',
            'merchant_secret_1' => 'required|string|max:255',
            'merchant_secret_2' => 'nullable|string|max:255',
            'active' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $imagePath = $request->file('logo')->store('payment_systems', 'public');
            $validatedData['logo'] = $imagePath;
        }

        $paymentSystem->update($validatedData);

        return redirect()->route('admin.payment_systems.index')->with('success', 'Payment system updated successfully.');
    }

    public function destroy(PaymentSystem $paymentSystem)
    {
        $paymentSystem->delete();
        return redirect()->route('admin.payment_systems.index')->with('success', 'Payment system deleted successfully.');
    }
}
