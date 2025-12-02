<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPromocodeController extends Controller
{
    public function index()
    {
        $promocodes = Promocode::withCount('claims')
            ->latest()
            ->paginate(20);

        return view('admin.promo.index', compact('promocodes'));
    }

    public function create()
    {
        return view('admin.promo.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'amount_type' => 'required|in:fixed,random',
            'usage_limit' => 'nullable|integer|min:1',
        ];

        // Добавляем правила в зависимости от типа суммы
        if ($request->amount_type === 'fixed') {
            $rules['amount'] = 'required|numeric|min:0';
        } else {
            $rules['min_amount'] = 'required|numeric|min:0';
            $rules['max_amount'] = 'required|numeric|gt:min_amount';
        }

        $validated = $request->validate($rules);

        try {
            $promocode = Promocode::create([
                'code' => $this->generateUniqueCode(),
                'amount_type' => $validated['amount_type'],
                'amount' => $validated['amount_type'] === 'fixed' ? $validated['amount'] : null,
                'min_amount' => $validated['amount_type'] === 'random' ? $validated['min_amount'] : null,
                'max_amount' => $validated['amount_type'] === 'random' ? $validated['max_amount'] : null,
                'usage_limit' => $validated['usage_limit'] ?? null,
                'is_active' => true
            ]);

            return redirect()
                ->route('admin.promo.index')
                ->with('success', __('Промокод успешно создан'));

        } catch (\Exception $e) {
            \Log::error('Error creating promocode: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Произошла ошибка при создании промокода'));
        }
    }

    public function destroy(Promocode $promocode)
    {
        $promocode->delete();

        return redirect()
            ->route('admin.promo.index')
            ->with('success', __('Промокод удален'));
    }

    public function toggle(Promocode $promocode)
    {
        $promocode->update([
            'is_active' => !$promocode->is_active
        ]);

        return redirect()
            ->route('admin.promo.index')
            ->with('success', __('Статус промокода изменен'));
    }

    private function generateUniqueCode($length = 8)
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (Promocode::where('code', $code)->exists());

        return $code;
    }
}
