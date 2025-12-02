@extends('panel')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4">

        <!-- Modern Header -->
        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 rounded-3xl shadow-2xl mb-8">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 via-purple-600/20 to-indigo-600/20"></div>
            <div class="relative p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">Ручные пополнения</h1>
                        <p class="text-blue-100 text-lg">Управление и модерация платежей</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="location.reload()"
                                class="px-6 py-3 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-semibold rounded-xl transition-all duration-200 border border-white/20">
                            <i class="fas fa-sync-alt mr-2"></i>Обновить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Ожидают</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['pending'] }}</p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full"></div>
            </div>

            <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-green-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-check text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Одобрено</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['approved'] }}</p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-emerald-400 to-green-500 rounded-full"></div>
            </div>

            <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-times text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide">Отклонено</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['rejected'] }}</p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-red-400 to-pink-500 rounded-full"></div>
            </div>

            <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-bar text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Всего</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full"></div>
            </div>
        </div>

        <!-- Modern Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-8">
            <form method="GET" class="flex flex-wrap items-end gap-6">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-search mr-2 text-gray-500"></i>Поиск по пользователю
                    </label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Username, email или ID"
                           class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200">
                </div>

                <div class="min-w-48">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-filter mr-2 text-gray-500"></i>Статус
                    </label>
                    <select name="status" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200">
                        <option value="">Все статусы</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ожидают</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Одобрено</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Отклонено</option>
                    </select>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-indigo-500/25">
                        <i class="fas fa-search mr-2"></i>Применить
                    </button>
                    <a href="{{ route('admin.manual-deposits.index') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>Сбросить
                    </a>
                </div>
            </form>
        </div>

        <!-- Modern Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-user mr-2"></i>Пользователь
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-money-bill mr-2"></i>Сумма
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-credit-card mr-2"></i>Система
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-2"></i>Статус
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-2"></i>Дата
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-image mr-2"></i>Чек
                            </th>
                            <th class="px-6 py-5 text-left text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-2"></i>Действия
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($manualPayments as $payment)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-700 dark:hover:to-gray-600 transition-all duration-200">
                            <!-- User Info -->
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-lg">
                                        {{ strtoupper(substr($payment->user->username, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $payment->user->username }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $payment->user->user_id }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                    <div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($payment->amount, 2) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->currency }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Payment System -->
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($payment->paymentHandler->icon)
                                    <img src="{{ asset('storage/' . $payment->paymentHandler->icon) }}" class="w-10 h-10 rounded-xl mr-3 shadow-sm">
                                    @endif
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->paymentHandler->name }}</span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-6 whitespace-nowrap">
                                @switch($payment->status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border border-amber-200">
                                            <div class="w-2 h-2 bg-amber-500 rounded-full mr-2 animate-pulse"></div>
                                            Ожидает
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 border border-emerald-200">
                                            <i class="fas fa-check text-emerald-600 mr-2"></i>
                                            Одобрено
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-gradient-to-r from-red-100 to-pink-100 text-red-800 border border-red-200">
                                            <i class="fas fa-times text-red-600 mr-2"></i>
                                            Отклонено
                                        </span>
                                        @break
                                @endswitch
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-6 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->created_at->format('d.m.Y') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Receipt -->
                            <td class="px-6 py-6 whitespace-nowrap">
                                @if($payment->receipt_path)
                                    <button onclick="openReceiptModal('{{ asset('storage/' . $payment->receipt_path) }}')"
                                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg shadow-blue-500/25">
                                        <i class="fas fa-eye mr-2"></i>Просмотр
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-xl text-sm">
                                        <i class="fas fa-image-slash mr-2"></i>Нет чека
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-6 whitespace-nowrap">
                                @if($payment->status === 'pending')
                                    <div class="flex space-x-2">
                                        <button onclick="showActionModal({{ $payment->id }}, 'approve', '{{ $payment->user->username }}', '{{ number_format($payment->amount, 2) }} {{ $payment->currency }}')"
                                                class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold text-sm rounded-xl transition-all duration-200 shadow-lg shadow-emerald-500/25">
                                            <i class="fas fa-check mr-1"></i>Одобрить
                                        </button>
                                        <button onclick="showActionModal({{ $payment->id }}, 'reject', '{{ $payment->user->username }}', '{{ number_format($payment->amount, 2) }} {{ $payment->currency }}')"
                                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold text-sm rounded-xl transition-all duration-200 shadow-lg shadow-red-500/25">
                                            <i class="fas fa-times mr-1"></i>Отклонить
                                        </button>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">
                                        @if($payment->approvedBy)
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 mr-2">
                                                    {{ strtoupper(substr($payment->approvedBy->username, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $payment->approvedBy->username }}</div>
                                                    <div class="text-xs">{{ $payment->approved_at->format('d.m.Y H:i') }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-inbox text-4xl text-gray-400"></i>
                                    </div>
                                    <p class="text-xl font-semibold mb-2">Нет ручных пополнений</p>
                                    <p class="text-sm">Как только пользователи создадут заявки, они появятся здесь</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($manualPayments->hasPages())
            <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $manualPayments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modern Receipt Modal -->
<div id="receiptModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeReceiptModal()"></div>
        <div class="inline-block bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all max-w-3xl w-full">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-image mr-3"></i>Чек об оплате
                    </h3>
                    <button onclick="closeReceiptModal()" class="text-white/80 hover:text-white p-2 rounded-xl hover:bg-white/20 transition-all duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <img id="receiptImage" src="" alt="Чек" class="max-w-full max-h-[70vh] mx-auto rounded-xl shadow-lg">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Action Modal -->
<div id="actionModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeActionModal()"></div>
        <div class="inline-block bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all max-w-lg w-full">
            <form id="actionForm" method="POST">
                @csrf
                <input type="hidden" name="action" id="actionType">

                <div id="modalHeader" class="p-6"></div>

                <div class="p-6 pt-0">
                    <div id="modalContent" class="mb-6"></div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            Комментарий администратора
                        </label>
                        <textarea name="admin_comment" id="adminComment" rows="3"
                                  class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-200"
                                  placeholder="Введите комментарий..."></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeActionModal()"
                            class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition-all duration-200">
                        Отмена
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-3 font-semibold rounded-xl transition-all duration-200">
                        Подтвердить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReceiptModal(imageSrc) {
    document.getElementById('receiptImage').src = imageSrc;
    document.getElementById('receiptModal').classList.remove('hidden');
}

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
}

function showActionModal(paymentId, action, username, amount) {
    const modal = document.getElementById('actionModal');
    const form = document.getElementById('actionForm');
    const actionType = document.getElementById('actionType');
    const modalHeader = document.getElementById('modalHeader');
    const modalContent = document.getElementById('modalContent');
    const submitBtn = document.getElementById('submitBtn');
    const adminComment = document.getElementById('adminComment');

    // Устанавливаем action и form action
    actionType.value = action;
    form.action = `{{ route('admin.manual-deposits.process', '') }}/${paymentId}`;

    // Настраиваем содержимое модала
    if (action === 'approve') {
        modalHeader.innerHTML = `
            <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Одобрить платеж</h3>
                        <p class="text-emerald-100">Подтвердите зачисление средств</p>
                    </div>
                </div>
            </div>
        `;
        modalContent.innerHTML = `
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-emerald-600 dark:text-emerald-400 mr-3"></i>
                    <div>
                        <p class="font-semibold text-emerald-800 dark:text-emerald-200">Пользователь: ${username}</p>
                        <p class="text-emerald-700 dark:text-emerald-300">Сумма: ${amount}</p>
                    </div>
                </div>
            </div>
        `;
        submitBtn.className = 'px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-emerald-500/25';
        submitBtn.textContent = 'Одобрить платеж';
        adminComment.placeholder = 'Платеж одобрен (необязательно)';
    } else {
        modalHeader.innerHTML = `
            <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-times text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Отклонить платеж</h3>
                        <p class="text-red-100">Укажите причину отклонения</p>
                    </div>
                </div>
            </div>
        `;
        modalContent.innerHTML = `
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mr-3"></i>
                    <div>
                        <p class="font-semibold text-red-800 dark:text-red-200">Пользователь: ${username}</p>
                        <p class="text-red-700 dark:text-red-300">Сумма: ${amount}</p>
                    </div>
                </div>
            </div>
        `;
        submitBtn.className = 'px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-red-500/25';
        submitBtn.textContent = 'Отклонить платеж';
        adminComment.placeholder = 'Причина отклонения (рекомендуется)';
    }

    adminComment.value = '';
    modal.classList.remove('hidden');
}

function closeActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
}
</script>

@endsection
