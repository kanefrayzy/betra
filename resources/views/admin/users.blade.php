@extends('panel')
@php $baseUrl = 'panel8808'; @endphp

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-cyan-600 to-blue-700 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Управление пользователями</h1>
                    <p class="text-cyan-100 mt-1">Просмотр и управление всеми зарегистрированными пользователями</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-cyan-100 text-sm mb-1">Активных пользователей</div>
                <div class="text-2xl font-bold" id="users-count">Загрузка...</div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                        </svg>
                    </div>
                    Список пользователей
                </h2>
                <div class="flex items-center space-x-3">
                    <button
                        id="refresh-table"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                        </svg>
                        Обновить
                    </button>

                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Enhanced DataTable -->
            <div class="overflow-x-auto">
                <table id="datable_users" class="w-full">
                    <thead>
                        <tr class="text-left">
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Имя и фамилия</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Пользователь</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Баланс</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Уровень</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP адрес</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Блокировка</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="text-left">
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Имя и фамилия</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Пользователь</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Баланс</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Уровень</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP адрес</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Блокировка</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Action Modals -->
<!-- Block User Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="blockModal">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeBlockModal()"></div>

        <div class="inline-block w-full max-w-md my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
            <div class="bg-red-50 dark:bg-red-900/20 px-6 py-4 border-b border-red-200 dark:border-red-800">
                <h3 class="text-lg font-semibold text-red-800 dark:text-red-300">Заблокировать пользователя</h3>
            </div>

            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Вы уверены, что хотите заблокировать пользователя <strong id="blockUsername"></strong>?
                </p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Причина блокировки
                        </label>
                        <textarea
                            id="blockReason"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                            placeholder="Укажите причину блокировки..."
                        ></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-600">
                <button
                    onclick="closeBlockModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200"
                >
                    Отмена
                </button>
                <button
                    id="confirmBlock"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200"
                >
                    Заблокировать
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for Modern DataTable Styling -->
<style>
/* ========== DATATABLE MODERN REDESIGN ========== */

/* Контейнер таблицы */
.dataTables_wrapper {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    padding: 1.5rem;
}

/* Header элементы */
.datatable-header {
    margin-bottom: 1.5rem;
}

.datatable-header .flex {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

/* Поиск */
.dataTables_filter {
    flex: 1;
    min-width: 250px;
}

.dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    margin: 0;
    font-weight: 500;
    color: #6b7280;
}

.dark .dataTables_filter label {
    color: #9ca3af;
}

.dataTables_filter input,
.modern-search-input {
    flex: 1;
    border: 2px solid #e5e7eb !important;
    border-radius: 0.75rem !important;
    padding: 0.625rem 1rem !important;
    background: white !important;
    transition: all 0.2s ease !important;
    font-size: 0.875rem !important;
    width: 100% !important;
}

.dark .dataTables_filter input,
.dark .modern-search-input {
    border-color: #374151 !important;
    background: #1f2937 !important;
    color: white !important;
}

.dataTables_filter input:focus,
.modern-search-input:focus {
    outline: none !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

/* Length Menu */
.dataTables_length {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dataTables_length label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-weight: 500;
    color: #6b7280;
    white-space: nowrap;
}

.dark .dataTables_length label {
    color: #9ca3af;
}

.dataTables_length select,
.modern-select {
    border: 2px solid #e5e7eb !important;
    border-radius: 0.625rem !important;
    padding: 0.5rem 2rem 0.5rem 0.75rem !important;
    background: white url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") no-repeat right 0.5rem center !important;
    background-size: 1.25rem !important;
    appearance: none !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    font-weight: 600 !important;
}

.dark .dataTables_length select,
.dark .modern-select {
    border-color: #374151 !important;
    background-color: #1f2937 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
    color: white !important;
}

.dataTables_length select:hover,
.modern-select:hover {
    border-color: #3b82f6 !important;
}

/* Таблица */
#datable_users {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
}

#datable_users thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    padding: 1rem !important;
    border: none !important;
    position: sticky;
    top: 0;
    z-index: 10;
}

#datable_users thead th:first-child {
    border-top-left-radius: 0.75rem;
}

#datable_users thead th:last-child {
    border-top-right-radius: 0.75rem;
}

#datable_users tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f3f4f6;
}

.dark #datable_users tbody tr {
    border-bottom-color: #374151;
}

#datable_users tbody tr:hover {
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 51, 234, 0.05) 100%);
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.dark #datable_users tbody tr:hover {
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
}

#datable_users tbody td {
    padding: 1rem !important;
    vertical-align: middle;
    color: #1f2937;
}

.dark #datable_users tbody td {
    color: #f9fafb;
}

/* Footer */
.datatable-footer {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 2px solid #e5e7eb;
}

.dark .datatable-footer {
    border-top-color: #374151;
}

.dataTables_info {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.dark .dataTables_info {
    color: #9ca3af;
}

/* Пагинация */
.dataTables_paginate {
    display: flex;
    gap: 0.25rem;
}

.dataTables_paginate .paginate_button,
.modern-page-btn {
    border: 2px solid #e5e7eb !important;
    border-radius: 0.5rem !important;
    padding: 0.5rem 0.875rem !important;
    margin: 0 !important;
    background: white !important;
    color: #374151 !important;
    transition: all 0.2s ease !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    min-width: 2.5rem;
    text-align: center;
}

.dark .dataTables_paginate .paginate_button,
.dark .modern-page-btn {
    border-color: #374151 !important;
    background: #1f2937 !important;
    color: #d1d5db !important;
}

.dataTables_paginate .paginate_button:hover,
.modern-page-btn:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-color: #667eea !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.dataTables_paginate .paginate_button.current,
.modern-page-btn.current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-color: #667eea !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.dataTables_paginate .paginate_button.disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
    pointer-events: none;
}

/* Loading Spinner */
.dataTables_processing {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    width: auto !important;
    height: auto !important;
    margin: 0 !important;
    padding: 2rem 3rem !important;
    border: none !important;
    border-radius: 1rem !important;
    background: white !important;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2) !important;
    z-index: 9999 !important;
}

.dark .dataTables_processing {
    background: #1f2937 !important;
    color: white !important;
}

.spinner-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e5e7eb;
    border-top-color: #667eea;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.spinner-container p {
    margin: 0;
    font-weight: 600;
    color: #667eea;
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .dataTables_wrapper {
        padding: 1rem;
    }
    
    .datatable-header .flex {
        flex-direction: column;
    }
    
    .dataTables_filter,
    .dataTables_length {
        width: 100%;
    }
}
}
</style>

<script>
// Modal functions для блокировки (если нужно)
let currentUserId = null;

function showBlockModal(userId, username) {
    currentUserId = userId;
    document.getElementById('blockUsername').textContent = username;
    document.getElementById('blockReason').value = '';
    document.getElementById('blockModal').classList.remove('hidden');
}

function closeBlockModal() {
    document.getElementById('blockModal').classList.add('hidden');
    currentUserId = null;
}

function unblockUser(userId, username) {
    if (confirm(`Разблокировать пользователя ${username}?`)) {
        // Add your unblock logic here
        fetch(`/${cpBaseUrl}/users/${userId}/unblock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => {
            $('#datable_users').DataTable().ajax.reload();
        }).catch(error => {
            console.error('Error:', error);
            alert('Ошибка при разблокировке пользователя');
        });
    }
}

// Confirm block button
document.getElementById('confirmBlock').addEventListener('click', function() {
    const reason = document.getElementById('blockReason').value;
    if (!reason.trim()) {
        alert('Укажите причину блокировки');
        return;
    }

    // Add your block logic here
    fetch(`/${cpBaseUrl}/users/${currentUserId}/block`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    }).then(() => {
        closeBlockModal();
        $('#datable_users').DataTable().ajax.reload();
    }).catch(error => {
        console.error('Error:', error);
        alert('Ошибка при блокировке пользователя');
    });
});

// Close modal on escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeBlockModal();
    }
});
</script>

@endsection
