@extends('panel')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Конструктор категорий</h1>
                        <p class="text-purple-100 mt-1">Управление категориями игр на главной странице</p>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-purple-600 rounded-xl font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                        Создать категорию
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

    <!-- Categories List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                    </svg>
                </div>
                Категории ({{ $categories->count() }})
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Перетаскивайте категории для изменения порядка</p>
        </div>

        <div class="p-6">
            <div id="categories-sortable" class="space-y-4">
                @forelse($categories as $category)
                <div class="category-item bg-gradient-to-r from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-xl p-6 border-2 border-gray-200 dark:border-gray-600 hover:border-purple-400 dark:hover:border-purple-500 transition-all cursor-move" 
                     data-id="{{ $category->id }}" 
                     data-order="{{ $category->order }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 flex-1">
                            <!-- Drag Handle -->
                            <div class="drag-handle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-grab active:cursor-grabbing">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zm3 14a1 1 0 100-2 1 1 0 000 2zm0-4a1 1 0 100-2 1 1 0 000 2zm0-4a1 1 0 100-2 1 1 0 000 2z"/>
                                </svg>
                            </div>

                            <!-- Category Icon/Color -->
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg" 
                                 style="background-color: {{ $category->color ?? '#ffb300' }}">
                                @if($category->icon)
                                    {!! $category->icon !!}
                                @else
                                    {{ substr($category->name, 0, 1) }}
                                @endif
                            </div>

                            <!-- Category Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                                    @if($category->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-semibold rounded-full">Активна</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 text-xs font-semibold rounded-full">Неактивна</span>
                                    @endif
                                    @if($category->show_on_homepage)
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 text-xs font-semibold rounded-full">Главная</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                        </svg>
                                        {{ $category->games_count }} игр
                                    </span>
                                    <span>•</span>
                                    <span>Порядок: {{ $category->order }}</span>
                                    @if($category->slug)
                                    <span>•</span>
                                    <code class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-xs">{{ $category->slug }}</code>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" 
                               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                                <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Редактировать
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Категории еще не созданы</p>
                    <a href="{{ route('admin.categories.create') }}" class="inline-block mt-4 px-6 py-3 bg-purple-600 text-white rounded-xl font-semibold hover:bg-purple-700 transition-colors">
                        Создать первую категорию
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Sortable.js -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('categories-sortable');
    if (!el || el.children.length === 0) return;

    const sortable = Sortable.create(el, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'opacity-50',
        onEnd: function(evt) {
            updateCategoryOrder();
        }
    });

    function updateCategoryOrder() {
        const categories = [];
        document.querySelectorAll('.category-item').forEach((item, index) => {
            categories.push({
                id: parseInt(item.dataset.id),
                order: index + 1
            });
        });

        fetch('{{ route('admin.categories.updateOrder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ categories })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update order display
                document.querySelectorAll('.category-item').forEach((item, index) => {
                    item.dataset.order = index + 1;
                    const orderSpan = item.querySelector('span:contains("Порядок:")');
                    if (orderSpan) {
                        orderSpan.textContent = 'Порядок: ' + (index + 1);
                    }
                });
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>

<style>
.category-item.sortable-ghost {
    opacity: 0.4;
}

.drag-handle:active {
    cursor: grabbing;
}
</style>
@endsection
