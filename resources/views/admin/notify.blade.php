@extends('panel')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Массовая рассылка уведомлений</h1>
                    <p class="text-indigo-100 mt-1">Отправка персонализированных сообщений пользователям</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-indigo-100 text-sm mb-1">Всего пользователей</div>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <span class="text-2xl font-bold">{{ number_format($userCount) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Notification Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('admin.sendMassNotification') }}" method="POST" id="notificationForm" class="space-y-6">
                @csrf

                <!-- User ID Input -->
                <div class="space-y-2">
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <span>ID пользователя</span>
                        </div>
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="user_id"
                            name="user_id"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 @error('user_id') border-red-500 ring-2 ring-red-500 @enderror"
                            placeholder="Оставьте пустым для массовой рассылки"
                            value="{{ old('user_id') }}"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    @error('user_id')
                        <div class="flex items-center mt-2 text-sm text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- User Info Display -->
                    <div id="user_info" class="mt-3 transition-all duration-300"></div>
                </div>

                <!-- Message Input -->
                <div class="space-y-2">
                    <label for="message" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span>Текст уведомления</span>
                            <span class="text-red-500">*</span>
                        </div>
                    </label>
                    <div class="relative">
                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none transition-all duration-200 @error('message') border-red-500 ring-2 ring-red-500 @enderror"
                            placeholder="Введите текст уведомления..."
                            required
                        >{{ old('message') }}</textarea>
                        <div class="absolute bottom-3 right-3 text-xs text-gray-400 dark:text-gray-500" id="char-count">
                            0 символов
                        </div>
                    </div>
                    @error('message')
                        <div class="flex items-center mt-2 text-sm text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <p class="font-medium mb-1">Информация о рассылке:</p>
                            <ul class="space-y-1 text-blue-700 dark:text-blue-300">
                                <li>• Если ID пользователя не указан, уведомление будет отправлено всем пользователям</li>
                                <li>• Массовая рассылка выполняется в фоновом режиме</li>
                                <li>• Уведомления доставляются мгновенно активным пользователям</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span id="recipient-info">Готово к отправке</span>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-all duration-200 transform hover:scale-105 shadow-lg"
                        id="submit-btn"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                        </svg>
                        <span id="submit-text">Отправить уведомление</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#notificationForm');
    const userIdInput = document.querySelector('#user_id');
    const messageInput = document.querySelector('#message');
    const userInfoDiv = document.querySelector('#user_info');
    const charCount = document.querySelector('#char-count');
    const recipientInfo = document.querySelector('#recipient-info');
    const submitBtn = document.querySelector('#submit-btn');
    const submitText = document.querySelector('#submit-text');

    // Character counter
    function updateCharCount() {
        const count = messageInput.value.length;
        charCount.textContent = `${count} символов`;
        charCount.className = count > 500 ? 'text-red-500' : 'text-gray-400 dark:text-gray-500';
    }

    messageInput.addEventListener('input', updateCharCount);
    updateCharCount();

    // Update recipient info
    function updateRecipientInfo() {
        const userId = userIdInput.value.trim();
        if (userId) {
            recipientInfo.textContent = `Отправка для пользователя ID: ${userId}`;
            submitText.textContent = 'Отправить пользователю';
        } else {
            recipientInfo.textContent = `Массовая рассылка для {{ $userCount }} пользователей`;
            submitText.textContent = 'Отправить всем';
        }
    }

    userIdInput.addEventListener('input', updateRecipientInfo);
    updateRecipientInfo();

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const userId = userIdInput.value.trim();

        if (userId) {
            this.submit();
        } else {
            const userCount = {{ $userCount }};

            // Show confirmation modal
            const confirmModal = document.createElement('div');
            confirmModal.className = 'fixed inset-0 z-50 overflow-y-auto';
            confirmModal.innerHTML = `
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                    <div class="inline-block w-full max-w-md my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 px-6 py-4 border-b border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300">Подтверждение массовой рассылки</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                Вы уверены, что хотите отправить уведомление <strong>${userCount.toLocaleString()}</strong> пользователям?
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Это действие нельзя отменить. Уведомление будет добавлено в очередь и отправлено в фоновом режиме.
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200 dark:border-gray-600">
                            <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200">
                                Отмена
                            </button>
                            <button onclick="confirmSend()" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg transition-colors duration-200">
                                Отправить
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(confirmModal);

            window.confirmSend = () => {
                confirmModal.remove();

                // Show success message
                const successToast = document.createElement('div');
                successToast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300';
                successToast.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Уведомление добавлено в очередь отправки</span>
                    </div>
                `;
                document.body.appendChild(successToast);

                setTimeout(() => successToast.classList.remove('translate-x-full'), 100);
                setTimeout(() => {
                    successToast.classList.add('translate-x-full');
                    setTimeout(() => document.body.removeChild(successToast), 300);
                }, 5000);

                form.submit();
            };
        }
    });

    // Dynamic user info loading
    let timeout;
    userIdInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const userId = this.value.trim();

        if (userId) {
            timeout = setTimeout(() => {
                // Show loading state
                userInfoDiv.innerHTML = `
                    <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                        <div class="animate-spin w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                        <span class="text-sm">Поиск пользователя...</span>
                    </div>
                `;

                const url = `{{ route('admin.getUserInfo') }}?user_id=${userId}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            userInfoDiv.innerHTML = `
                                <div class="flex items-center space-x-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Пользователь найден</p>
                                        <p class="text-sm text-green-700 dark:text-green-300">${data.user.username} (${data.user.email})</p>
                                    </div>
                                </div>
                            `;
                        } else {
                            userInfoDiv.innerHTML = `
                                <div class="flex items-center space-x-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-red-800 dark:text-red-200">Пользователь не найден</p>
                                        <p class="text-sm text-red-700 dark:text-red-300">${data.message || 'Проверьте правильность ID'}</p>
                                    </div>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        userInfoDiv.innerHTML = `
                            <div class="flex items-center space-x-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-red-800 dark:text-red-200">Ошибка загрузки</p>
                                    <p class="text-sm text-red-700 dark:text-red-300">Не удалось получить информацию о пользователе</p>
                                </div>
                            </div>
                        `;
                    });
            }, 300);
        } else {
            userInfoDiv.innerHTML = '';
        }
    });

    // Check initial value
    if (userIdInput.value.trim()) {
        userIdInput.dispatchEvent(new Event('input'));
    }
});
</script>

@endsection
