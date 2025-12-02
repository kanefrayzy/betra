@extends('panel')

@section('content')

<style>
/* Advanced Form Styles */
.form-wizard {
    max-width: 1000px;
    margin: 0 auto;
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    position: relative;
}

.dark .form-wizard {
    background: #1e293b;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.wizard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 32px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.wizard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.wizard-steps {
    display: flex;
    justify-content: center;
    margin-bottom: 32px;
    padding: 0 32px;
    position: relative;
    z-10;
}

.step {
    display: flex;
    align-items: center;
    margin: 0 16px;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 600;
    transition: all 0.3s ease;
}

.step.active {
    color: white;
    transform: scale(1.1);
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: white;
    color: #667eea;
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

.form-section {
    padding: 32px;
    display: none;
}

.form-section.active {
    display: block;
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.form-group-advanced {
    margin-bottom: 32px;
    position: relative;
}

.floating-label {
    position: relative;
    margin-bottom: 24px;
}

.floating-input {
    width: 100%;
    padding: 20px 16px 8px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    font-size: 16px;
    background: #f8fafc;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    appearance: none;
}

.dark .floating-input {
    background: #334155;
    border-color: #475569;
    color: white;
}

.floating-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
    background: white;
}

.dark .floating-input:focus {
    background: #475569;
}

.floating-label-text {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
    background: transparent;
    padding: 0 4px;
}

.floating-input:focus ~ .floating-label-text,
.floating-input:not(:placeholder-shown) ~ .floating-label-text {
    top: 0;
    transform: translateY(-50%);
    font-size: 12px;
    font-weight: 600;
    color: #667eea;
    background: white;
}

.dark .floating-input:focus ~ .floating-label-text,
.dark .floating-input:not(:placeholder-shown) ~ .floating-label-text {
    background: #1e293b;
}

.file-upload-zone {
    border: 3px dashed #cbd5e1;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    background: linear-gradient(45deg, #f8fafc 25%, transparent 25%),
                linear-gradient(-45deg, #f8fafc 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #f8fafc 75%),
                linear-gradient(-45deg, transparent 75%, #f8fafc 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}

.dark .file-upload-zone {
    background: linear-gradient(45deg, #334155 25%, transparent 25%),
                linear-gradient(-45deg, #334155 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #334155 75%),
                linear-gradient(-45deg, transparent 75%, #334155 75%);
    background-size: 20px 20px;
}

.file-upload-zone:hover {
    border-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
    transform: scale(1.02);
}

.file-upload-zone.dragover {
    border-color: #667eea;
    background-color: rgba(102, 126, 234, 0.1);
    transform: scale(1.05);
}

.upload-icon {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 16px;
    transition: all 0.3s ease;
}

.file-upload-zone:hover .upload-icon {
    color: #667eea;
    transform: scale(1.1);
}

.range-slider {
    position: relative;
    margin: 24px 0;
}

.range-input {
    width: 100%;
    height: 8px;
    border-radius: 4px;
    background: #e2e8f0;
    outline: none;
    appearance: none;
}

.range-input::-webkit-slider-thumb {
    appearance: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.range-input::-webkit-slider-thumb:hover {
    transform: scale(1.2);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.navigation-buttons {
    padding: 32px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    background: #f8fafc;
}

.dark .navigation-buttons {
    background: #334155;
    border-color: #475569;
}

.nav-button {
    padding: 16px 32px;
    border-radius: 16px;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.nav-button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transition: all 0.5s ease;
    transform: translate(-50%, -50%);
}

.nav-button:active::before {
    width: 300px;
    height: 300px;
}

.btn-previous {
    background: #64748b;
    color: white;
}

.btn-next,
.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
}

.btn-next:hover,
.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 36px rgba(102, 126, 234, 0.4);
}

.progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 2px;
}

@media (max-width: 768px) {
    .wizard-steps {
        flex-direction: column;
        align-items: center;
    }

    .step {
        margin: 8px 0;
    }

    .form-section {
        padding: 20px;
    }

    .navigation-buttons {
        padding: 20px;
        flex-direction: column;
        gap: 12px;
    }
}
</style>

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-indigo-900 dark:to-purple-900 py-12">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('admin.payment_handlers.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-arrow-left mr-2"></i>
                Назад к списку
            </a>
        </div>

        <!-- Form Wizard -->
        <div class="form-wizard">
            <!-- Header -->
            <div class="wizard-header">
                <div class="relative z-10">
                    <h1 class="text-3xl font-black text-white mb-4">Создание платежного обработчика</h1>
                    <p class="text-lg text-white opacity-90">Настройка нового способа приема платежей</p>
                </div>

                <!-- Steps -->
                <div class="wizard-steps">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <span class="hidden sm:inline">Основные данные</span>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <span class="hidden sm:inline">Комиссии</span>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <span class="hidden sm:inline">Лимиты</span>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <span class="hidden sm:inline">Завершение</span>
                    </div>
                </div>

                <div class="progress-bar" style="width: 25%"></div>
            </div>

            <form action="{{ route('admin.payment_handlers.store') }}" method="POST" enctype="multipart/form-data" id="paymentHandlerForm">
                @csrf

                <!-- Step 1: Basic Information -->
                <div class="form-section active" data-section="1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Основная информация</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="floating-label">
                            <input type="text" name="name" id="name" class="floating-input" placeholder=" " required>
                            <label class="floating-label-text">Название обработчика</label>
                        </div>

                        <div class="floating-label">
                            <select name="payment_system_id" id="payment_system_id" class="floating-input" required>
                                <option value="">Выберите платежную систему</option>
                                @foreach($paymentSystems as $system)
                                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                                @endforeach
                            </select>
                            <label class="floating-label-text">Платежная система</label>
                        </div>

                        <div class="floating-label">
                            <input type="text" name="currency" id="currency" class="floating-input" placeholder=" " required>
                            <label class="floating-label-text">Валюта (например: USD, EUR, RUB)</label>
                        </div>

                        <div class="floating-label">
                            <input type="url" name="url" id="url" class="floating-input" placeholder=" ">
                            <label class="floating-label-text">URL API</label>
                        </div>
                    </div>

                    <!-- Icon Upload -->
                    <div class="form-group-advanced">
                        <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">Иконка обработчика</label>
                        <div class="file-upload-zone" onclick="document.getElementById('icon').click()">
                            <input type="file" name="icon" id="icon" accept="image/*" style="display: none;">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Перетащите изображение или нажмите для выбора</div>
                            <div class="text-sm text-gray-500">PNG, JPG, SVG до 2MB</div>
                        </div>
                        <div id="imagePreview" class="mt-4 hidden"></div>
                    </div>
                </div>

                <!-- Step 2: Fees -->
                <div class="form-section" data-section="2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Настройка комиссий</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="form-group-advanced">
                            <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">Комиссия за депозит (%)</label>
                            <div class="floating-label">
                                <input type="number" name="deposit_fee" id="deposit_fee" class="floating-input" step="0.01" min="0" max="100" placeholder=" ">
                                <label class="floating-label-text">Процент комиссии</label>
                            </div>
                            <div class="range-slider">
                                <input type="range" class="range-input" min="0" max="10" step="0.1" value="0" onchange="document.getElementById('deposit_fee').value = this.value">
                            </div>
                        </div>

                        <div class="form-group-advanced">
                            <label class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">Комиссия за вывод (%)</label>
                            <div class="floating-label">
                                <input type="number" name="withdrawal_fee" id="withdrawal_fee" class="floating-input" step="0.01" min="0" max="100" placeholder=" ">
                                <label class="floating-label-text">Процент комиссии</label>
                            </div>
                            <div class="range-slider">
                                <input type="range" class="range-input" min="0" max="10" step="0.1" value="0" onchange="document.getElementById('withdrawal_fee').value = this.value">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Limits -->
                <div class="form-section" data-section="3">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Лимиты операций</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Лимиты депозитов</h3>
                            <div class="floating-label">
                                <input type="number" name="min_deposit_limit" id="min_deposit_limit" class="floating-input" step="0.01" min="0" placeholder=" ">
                                <label class="floating-label-text">Минимальная сумма депозита</label>
                            </div>
                            <div class="floating-label">
                                <input type="number" name="max_deposit_limit" id="max_deposit_limit" class="floating-input" step="0.01" min="0" placeholder=" ">
                                <label class="floating-label-text">Максимальная сумма депозита</label>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Лимиты выводов</h3>
                            <div class="floating-label">
                                <input type="number" name="min_withdrawal_limit" id="min_withdrawal_limit" class="floating-input" step="0.01" min="0" placeholder=" ">
                                <label class="floating-label-text">Минимальная сумма вывода</label>
                            </div>
                            <div class="floating-label">
                                <input type="number" name="max_withdrawal_limit" id="max_withdrawal_limit" class="floating-input" step="0.01" min="0" placeholder=" ">
                                <label class="floating-label-text">Максимальная сумма вывода</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Final Settings -->
                <div class="form-section" data-section="4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Финальные настройки</h2>

                    <div class="text-center">
                        <div class="inline-flex items-center space-x-4 bg-gray-100 dark:bg-gray-700 rounded-2xl p-6">
                            <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">Статус активности:</span>
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="active" value="1" id="active_yes" class="hidden" checked>
                                <label for="active_yes" class="cursor-pointer px-6 py-3 bg-green-500 text-white rounded-xl font-semibold transition-all hover:bg-green-600">
                                    Активен
                                </label>
                                <input type="radio" name="active" value="0" id="active_no" class="hidden">
                                <label for="active_no" class="cursor-pointer px-6 py-3 bg-gray-400 text-white rounded-xl font-semibold transition-all hover:bg-gray-500">
                                    Неактивен
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="mt-12 bg-gray-50 dark:bg-gray-700 rounded-2xl p-8">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Сводка настроек</h3>
                        <div id="summaryContent" class="space-y-4 text-gray-700 dark:text-gray-300">
                            <!-- Summary will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="navigation-buttons">
                    <button type="button" class="nav-button btn-previous" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Назад
                    </button>
                    <div></div>
                    <button type="button" class="nav-button btn-next" id="nextBtn" onclick="changeStep(1)">
                        Далее
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" class="nav-button btn-submit" id="submitBtn" style="display: none;">
                        <i class="fas fa-check mr-2"></i>
                        Создать обработчик
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 4;

// File upload handling
document.getElementById('icon').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="text-center">
                    <img src="${e.target.result}" class="w-32 h-32 object-cover rounded-2xl mx-auto shadow-lg">
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">${file.name}</p>
                </div>
            `;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Drag and drop
const uploadZone = document.querySelector('.file-upload-zone');

uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', () => {
    uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('icon').files = files;
        document.getElementById('icon').dispatchEvent(new Event('change'));
    }
});

// Step navigation
function changeStep(direction) {
    const newStep = currentStep + direction;

    if (newStep < 1 || newStep > totalSteps) return;

    // Hide current step
    document.querySelector(`[data-section="${currentStep}"]`).classList.remove('active');
    document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');

    // Show new step
    currentStep = newStep;
    document.querySelector(`[data-section="${currentStep}"]`).classList.add('active');
    document.querySelector(`[data-step="${currentStep}"]`).classList.add('active');

    // Update progress bar
    const progress = (currentStep / totalSteps) * 100;
    document.querySelector('.progress-bar').style.width = progress + '%';

    // Update buttons
    document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'block';
    document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'block';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';

    // Update summary on last step
    if (currentStep === totalSteps) {
        updateSummary();
    }
}

function updateSummary() {
    const formData = new FormData(document.getElementById('paymentHandlerForm'));
    const summaryContent = document.getElementById('summaryContent');

    summaryContent.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold mb-2">Основная информация:</h4>
                <p>Название: ${formData.get('name') || 'Не указано'}</p>
                <p>Валюта: ${formData.get('currency') || 'Не указана'}</p>
                <p>URL: ${formData.get('url') || 'Не указан'}</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Комиссии:</h4>
                <p>Депозит: ${formData.get('deposit_fee') || '0'}%</p>
                <p>Вывод: ${formData.get('withdrawal_fee') || '0'}%</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Лимиты депозитов:</h4>
                <p>Мин: ${formData.get('min_deposit_limit') || 'Не ограничено'}</p>
                <p>Макс: ${formData.get('max_deposit_limit') || 'Не ограничено'}</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Лимиты выводов:</h4>
                <p>Мин: ${formData.get('min_withdrawal_limit') || 'Не ограничено'}</p>
                <p>Макс: ${formData.get('max_withdrawal_limit') || 'Не ограничено'}</p>
            </div>
        </div>
    `;
}

// Radio button styling
document.querySelectorAll('input[name="active"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('label[for^="active_"]').forEach(label => {
            label.classList.remove('bg-green-500', 'bg-gray-400');
            label.classList.add('bg-gray-300');
        });

        if (this.value === '1') {
            document.querySelector('label[for="active_yes"]').classList.remove('bg-gray-300');
            document.querySelector('label[for="active_yes"]').classList.add('bg-green-500');
        } else {
            document.querySelector('label[for="active_no"]').classList.remove('bg-gray-300');
            document.querySelector('label[for="active_no"]').classList.add('bg-gray-400');
        }
    });
});

// Form validation before submission
document.getElementById('paymentHandlerForm').addEventListener('submit', function(e) {
    const requiredFields = ['name', 'payment_system_id', 'currency'];
    let isValid = true;

    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Пожалуйста, заполните все обязательные поля');
    }
});
</script>

@endsection
