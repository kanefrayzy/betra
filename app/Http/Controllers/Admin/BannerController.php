<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();
        
        // Фильтр по языку
        if ($request->filled('locale')) {
            $query->where('locale', $request->locale);
        } else {
            $query->where('locale', 'ru'); // По умолчанию русский
        }
        
        // Фильтр по типу
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $banners = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url',
            'type' => ['required', Rule::in(['main', 'small'])],
            'locale' => ['required', Rule::in(['ru', 'en', 'kz', 'tr', 'az', 'uz'])],
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $destinationPath = public_path('/assets/images/banners');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $validated['image'] = 'assets/images/banners/' . $filename;
        }

        if ($request->hasFile('mobile_image')) {
            $file = $request->file('mobile_image');
            $destinationPath = public_path('/assets/images/banners');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $filename = 'mobile_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $validated['mobile_image'] = 'assets/images/banners/' . $filename;
        }

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', __('Баннер успешно создан!'));
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url',
            'type' => ['required', Rule::in(['main', 'small'])],
            'locale' => ['required', Rule::in(['ru', 'en', 'kz', 'tr', 'az', 'uz'])],
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('image')) {
            // Удаляем старое изображение
            if ($banner->image && file_exists(public_path($banner->image))) {
                unlink(public_path($banner->image));
            }
            
            $file = $request->file('image');
            $destinationPath = public_path('/assets/images/banners');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $validated['image'] = 'assets/images/banners/' . $filename;
        }

        if ($request->hasFile('mobile_image')) {
            // Удаляем старое мобильное изображение
            if ($banner->mobile_image && file_exists(public_path($banner->mobile_image))) {
                unlink(public_path($banner->mobile_image));
            }
            
            $file = $request->file('mobile_image');
            $destinationPath = public_path('/assets/images/banners');
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $filename = 'mobile_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $validated['mobile_image'] = 'assets/images/banners/' . $filename;
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', __('Баннер успешно обновлен!'));
    }

    public function destroy(Banner $banner)
    {
        // Удаляем файлы изображений
        if ($banner->image && file_exists(public_path($banner->image))) {
            unlink(public_path($banner->image));
        }
        if ($banner->mobile_image && file_exists(public_path($banner->mobile_image))) {
            unlink(public_path($banner->mobile_image));
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', __('Баннер успешно удален!'));
    }
}
