@extends('panel')
@php $baseUrl = 'betrika'; @endphp
@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --danger-gradient: linear-gradient(135deg, #fd746c 0%, #ff9068 100%);
    --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.dark .glass-card {
    background: rgba(17, 24, 39, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.stat-card {
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover::before {
    opacity: 1;
}

.floating-label {
    position: relative;
}

.floating-label input:focus ~ label,
.floating-label input:not(:placeholder-shown) ~ label {
    transform: translateY(-24px) scale(0.8);
    color: #667eea;
}

.floating-label label {
    position: absolute;
    left: 12px;
    top: 12px;
    transition: all 0.2s ease;
    pointer-events: none;
    background: rgba(255, 255, 255, 0.9);
    padding: 0 4px;
    border-radius: 4px;
}

.dark .floating-label label {
    background: rgba(17, 24, 39, 0.9);
}

.metric-card {
    position: relative;
    overflow: hidden;
}

.metric-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.metric-card:hover::after {
    left: 100%;
}

.tab-button {
    position: relative;
    overflow: hidden;
}

.tab-button::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--primary-gradient);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.tab-button.active::before {
    width: 100%;
}

.pulse-dot {
    animation: pulse-ring 1.5s infinite;
}

@keyframes pulse-ring {
    0% {
        transform: scale(0.33);
        opacity: 1;
    }
    80%, 100% {
        transform: scale(1);
        opacity: 0;
    }
}

.slide-in {
    animation: slideIn 0.6s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.morphism-button {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.morphism-button:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.glow-effect {
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.4);
}

.progress-bar {
    background: var(--primary-gradient);
    height: 4px;
    border-radius: 2px;
    transition: width 1s ease-in-out;
}

.icon-bounce {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-5px);
    }
    60% {
        transform: translateY(-3px);
    }
}

.neon-border {
    box-shadow: 0 0 5px #667eea, 0 0 10px #667eea, 0 0 15px #667eea;
}

.ripple {
    position: relative;
    overflow: hidden;
}

.ripple::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.ripple:active::before {
    width: 300px;
    height: 300px;
}
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50 dark:from-gray-900 dark:via-blue-900 dark:to-purple-900 p-6">
    <!-- Floating Header -->
    <div class="glass-card rounded-2xl p-6 mb-8 slide-in">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-purple-500 to-blue-600 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                    </div>
                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white pulse-dot"></div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold gradient-text">Профиль пользователя</h1>
                    <p class="text-gray-600 dark:text-gray-400 font-medium">Полное управление аккаунтом {{$user->username}}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
               <a href="/{{$baseUrl}}/gamehistory/{{ $user->id }}"
                  class="px-5 py-2.5 rounded-xl text-white font-medium ripple group bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                  target="_blank">
                   <div class="flex items-center space-x-2">
                       <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                           <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                       </svg>
                       <span>История игры</span>
                   </div>
               </a>

               <a href="/{{$baseUrl}}/payhistory/{{ $user->id }}"
                  class="px-5 py-2.5 rounded-xl text-white font-medium ripple group bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                  target="_blank">
                   <div class="flex items-center space-x-2">
                       <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                           <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4z"/>
                       </svg>
                       <span>Платежи</span>
                   </div>
               </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- Enhanced Profile Card -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Main Profile -->
            <div class="glass-card rounded-2xl overflow-hidden stat-card slide-in">
                <!-- Premium Header -->
                <div class="relative p-8 text-center" style="background: var(--primary-gradient);">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-blue-600/20"></div>
                    <div class="relative">
                        <div class="relative inline-block mb-4">
                            <img src="{{$user->avatar}}" alt="{{$user->username}}"
                                 class="w-24 h-24 rounded-full border-4 border-white shadow-xl">
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-3 border-white flex items-center justify-center pulse-dot">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-white mb-2">{{$user->username}}</h3>

                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm border border-white/30">
                            @if($user->is_admin)
                                <svg class="w-4 h-4 mr-2 text-red-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-white">Администратор</span>
                            @elseif($user->is_moder)
                                <svg class="w-4 h-4 mr-2 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-white">Модератор</span>
                            @elseif($user->is_chat_moder)
                                <svg class="w-4 h-4 mr-2 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="text-white">Модератор чата</span>
                            @else
                                <svg class="w-4 h-4 mr-2 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                <span class="text-white">Пользователь</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="p-6 space-y-4">
                    <!-- Balance Highlight -->
                    <div class="relative p-6 rounded-xl overflow-hidden" style="background: var(--success-gradient);">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-emerald-500/20"></div>
                        <div class="relative text-center text-white">
                            <p class="text-sm font-medium opacity-90 mb-1">Текущий баланс</p>
                            <p class="text-3xl font-bold">{{$user->balance}}</p>
                            <p class="text-lg font-medium opacity-90">{{$user->currency->symbol}}</p>
                        </div>
                        <div class="absolute top-4 right-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm icon-bounce">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 p-4 rounded-xl text-center metric-card">
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $refcount }}</p>
                            <p class="text-sm text-purple-500 dark:text-purple-300 font-medium">Рефералов</p>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 p-4 rounded-xl text-center metric-card">
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ moneyFormat($ref) }}</p>
                            <p class="text-sm text-blue-500 dark:text-blue-300 font-medium">Заработано USD</p>
                        </div>
                    </div>

                    @if($referrer)
                    <!-- Статистика для реферера -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mb-1">Принес рефереру</p>
                                <a href="/{{$baseUrl}}/user/{{$referrer->id}}" class="text-sm text-emerald-700 dark:text-emerald-300 hover:underline font-semibold">
                                    {{$referrer->username}}
                                </a>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ moneyFormat($broughtToReferrer) }}</p>
                                <p class="text-xs text-emerald-500 dark:text-emerald-400">USD</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Level Progress -->
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 p-4 rounded-xl border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">{{$user->rank->name}}</p>
                            <p class="text-sm font-bold text-yellow-800 dark:text-yellow-200">{{$user->oborot}} $</p>
                        </div>
                        <div class="w-full bg-yellow-200 dark:bg-yellow-800 rounded-full h-2">
                            <div class="progress-bar" style="width: 65%"></div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">User ID</span>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{$user->id}}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">IP адрес</span>
                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{$user->ip}}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</span>
                            <span class="font-mono text-sm text-gray-900 dark:text-white">{{$user->email ?? 'Не указан'}}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Telegram</span>
                            <span class="font-mono text-sm text-gray-900 dark:text-white">
                                @if($user->telegram_id)
                                    <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                        Привязан
                                    </span>
                                @else
                                    <span class="text-gray-500">Не привязан</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Пригласил</span>
                            <span class="font-mono text-sm text-gray-900 dark:text-white">
                                @if($referrer)
                                    <a href="/{{$baseUrl}}/user/{{$referrer->id}}" class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:underline">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                                        {{$referrer->username}}
                                    </a>
                                @else
                                    <span class="text-gray-500">Нет реферера</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Status -->
            @php
                $verification = \App\Models\UserVerification::where('user_id', $user->id)->latest()->first();
            @endphp
            @if($verification)
            <div class="glass-card rounded-2xl overflow-hidden stat-card slide-in">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Верификация</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Статус проверки документов</p>
                        </div>
                    </div>

                    @if($verification->status === 'pending')
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl flex items-center gap-3">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                            <div>
                                <p class="font-semibold text-yellow-800 dark:text-yellow-200">На рассмотрении</p>
                                <p class="text-sm text-yellow-600 dark:text-yellow-400">Заявка проверяется</p>
                            </div>
                        </div>
                    @elseif($verification->status === 'approved')
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center gap-3">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                            <div>
                                <p class="font-semibold text-green-800 dark:text-green-200">Верифицирован</p>
                                <p class="text-sm text-green-600 dark:text-green-400">Проверка пройдена</p>
                            </div>
                        </div>
                    @elseif($verification->status === 'rejected')
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                                <div>
                                    <p class="font-semibold text-red-800 dark:text-red-200">Отклонено</p>
                                    <p class="text-sm text-red-600 dark:text-red-400">{{$verification->reject_reason}}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($verification->first_name || $verification->last_name)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                        @if($verification->first_name)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Имя:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{$verification->first_name}}</span>
                        </div>
                        @endif
                        @if($verification->last_name)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Фамилия:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{$verification->last_name}}</span>
                        </div>
                        @endif
                        @if($verification->birth_date)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Дата рождения:</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{\Carbon\Carbon::parse($verification->birth_date)->format('d.m.Y')}}</span>
                        </div>
                        @endif
                        @if($verification->document_type)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Документ:</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{$verification->document_type === 'passport' ? 'Паспорт' : 'ID карта'}}
                            </span>
                        </div>
                        @endif
                        @if($verification->selfie_path)
                        <div class="mt-3">
                            <a href="{{asset('storage/' . $verification->selfie_path)}}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/></svg>
                                Просмотреть документ
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Enhanced Settings Form -->
        <div class="xl:col-span-3 space-y-6">
            <div class="glass-card rounded-2xl stat-card slide-in">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Настройки пользователя</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Редактирование данных и прав доступа</p>
                        </div>
                    </div>
                </div>

                <form method="post" action="/{{$baseUrl}}/userSave" class="p-6">
                    <input name="id" value="{{$user->id}}" type="hidden">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Username -->
                        <div class="floating-label">
                            <input type="text"
                                   name="username"
                                   value="{{$user->username}}"
                                   placeholder=" "
                                   @if(!$u->is_admin) readonly @endif
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-300 @if(!$u->is_admin) bg-gray-50 dark:bg-gray-700 @endif">
                            <label class="text-gray-600 dark:text-gray-400 font-medium">Имя пользователя</label>
                        </div>

                        <!-- Status -->
                        <div class="floating-label">
                            <select name="priv" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-300">
                                <option value="Admin" @if($user->is_admin) selected @endif>Администратор</option>
                                <option value="moder" @if($user->is_moder) selected @endif>Модератор</option>
                                <option value="chat_moder" @if($user->is_chat_moder) selected @endif>Модератор чата</option>
                                <option value="user" @if(!$user->is_admin && !$user->is_moder && !$user->is_chat_moder) selected @endif>Пользователь</option>
                            </select>
                            <label class="text-gray-600 dark:text-gray-400 font-medium">Статус пользователя</label>
                        </div>

                        <!-- Balance -->
                        <div class="floating-label">
                            <input type="text"
                                   name="balance"
                                   value="{{$user->balance}}"
                                   id="balance"
                                   placeholder=" "
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-300">
                            <label class="text-gray-600 dark:text-gray-400 font-medium">Баланс</label>
                        </div>

                        <!-- Currency Display -->
                        <div class="floating-label">
                            <input type="text"
                                   value="{{$user->balance}} {{$user->currency->symbol}}"
                                   id="rub"
                                   placeholder=" "
                                   readonly
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            <label class="text-gray-600 dark:text-gray-400 font-medium">В валюте</label>
                        </div>

                        <!-- Payment Ban -->
                        <div class="floating-label">
                            <select name="payment_ban" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-all duration-300">
                                <option value="">Нет</option>
                                <option value="1">1 час</option>
                                <option value="2">2 часа</option>
                                <option value="6">6 часов</option>
                                <option value="12">12 часов</option>
                            </select>
                            <label class="text-gray-600 dark:text-gray-400 font-medium">Блокировка платежей</label>
                        </div>
                    </div>

                    @if($user->payment_ban_at && $user->payment_ban_at > now())
                    <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-800 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-red-800 dark:text-red-200">Активная блокировка</p>
                                <p class="text-sm text-red-600 dark:text-red-400">Бан действует до: {{$user->payment_ban_at}}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($u->is_admin)
                    <div class="mt-6 space-y-6">
                        <!-- Danger Zone -->
                        <div class="p-6 bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-xl neon-border">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-red-100 dark:bg-red-800 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-red-800 dark:text-red-200">Опасная зона</h4>
                                    <p class="text-sm text-red-600 dark:text-red-400">Действия с необратимыми последствиями</p>
                                </div>
                            </div>
                            <select name="user_ban" class="w-full px-4 py-3 border-2 border-red-300 dark:border-red-600 rounded-xl focus:border-red-500 bg-red-50 dark:bg-red-900/40 text-red-900 dark:text-red-100">
                                <option value="" @if($user->ban == 0) selected @endif>Пользователь не забанен</option>
                                <option value="1" @if($user->ban == 1) selected @endif>🔒 Забанить пользователя</option>
                                <option value="2">🔓 Разбанить пользователя</option>
                            </select>
                        </div>

                        <!-- Premium Controls -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Referral Percentage -->
                            <div class="p-6 bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border-2 border-yellow-200 dark:border-yellow-800 rounded-xl glow-effect">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-800 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-yellow-800 dark:text-yellow-200">Реферальный процент</h4>
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400">Индивидуальная настройка</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <input type="number"
                                           name="ref_percentage"
                                           value="{{$user->ref_percentage ?? 20}}"
                                           min="0"
                                           max="100"
                                           step="0.1"
                                           class="flex-1 px-4 py-3 border-2 border-yellow-300 dark:border-yellow-600 rounded-l-xl focus:border-yellow-500 bg-white dark:bg-yellow-900/40 text-gray-900 dark:text-white">
                                    <div class="px-4 py-3 bg-yellow-200 dark:bg-yellow-800 border-2 border-l-0 border-yellow-300 dark:border-yellow-600 rounded-r-xl">
                                        <span class="text-yellow-800 dark:text-yellow-200 font-bold">%</span>
                                    </div>
                                </div>
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">💡 По умолчанию: 20%</p>
                            </div>

                        </div>
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-8">
                        <button type="submit" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl ripple">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Сохранить изменения</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Currency Statistics -->
    <div class="mt-8 glass-card rounded-2xl stat-card slide-in">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold gradient-text">Статистика пользователя</h3>
                    <p class="text-gray-600 dark:text-gray-400">Детальная аналитика активности ({{$user->currency->name}} - {{$user->currency->symbol}})</p>
                </div>
            </div>
        </div>

        {{-- Табы валют закомментированы, так как теперь пользователь имеет только одну валюту --}}
        {{-- Enhanced Currency Tabs --}}
        {{-- <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex flex-wrap gap-2 p-4">
                @foreach($statistics as $index => $stats)
                <button onclick="showTab('currency-{{ $index }}')"
                        class="tab-button px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 @if($index == 0) bg-gradient-to-r from-blue-600 to-purple-600 text-white active @else bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 @endif"
                        data-tab="currency-{{ $index }}">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full @if($index == 0) bg-white/20 @else bg-gradient-to-r from-blue-500 to-purple-600 @endif flex items-center justify-center">
                            <span class="text-xs font-bold @if($index == 0) text-white @else text-white @endif">
                                {{ substr($stats['currency_code'], 0, 1) }}
                            </span>
                        </div>
                        <span>{{ $stats['currency_name'] }}</span>
                        <span class="opacity-75">({{ $stats['currency_code'] }})</span>
                    </div>
                </button>
                @endforeach
            </div>
        </div> --}}

        <!-- Enhanced Tab Content -->
        <div class="p-6">
            @foreach($statistics as $index => $stats)
            <div id="currency-{{ $index }}" class="currency-content space-y-8">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Deposits -->
                    <div class="relative p-6 rounded-2xl overflow-hidden metric-card" style="background: var(--success-gradient);">
                        <div class="relative text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold">{{ moneyFormat($stats['total_deposits']) }}</p>
                                    <p class="text-sm opacity-90">{{ $stats['currency_code'] }}</p>
                                </div>
                            </div>
                            <p class="font-semibold">Депозиты</p>
                            <p class="text-sm opacity-75">{{ $stats['deposit_count'] }} операций</p>
                        </div>
                    </div>

                    <!-- Withdrawals -->
                    <div class="relative p-6 rounded-2xl overflow-hidden metric-card" style="background: var(--danger-gradient);">
                        <div class="relative text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold">{{ moneyFormat($stats['total_withdrawals']) }}</p>
                                    <p class="text-sm opacity-90">{{ $stats['currency_code'] }}</p>
                                </div>
                            </div>
                            <p class="font-semibold">Выводы</p>
                            <p class="text-sm opacity-75">{{ $stats['withdrawal_count'] }} операций</p>
                        </div>
                    </div>

                    <!-- Bets -->
                    <div class="relative p-6 rounded-2xl overflow-hidden metric-card" style="background: var(--info-gradient);">
                        <div class="relative text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold">{{ moneyFormat($stats['total_bets']) }}</p>
                                    <p class="text-sm opacity-90">{{ $stats['currency_code'] }}</p>
                                </div>
                            </div>
                            <p class="font-semibold">Ставки</p>
                            <p class="text-sm opacity-75">Общая сумма</p>
                        </div>
                    </div>

                    <!-- Net Result -->
                    @php
                        $netWin = $stats['total_wins'] - $stats['total_bets'];
                        $isProfit = $netWin > 0;
                    @endphp
                    <div class="relative p-6 rounded-2xl overflow-hidden metric-card" style="background: @if($isProfit) var(--danger-gradient) @else var(--success-gradient) @endif;">
                        <div class="relative text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    @if($isProfit)
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @else
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold">{{ moneyFormat($netWin) }}</p>
                                    <p class="text-sm opacity-90">{{ $stats['currency_code'] }}</p>
                                </div>
                            </div>
                            <p class="font-semibold">Чистый результат</p>
                            <p class="text-sm opacity-75">@if($isProfit) 📈 Прибыль @else 📉 Убыток @endif</p>
                        </div>
                    </div>
                </div>

                <!-- Detailed Stats -->
                <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
                    <!-- Game Stats -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">🎮 Игровая статистика</h4>

                        <div class="p-6 bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center justify-between mb-4">
                                <p class="font-semibold text-purple-700 dark:text-purple-300">Выигрыши</p>
                                <p class="text-2xl font-bold text-purple-800 dark:text-purple-200">{{ moneyFormat($stats['total_wins']) }}</p>
                            </div>
                            <div class="w-full bg-purple-200 dark:bg-purple-800 rounded-full h-2">
                                @php
                                    $winRate = $stats['total_bets'] > 0 ? ($stats['total_wins'] / $stats['total_bets']) * 100 : 0;
                                @endphp
                                <div class="progress-bar" style="width: {{ min($winRate, 100) }}%"></div>
                            </div>
                            <p class="text-sm text-purple-600 dark:text-purple-400 mt-2">Коэффициент выигрыша: {{ number_format($winRate, 1) }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Статистика из БД (обновляется командой oborot:update) -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
                                <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
                                <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">📊 Накопленная статистика</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Данные обновляются автоматически каждую минуту</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Всего игр -->
                        <div class="p-5 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Всего игр</p>
                                    <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">{{ number_format($stats['total_games']) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Всего выигрышей -->
                        <div class="p-5 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-green-600 dark:text-green-400 font-medium">Выигрышей</p>
                                    <p class="text-2xl font-bold text-green-800 dark:text-green-200">{{ number_format($stats['total_wins_count']) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Сумма ставок -->
                        <div class="p-5 bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl border border-orange-200 dark:border-orange-800">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">Сумма ставок</p>
                                    <p class="text-xl font-bold text-orange-800 dark:text-orange-200">{{ moneyFormat($stats['total_bets_amount']) }}</p>
                                    <p class="text-xs text-orange-500 dark:text-orange-400">USD</p>
                                </div>
                            </div>
                        </div>

                        <!-- Сумма выигрышей -->
                        <div class="p-5 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Сумма выигрышей</p>
                                    <p class="text-xl font-bold text-purple-800 dark:text-purple-200">{{ moneyFormat($stats['total_wins_amount']) }}</p>
                                    <p class="text-xs text-purple-500 dark:text-purple-400">USD</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Win Rate -->
                    @php
                        $dbWinRate = $stats['total_games'] > 0 ? ($stats['total_wins_count'] / $stats['total_games']) * 100 : 0;
                    @endphp
                    <div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Процент побед (Win Rate)</p>
                                <p class="text-3xl font-bold text-indigo-800 dark:text-indigo-200">{{ number_format($dbWinRate, 2) }}%</p>
                            </div>
                            <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-800 rounded-full flex items-center justify-center">
                                <span class="text-2xl">🎯</span>
                            </div>
                        </div>
                        <div class="w-full bg-indigo-200 dark:bg-indigo-800 rounded-full h-3">
                            <div class="progress-bar bg-gradient-to-r from-indigo-500 to-purple-600" style="width: {{ min($dbWinRate, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
// Enhanced tab switching with animations
function showTab(tabId) {
    // Hide all content with fade effect
    document.querySelectorAll('.currency-content').forEach(content => {
        content.style.opacity = '0';
        setTimeout(() => {
            content.classList.add('hidden');
        }, 150);
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(tab => {
        tab.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-purple-600', 'text-white', 'active');
        tab.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
    });

    // Show selected content with fade effect
    setTimeout(() => {
        const selectedContent = document.getElementById(tabId);
        selectedContent.classList.remove('hidden');
        setTimeout(() => {
            selectedContent.style.opacity = '1';
        }, 50);
    }, 150);

    // Add active class to selected tab
    const selectedTab = document.querySelector(`[data-tab="${tabId}"]`);
    selectedTab.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
    selectedTab.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-purple-600', 'text-white', 'active');
}

// Enhanced balance calculation
document.getElementById('balance')?.addEventListener('input', function() {
    const balance = this.value;
    const symbol = '{{$user->currency->symbol}}';
    document.getElementById('rub').value = balance + ' ' + symbol;

    // Add glow effect on change
    this.classList.add('glow-effect');
    setTimeout(() => {
        this.classList.remove('glow-effect');
    }, 2000);
});

// Add intersection observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('slide-in');
        }
    });
}, observerOptions);

// Observe all stat cards
document.querySelectorAll('.stat-card').forEach(card => {
    observer.observe(card);
});

// Animate progress bars on load
window.addEventListener('load', () => {
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
});

// Add floating label animation
document.querySelectorAll('.floating-label input, .floating-label select').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentNode.classList.add('focused');
    });

    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentNode.classList.remove('focused');
        }
    });

    // Check initial state
    if (input.value) {
        input.parentNode.classList.add('focused');
    }
});
</script>

@endsection
