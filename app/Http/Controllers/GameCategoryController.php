<?php

namespace App\Http\Controllers;

use App\Models\GameCategory;
use App\Models\SlotegratorGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GameCategoryController extends Controller
{
    /**
     * Показать конструктор категорий
     */
    public function index()
    {
        $categories = GameCategory::withCount('games')
            ->orderBy('order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Показать форму создания категории
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Сохранить новую категорию
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:game_categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'show_on_homepage' => 'boolean',
        ]);

        // Получаем максимальный order и добавляем 1
        $maxOrder = GameCategory::max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        $category = GameCategory::create($validated);

        Cache::forget('homepage_categories');

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', __('Категория успешно создана'));
    }

    /**
     * Показать страницу редактирования категории
     */
    public function edit(GameCategory $category)
    {
        $category->load(['games' => function($query) {
            $query->orderBy('category_game.order');
        }]);

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * AJAX: Поиск доступных игр для добавления
     */
    public function searchAvailableGames(Request $request, GameCategory $category)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $perPage = 20;

        $query = SlotegratorGame::where('is_active', true)
            ->whereNotIn('id', $category->games->pluck('id'));

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('provider', 'LIKE', "%{$search}%");
            });
        }

        $games = $query->orderBy('name')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get(['id', 'name', 'provider', 'image']);

        $total = $query->count();
        $hasMore = ($page * $perPage) < $total;

        return response()->json([
            'games' => $games,
            'hasMore' => $hasMore,
            'total' => $total
        ]);
    }

    /**
     * Обновить категорию
     */
    public function update(Request $request, GameCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:game_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'show_on_homepage' => 'boolean',
        ]);

        $category->update($validated);

        Cache::forget('homepage_categories');

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', __('Категория успешно обновлена'));
    }

    /**
     * Удалить категорию
     */
    public function destroy(GameCategory $category)
    {
        $category->delete();

        Cache::forget('homepage_categories');

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('Категория успешно удалена'));
    }

    /**
     * Обновить порядок категорий
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:game_categories,id',
            'categories.*.order' => 'required|integer',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['categories'] as $item) {
                GameCategory::where('id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
        });

        Cache::forget('homepage_categories');

        return response()->json(['success' => true]);
    }

    /**
     * Добавить игры в категорию
     */
    public function addGames(Request $request, GameCategory $category)
    {
        $validated = $request->validate([
            'game_ids' => 'required|array',
            'game_ids.*' => 'exists:slotegrator_games,id',
        ]);

        $maxOrder = DB::table('category_game')
            ->where('game_category_id', $category->id)
            ->max('order') ?? 0;

        $syncData = [];
        foreach ($validated['game_ids'] as $index => $gameId) {
            $syncData[$gameId] = ['order' => $maxOrder + $index + 1];
        }

        $category->games()->syncWithoutDetaching($syncData);

        Cache::forget('homepage_categories');

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', __('Игры успешно добавлены в категорию'));
    }

    /**
     * Удалить игру из категории
     */
    public function removeGame(GameCategory $category, SlotegratorGame $game)
    {
        $category->games()->detach($game->id);

        Cache::forget('homepage_categories');

        return response()->json(['success' => true]);
    }

    /**
     * Обновить порядок игр в категории
     */
    public function updateGamesOrder(Request $request, GameCategory $category)
    {
        $validated = $request->validate([
            'games' => 'required|array',
            'games.*.id' => 'required|exists:slotegrator_games,id',
            'games.*.order' => 'required|integer',
        ]);

        DB::transaction(function () use ($category, $validated) {
            foreach ($validated['games'] as $item) {
                DB::table('category_game')
                    ->where('game_category_id', $category->id)
                    ->where('slotegrator_game_id', $item['id'])
                    ->update(['order' => $item['order']]);
            }
        });

        Cache::forget('homepage_categories');

        return response()->json(['success' => true]);
    }
    
    /**
     * Показать страницу категории на фронте
     */
    public function showCategory($slug)
    {
        $category = GameCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('games.category', compact('category'));
    }
}
