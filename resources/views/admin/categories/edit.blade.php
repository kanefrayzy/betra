@extends('panel')

@section('content')
<div class="space-y-8 animate-fade-in" x-data="categoryEditor()">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg" 
                         style="background-color: {{ $category->color ?? '#ffb300' }}">
                        @if($category->icon)
                            {!! $category->icon !!}
                        @else
                            {{ substr($category->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
                        <p class="text-indigo-100 mt-1">Редактирование категории и управление играми</p>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur text-white rounded-xl font-semibold hover:bg-opacity-30 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                        Назад к категориям
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Category Settings -->
        <div class="lg:col-span-1">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        Настройки категории
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Название</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white" 
                               required>
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Slug (URL)</label>
                        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Описание</label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Цвет</label>
                        <input type="color" name="color" value="{{ old('color', $category->color ?? '#ffb300') }}" 
                               class="w-full h-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Icon SVG -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Иконка (SVG код)</label>
                        <textarea name="icon" rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white font-mono text-sm">{{ old('icon', $category->icon) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Вставьте SVG код иконки</p>
                    </div>

                    <!-- Checkboxes -->
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Активна</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="show_on_homepage" value="1" {{ old('show_on_homepage', $category->show_on_homepage) ? 'checked' : '' }}
                                   class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Показывать на главной</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Сохранить настройки
                    </button>
                </div>
            </form>
        </div>

        <!-- Games in Category -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                </svg>
                            </div>
                            Игры в категории ({{ $category->games->count() }})
                        </h2>
                        <button @click="showAddGames = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                            <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            Добавить игры
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div id="games-sortable" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @forelse($category->games as $game)
                        <div class="game-item relative group" data-id="{{ $game->id }}" data-order="{{ $game->pivot->order }}">
                            <div class="relative rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 hover:border-purple-400 transition-all cursor-move">
                                <div class="aspect-[3/4]">
                                    @if($game->image)
                                    <img src="{{ $game->image }}" alt="{{ $game->name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-400 to-pink-400">
                                        <span class="text-white text-4xl font-bold">{{ substr($game->name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/90 to-transparent">
                                    <p class="text-white text-xs font-semibold truncate">{{ $game->name }}</p>
                                    <p class="text-gray-300 text-xs">{{ $game->provider }}</p>
                                </div>
                                <button @click="removeGame({{ $game->id }})" 
                                        class="absolute top-2 right-2 w-6 h-6 bg-red-500 hover:bg-red-600 rounded-full text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">В категории еще нет игр</p>
                            <button @click="showAddGames = true" class="mt-4 px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition-colors">
                                Добавить игры
                            </button>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Games Modal -->
    <div x-show="showAddGames" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="showAddGames = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Добавить игры в категорию</h3>
                        <button @click="showAddGames = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.categories.addGames', $category) }}" method="POST" 
                      x-data="gameSearch({{ $category->id }})"
                      x-init="loadGames()">
                    @csrf
                    <div class="p-6 overflow-y-auto" style="max-height: calc(80vh - 180px);" @scroll="handleScroll($event)">
                        <div class="mb-4">
                            <input type="text" 
                                   x-model="searchQuery"
                                   @input.debounce.500ms="resetAndSearch()"
                                   placeholder="Поиск игр по названию или провайдеру..."
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Счетчик результатов -->
                        <div x-show="!loading && games.length > 0" class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Найдено: <span x-text="total"></span> игр(ы)
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <template x-for="game in games" :key="game.id">
                                <label class="relative cursor-pointer group game-card">
                                    <input type="checkbox" name="game_ids[]" :value="game.id" class="game-checkbox sr-only peer">
                                    <div class="relative rounded-xl overflow-hidden border-2 border-gray-200 dark:border-gray-600 peer-checked:border-green-500 peer-checked:ring-4 peer-checked:ring-green-500/50 transition-all hover:scale-105">
                                        <div class="aspect-[3/4]">
                                            <img :src="game.image" :alt="game.name" class="w-full h-full object-cover" loading="lazy">
                                        </div>
                                        <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/90 to-transparent">
                                            <p class="text-white text-xs font-semibold truncate" x-text="game.name"></p>
                                            <p class="text-gray-300 text-xs" x-text="game.provider"></p>
                                        </div>
                                        <!-- Overlay для выбранных -->
                                        <div class="absolute inset-0 bg-green-500/40 opacity-0 peer-checked:opacity-100 transition-all flex items-center justify-center backdrop-blur-sm">
                                            <div class="bg-white rounded-full p-3 shadow-lg transform scale-0 peer-checked:scale-100 transition-transform">
                                                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>

                        <!-- Загрузка -->
                        <div x-show="loading" class="flex justify-center items-center py-8">
                            <svg class="animate-spin h-8 w-8 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <!-- Нет результатов -->
                        <div x-show="!loading && games.length === 0" class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Игры не найдены</p>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-4">
                        <button type="button" @click="showAddGames = false" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Отмена
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg">
                            Добавить выбранные
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function categoryEditor() {
    return {
        showAddGames: false,

        removeGame(gameId) {
            if (!confirm('Удалить игру из категории?')) return;

            fetch(`{{ route('admin.categories.removeGame', ['category' => $category->id, 'game' => '__GAME_ID__']) }}`.replace('__GAME_ID__', gameId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
}

function gameSearch(categoryId) {
    return {
        games: [],
        searchQuery: '',
        loading: false,
        page: 1,
        hasMore: true,
        total: 0,
        categoryId: categoryId,

        async loadGames() {
            if (this.loading || !this.hasMore) return;
            
            this.loading = true;
            
            try {
                const response = await fetch(`/qwdkox1i20/categories/${this.categoryId}/search-games?search=${encodeURIComponent(this.searchQuery)}&page=${this.page}`);
                const data = await response.json();
                
                this.games = [...this.games, ...data.games];
                this.hasMore = data.hasMore;
                this.total = data.total;
                this.page++;
            } catch (error) {
                console.error('Error loading games:', error);
            } finally {
                this.loading = false;
            }
        },

        resetAndSearch() {
            this.games = [];
            this.page = 1;
            this.hasMore = true;
            this.loadGames();
        },

        handleScroll(event) {
            const element = event.target;
            if (element.scrollHeight - element.scrollTop <= element.clientHeight + 100) {
                this.loadGames();
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('games-sortable');
    if (!el || el.querySelector('.game-item') === null) return;

    const sortable = Sortable.create(el, {
        animation: 150,
        ghostClass: 'opacity-50',
        onEnd: function(evt) {
            updateGamesOrder();
        }
    });

    function updateGamesOrder() {
        const games = [];
        document.querySelectorAll('.game-item').forEach((item, index) => {
            games.push({
                id: parseInt(item.dataset.id),
                order: index + 1
            });
        });

        fetch('{{ route('admin.categories.updateGamesOrder', $category) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ games })
        })
        .then(response => response.json())
        .catch(error => console.error('Error:', error));
    }
});
</script>

<style>
[x-cloak] { display: none !important; }

.game-checkbox:checked ~ div {
    transform: scale(0.95);
}

.game-card:hover {
    z-index: 10;
}
</style>
@endsection
