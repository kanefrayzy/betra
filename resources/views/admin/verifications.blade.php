@extends('panel')
@section('content')

<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-8 text-white">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur">
                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold">Верификации пользователей</h1>
                <p class="text-orange-100 mt-1">Управление документами и подтверждение личности</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <form action="{{ route('admin.verifications') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Поиск по логину</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            placeholder="Введите логин..."
                            value="{{ request('search') }}"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Date From -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Дата от</label>
                    <input
                        type="date"
                        name="date_from"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        value="{{ request('date_from') }}"
                    >
                </div>

                <!-- Date To -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Дата до</label>
                    <input
                        type="date"
                        name="date_to"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        value="{{ request('date_to') }}"
                    >
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Статус</label>
                    <select
                        name="status"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                    >
                        <option value="">Все статусы</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Ожидает</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Подтверждено</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Отклонено</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Действия</label>
                    <div class="flex space-x-2">
                        <button
                            type="submit"
                            class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <a
                            href="{{ route('admin.verifications') }}"
                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center justify-center"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Verifications List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Пользователь</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Документы</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Данные</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($verifications as $verification)
                        <tr id="verification-row-{{ $verification['id'] }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <!-- ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                                        {{ $verification['id'] }}
                                    </div>
                                </div>
                            </td>

                            <!-- User -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <img
                                        src="{{ $verification['avatar'] }}"
                                        alt="{{ $verification['username'] }}"
                                        class="w-12 h-12 rounded-xl object-cover border-2 border-gray-200 dark:border-gray-600"
                                    >
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $verification['username'] }}</div>
                                        <div class="mt-1">
                                            @switch($verification['status'])
                                                @case('pending')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                        <div class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5 animate-pulse"></div>
                                                        Ожидает
                                                    </span>
                                                    @break
                                                @case('approved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Подтверждено
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Отклонено
                                                    </span>
                                            @endswitch
                                        </div>

                                        @if($verification['bonus_info']['has_active_bonus'])
                                            <div class="mt-2">
                                                @foreach($verification['bonus_info']['active_bonuses'] as $bonus)
                                                    <div class="flex items-center space-x-1 text-xs text-emerald-600 dark:text-emerald-400">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="font-medium">{{ $bonus['name'] }}</span>
                                                        <span class="text-emerald-500">+{{ $bonus['amount'] }} &#8380;</span>
                                                        <span class="text-gray-400">{{ $bonus['created_at'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Documents -->
                            <td class="px-6 py-4">
                                <div class="space-y-3">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $verification['document_type'] }}
                                        </span>
                                    </div>
                                    <div class="relative group">
                                        <img
                                            src="{{ asset('storage/' . $verification['selfie']) }}"
                                            class="w-24 h-16 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600 cursor-pointer transition-all duration-200 group-hover:scale-105 group-hover:shadow-lg"
                                            data-toggle="modal"
                                            data-target="#docsModal-{{ $verification['id'] }}"
                                            alt="Документ"
                                        >
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <button
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium transition-colors duration-200"
                                        data-toggle="modal"
                                        data-target="#docsModal-{{ $verification['id'] }}"
                                    >
                                        Увеличить
                                    </button>
                                </div>
                            </td>

                            <!-- Data -->
                            <td class="px-6 py-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Имя</div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $verification['first_name'] }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Фамилия</div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $verification['last_name'] }}</div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата рождения</div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $verification['birth_date'] }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                @if($verification['status'] === 'pending')
                                    <div class="flex flex-col space-y-2">
                                        <button
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105"
                                            onclick="approveVerification({{ $verification['id'] }})"
                                        >
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Подтвердить
                                        </button>
                                        <button
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105"
                                            onclick="showRejectModal({{ $verification['id'] }})"
                                        >
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Отклонить
                                        </button>
                                    </div>
                                @else
                                    <div class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        {{ $verification['status'] === 'approved' ? 'Подтверждено' : 'Отклонено' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
            {{ $verifications->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Document Modal -->
@foreach($verifications as $verification)
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="docsModal-{{ $verification['id'] }}">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeModal('docsModal-{{ $verification['id'] }}')"></div>

        <div class="inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Документы {{ $verification['username'] }}
                    </h3>
                    <button
                        onclick="closeModal('docsModal-{{ $verification['id'] }}')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="flex justify-center">
                    <img
                        src="{{ asset('storage/' . $verification['selfie']) }}"
                        class="max-w-full max-h-96 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600"
                        alt="Селфи с документом"
                    >
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Reject Modal -->
<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="rejectModal">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeModal('rejectModal')"></div>

        <div class="inline-block w-full max-w-md my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
            <div class="bg-red-50 dark:bg-red-900/20 px-6 py-4 border-b border-red-200 dark:border-red-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-300">
                        Отклонить верификацию
                    </h3>
                    <button
                        onclick="closeModal('rejectModal')"
                        class="text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors duration-200"
                    >
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="rejectForm">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Причина отклонения
                            </label>
                            <textarea
                                name="reject_reason"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                                placeholder="Укажите причину отклонения..."
                                required
                            ></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-600">
                <button
                    onclick="closeModal('rejectModal')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200"
                >
                    Отмена
                </button>
                <button
                    onclick="rejectVerification()"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200"
                >
                    Отклонить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentVerificationId = null;

function showRejectModal(id) {
    currentVerificationId = id;
    document.querySelector('#rejectForm textarea').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function approveVerification(id) {
    fetch(`/qwdkox1i20/verifications/${id}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: 'approved'
        })
    }).then(() => {
        window.location.reload();
    });
}

function rejectVerification() {
    const reason = document.querySelector('#rejectForm textarea').value;
    if (!reason) {
        alert('Укажите причину отклонения');
        return;
    }

    fetch(`/qwdkox1i20/verifications/${currentVerificationId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            status: 'rejected',
            reject_reason: reason
        })
    }).then(() => {
        closeModal('rejectModal');
        window.location.reload();
    });
}

// Open modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle image preview clicks
    document.querySelectorAll('[data-toggle="modal"]').forEach(element => {
        element.addEventListener('click', function() {
            const target = this.getAttribute('data-target').substring(1);
            document.getElementById(target).classList.remove('hidden');
        });
    });

    // Handle image error fallback
    const previewImages = document.querySelectorAll('img');
    previewImages.forEach(img => {
        img.onerror = function() {
            this.src = '/assets/images/og-image.png';
            this.onerror = null;
        };
    });
});
</script>

@endsection
