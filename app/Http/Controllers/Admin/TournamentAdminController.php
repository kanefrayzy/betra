<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentLeaderboard;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TournamentAdminController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::orderBy('created_at', 'desc')->get();
        return view('admin.tournament.index', compact('tournaments'));
    }

    public function create()
    {
        return view('admin.tournament.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prize_pool' => 'required|numeric|min:0',
            'min_turnover' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'prizes' => 'required|array',
            'prizes.*' => 'numeric|min:0'
        ]);

        // Формируем распределение призов
        $prize_distribution = [
            100000, // 1 место - $100,000
            50000,  // 2 место - $50,000
            25000,  // 3 место - $25,000
            10000,  // 4 место - $10,000
            5000,   // 5 место - $5,000
            2500,   // 6 место - $2,500
            2500,   // 7 место - $2,500
            2500,   // 8 место - $2,500
            1250,   // 9 место - $1,250
            1250    // 10 место - $1,250
        ];

        Tournament::create([
            'name' => $request->name,
            'description' => $request->description,
            'prize_pool' => $request->prize_pool,
            'min_turnover' => $request->min_turnover,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'prize_distribution' => $prize_distribution,
            'status' => Carbon::now()->between(
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ) ? 'active' : 'upcoming'
        ]);

        return redirect()->route('admin.tournament.index')->with('success', __('Турнир успешно создан'));
    }

    public function edit($id)
    {
        $tournament = Tournament::findOrFail($id);
        return view('admin.tournament.edit', compact('tournament'));
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prize_pool' => 'required|numeric|min:0',
            'min_turnover' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $prizes = [];
        if ($request->filled('prizes')) {
            foreach ($request->prizes as $prize) {
                if (!empty($prize)) {
                    $prizes[] = floatval($prize);
                }
            }
        }

        $tournament->update([
            'name' => $request->name,
            'description' => $request->description,
            'prize_pool' => $request->prize_pool,
            'min_turnover' => $request->min_turnover,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'prize_distribution' => $prizes,
            'status' => Carbon::now()->between(
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ) ? 'active' : 'upcoming'
        ]);

        return redirect()->route('admin.tournament.index')->with('success', __('Турнир обновлен'));
    }
    //
    // public function complete($id)
    // {
    //     $tournament = Tournament::findOrFail($id);
    //     $tournament->status = 'completed';
    //     $tournament->save();
    //
    //     // Начисляем призы победителям
    //     $winners = TournamentLeaderboard::where('tournament_id', $id)
    //         ->orderBy('turnover', 'desc')
    //         ->get();
    //
    //     foreach ($winners as $index => $winner) {
    //         if (isset($tournament->prize_distribution[$index])) {
    //             $prize = $tournament->prize_distribution[$index];
    //             $winner->prize = $prize;
    //             $winner->save();
    //
    //             // Начисляем приз на баланс
    //             $winner->user->increment('balance', $prize);
    //         }
    //     }
    //
    //     return redirect()->route('admin.tournaments.index')->with('success', 'Турнир завершен и призы распределены');
    // }

    public function destroy($id)
    {
        Tournament::findOrFail($id)->delete();
        return redirect()->route('admin.tournaments.index')->with('success', __('Турнир удален'));
    }
}
