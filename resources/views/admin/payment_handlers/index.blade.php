@extends('panel')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
/* Advanced Payment Handlers Styles */
:root {
    --payment-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --payment-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --payment-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --payment-danger: linear-gradient(135deg, #fd746c 0%, #ff9068 100%);
    --payment-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.payment-dashboard {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 24px;
    padding: 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.dark .payment-dashboard {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
}

.payment-dashboard::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
    transform: rotate(45deg);
}

.handler-card {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    height: 100%;
}

.dark .handler-card {
    background: #1e293b;
    border-color: #334155;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.handler-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.handler-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: var(--payment-primary);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.handler-card:hover::before {
    transform: scaleX(1);
}

.handler-icon {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    object-fit: cover;
    border: 3px solid transparent;
    background: linear-gradient(white, white) padding-box, var(--payment-primary) border-box;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.handler-card:hover .handler-icon {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 36px rgba(102, 126, 234, 0.4);
}

.status-toggle {
    position: relative;
    width: 60px;
    height: 30px;
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.status-toggle.active {
    background: var(--payment-success);
    box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4);
}

.status-toggle.inactive {
    background: #e2e8f0;
    box-shadow: 0 4px 12px rgba(226, 232, 240, 0.4);
}

.status-toggle::before {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.status-toggle.active::before {
    transform: translateX(30px);
}

.fee-display {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 12px;
    padding: 12px;
    margin: 12px 0;
    position: relative;
    overflow: hidden;
}

.dark .fee-display {
    background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
}

.fee-display::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.handler-card:hover .fee-display::before {
    left: 100%;
}

.limit-badge {
    background: var(--payment-info);
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(79, 172, 254, 0.3);
}

.action-button {
    padding: 10px 16px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-view {
    background: var(--payment-info);
    color: white;
    box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
}

.btn-edit {
    background: var(--payment-warning);
    color: white;
    box-shadow: 0 4px 12px rgba(240, 147, 251, 0.3);
}

.btn-delete {
    background: var(--payment-danger);
    color: white;
    box-shadow: 0 4px 12px rgba(253, 116, 108, 0.3);
}

.action-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
}

.dark .stat-card {
    background: #1e293b;
    border-color: #334155;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--payment-primary);
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 900;
    background: var(--payment-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
}

.stat-label {
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
}

.floating-add-btn {
    position: fixed;
    bottom: 32px;
    right: 32px;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--payment-primary);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
}

.floating-add-btn:hover {
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 12px 48px rgba(102, 126, 234, 0.6);
}

.search-filter {
    background: white;
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.dark .search-filter {
    background: #1e293b;
    border-color: #334155;
}

.search-input {
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    font-size: 16px;
    transition: all 0.3s ease;
    width: 100%;
    background: #f8fafc;
}

.dark .search-input {
    background: #334155;
    border-color: #475569;
    color: white;
}

.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
    background: white;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.dark .empty-state {
    background: #1e293b;
}

.empty-icon {
    font-size: 80px;
    margin-bottom: 24px;
    opacity: 0.3;
    background: var(--payment-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.morphing-border {
    border-radius: 20px;
    background: linear-gradient(45deg, #667eea, #764ba2, #667eea);
    background-size: 300% 300%;
    animation: morphing 4s ease-in-out infinite;
    padding: 2px;
}

@keyframes morphing {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.glass-effect {
    backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .handler-card {
        padding: 16px;
    }

    .payment-dashboard {
        padding: 20px;
    }

    .floating-add-btn {
        bottom: 20px;
        right: 20px;
        width: 56px;
        height: 56px;
        font-size: 20px;
    }
}
</style>

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-indigo-900 dark:to-purple-900 p-6">
    <!-- Dashboard Header -->
    <div class="payment-dashboard">
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center pulse-animation">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2">Payment Handlers</h1>
                        <p class="text-xl text-gray-600 dark:text-gray-300 font-medium">Управление платежными обработчиками и интеграциями</p>
                    </div>
                </div>

                <div class="hidden lg:flex items-center space-x-4">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $paymentHandlers->count() }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 uppercase tracking-wider">Обработчиков</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $paymentHandlers->count() }}</div>
            <div class="stat-label">Всего обработчиков</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $paymentHandlers->where('active', 1)->count() }}</div>
            <div class="stat-label">Активных</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $paymentHandlers->unique('currency')->count() }}</div>
            <div class="stat-label">Валют</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($paymentHandlers->avg('deposit_fee'), 2) }}%</div>
            <div class="stat-label">Средняя комиссия</div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-filter">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text"
                       id="searchInput"
                       class="search-input"
                       placeholder="🔍 Поиск по названию, валюте или системе...">
            </div>
            <div class="flex space-x-2">
                <button class="action-button btn-view" onclick="filterHandlers('all')">Все</button>
                <button class="action-button btn-edit" onclick="filterHandlers('active')">Активные</button>
                <button class="action-button btn-delete" onclick="filterHandlers('inactive')">Неактивные</button>
            </div>
        </div>
    </div>

    <!-- Payment Handlers Grid -->
    @if($paymentHandlers->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8" id="handlersGrid">
            @foreach($paymentHandlers as $handler)
                <div class="handler-card"
                     data-name="{{ strtolower($handler->name) }}"
                     data-currency="{{ strtolower($handler->currency) }}"
                     data-status="{{ $handler->active ? 'active' : 'inactive' }}">

                    <!-- Card Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            @if($handler->icon)
                                <img src="{{ asset('storage/' . $handler->icon) }}"
                                     alt="{{ $handler->name }}"
                                     class="handler-icon">
                            @else
                                <div class="handler-icon bg-gradient-to-r from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $handler->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">{{ $handler->currency }}</p>
                            </div>
                        </div>

                        <div class="status-toggle {{ $handler->active ? 'active' : 'inactive' }}"
                             onclick="toggleStatus({{ $handler->id }})">
                        </div>
                    </div>

                    <!-- Fees Section -->
                    <div class="fee-display">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-xs text-amber-800 dark:text-amber-200 font-semibold uppercase tracking-wide">Депозит</div>
                                <div class="text-lg font-bold text-amber-900 dark:text-amber-100">{{ $handler->deposit_fee }}%</div>
                            </div>
                            <div>
                                <div class="text-xs text-amber-800 dark:text-amber-200 font-semibold uppercase tracking-wide">Вывод</div>
                                <div class="text-lg font-bold text-amber-900 dark:text-amber-100">{{ $handler->withdrawal_fee }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Limits Section -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Лимиты депозита:</span>
                            <span class="limit-badge">{{ $handler->min_deposit_limit ?: '0' }} - {{ $handler->max_deposit_limit ?: '∞' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Лимиты вывода:</span>
                            <span class="limit-badge">{{ $handler->min_withdrawal_limit ?: '0' }} - {{ $handler->max_withdrawal_limit ?: '∞' }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('admin.payment_handlers.edit', $handler->id) }}"
                           class="action-button btn-edit text-center">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.payment_handlers.destroy', $handler->id) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="action-button btn-delete w-full"
                                    onclick="return confirm('Вы уверены, что хотите удалить этот обработчик?')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa fa-credit-card"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Нет платежных обработчиков</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                Создайте первый платежный обработчик для начала приема платежей
            </p>
            <a href="{{ route('admin.payment_handlers.create') }}"
               class="action-button btn-view inline-block">
                <i class="fa fa-plus mr-2"></i>
                Создать первый обработчик
            </a>
        </div>
    @endif

    <!-- Floating Add Button -->
    <button class="floating-add-btn" onclick="window.location.href='{{ route('admin.payment_handlers.create') }}'">
        <i class="fa fa-plus"></i>
    </button>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.handler-card');

    cards.forEach(card => {
        const name = card.dataset.name;
        const currency = card.dataset.currency;

        if (name.includes(searchTerm) || currency.includes(searchTerm)) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });
});

// Filter functionality
function filterHandlers(status) {
    const cards = document.querySelectorAll('.handler-card');

    cards.forEach(card => {
        const cardStatus = card.dataset.status;

        if (status === 'all' || cardStatus === status) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

// Status toggle (you would implement the actual API call)
function toggleStatus(handlerId) {
    // This would make an AJAX call to toggle the status
    console.log('Toggle status for handler:', handlerId);
    // For demo purposes, just toggle the visual state
    const toggle = event.target;
    toggle.classList.toggle('active');
    toggle.classList.toggle('inactive');
}

// Add some entrance animations
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.handler-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

@endsection
