<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function index()
    {
        $ranks = Rank::all();
        return view('admin.ranks.index', compact('ranks'));
    }

    public function create()
    {
        return view('admin.ranks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'oborot_min' => 'required|numeric',
            'oborot_max' => 'required|numeric',
            'rakeback' => 'nullable|numeric',
            'daily_min' => 'nullable|numeric',
            'daily_max' => 'nullable|numeric',
            'percent' => 'nullable|numeric',
        ]);

        $data = $request->all();

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filePath = $file->store('ranks', 'public');
            $data['picture'] = $filePath;
        }

        $data['percent'] = $request->has('percent') ? $request->get('percent') : 1.00;

        Rank::create($data);

        return redirect()->route('admin.ranks.index')->with('success', 'Rank created successfully.');
    }

    public function edit(Rank $rank)
    {
        return view('admin.ranks.edit', compact('rank'));
    }

    public function update(Request $request, Rank $rank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'oborot_min' => 'required|numeric',
            'oborot_max' => 'required|numeric',
            'rakeback' => 'nullable|numeric',
            'daily_min' => 'nullable|numeric',
            'daily_max' => 'nullable|numeric',
            'percent' => 'required|numeric',
        ]);

        $data = $request->all();

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filePath = $file->store('ranks', 'public');
            $data['picture'] = $filePath;
        }

        $rank->update($data);

        return redirect()->route('admin.ranks.index')->with('success', 'Rank updated successfully.');
    }

    public function destroy(Rank $rank)
    {
        $rank->delete();
        return redirect()->route('admin.ranks.index')->with('success', 'Rank deleted successfully.');
    }
}
