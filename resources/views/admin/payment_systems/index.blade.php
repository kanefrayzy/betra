@extends('panel')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-violet-50 via-purple-50 to-fuchsia-50 dark:from-gray-900 dark:via-purple-900 dark:to-fuchsia-900 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 p-8">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Платежные системы</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-400">Управление интеграциями платежных сервисов</p>
                    </div>
                </div>

                <a href="{{ route('admin.payment_systems.create') }}"
                   class="px-8 py-4 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                    </svg>
                    Добавить систему
                </a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-8 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-emerald-800 dark:text-emerald-200 font-semibold text-lg">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $paymentSystems->count() }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Всего систем</p>
                </div>
                <div class="w-12 h-12 bg-violet-100 dark:bg-violet-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $paymentSystems->where('active', 1)->count() }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Активных</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $paymentSystems->where('active', 0)->count() }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Неактивных</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Systems Grid -->
    @if($paymentSystems->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach($paymentSystems as $paymentSystem)
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-violet-500 to-purple-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($paymentSystem->logo)
                                    <img src="{{ asset('storage/' . $paymentSystem->logo) }}"
                                         class="w-16 h-16 rounded-xl object-cover border-2 border-white shadow-lg"
                                         alt="{{ $paymentSystem->name }}">
                                @else
                                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center border-2 border-white">
                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-xl font-bold">{{ $paymentSystem->name }}</h3>
                                    <p class="text-sm opacity-90">ID: {{ $paymentSystem->id }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $paymentSystem->active ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $paymentSystem->active ? 'Активна' : 'Неактивна' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-6 space-y-4">
                        <!-- URL -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">URL</label>
                            <code class="text-sm font-mono text-gray-800 dark:text-gray-200 break-all">{{ $paymentSystem->url }}</code>
                        </div>

                        <!-- Merchant ID -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4">
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Merchant ID</label>
                            <code class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $paymentSystem->merchant_id }}</code>
                        </div>

                        <!-- Secrets (Masked) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 border border-red-200 dark:border-red-800">
                                <label class="block text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide mb-1">Secret 1</label>
                                <code class="text-xs font-mono text-red-800 dark:text-red-200">{{ str_repeat('•', 12) }}{{ substr($paymentSystem->merchant_secret_1, -4) }}</code>
                            </div>

                            @if($paymentSystem->merchant_secret_2)
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 border border-red-200 dark:border-red-800">
                                <label class="block text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide mb-1">Secret 2</label>
                                <code class="text-xs font-mono text-red-800 dark:text-red-200">{{ str_repeat('•', 12) }}{{ substr($paymentSystem->merchant_secret_2, -4) }}</code>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div class="p-6 pt-0">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.payment_systems.edit', $paymentSystem->id) }}"
                               class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-300 text-center">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                Редактировать
                            </a>

                            <form action="{{ route('admin.payment_systems.destroy', $paymentSystem->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Вы уверены, что хотите удалить эту платежную систему?')"
                                        class="w-full px-4 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold rounded-xl transition-all duration-300">
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 012 0v4a1 1 0 11-2 0V7zm4 0a1 1 0 012 0v4a1 1 0 11-2 0V7z" clip-rule="evenodd"/>
                                    </svg>
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-16 text-center">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Нет платежных систем</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                Создайте первую платежную систему для начала приема платежей
            </p>
            <a href="{{ route('admin.payment_systems.create') }}"
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold rounded-2xl transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                </svg>
                Создать первую систему
            </a>
        </div>
    @endif
</div>

@endsection
