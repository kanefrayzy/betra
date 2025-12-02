@extends('panel')
@php $baseUrl = 'dicex'; @endphp

@section('content')

<!-- Reusing the same styles from create page -->
<style>
.form-container {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    max-width: 800px;
    margin: 0 auto;
}

.dark .form-container {
    background: #1f2937;
    border-color: #374151;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-weight: 700;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.dark .form-label {
    color: #e5e7eb;
}

.form-control {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: white;
}

.dark .form-control {
    background: #374151;
    border-color: #4b5563;
    color: white;
}

.form-control:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    outline: none;
}

.file-upload {
    position: relative;
    overflow: hidden;
    display: inline-block;
    width: 100%;
}

.file-upload input[type=file] {
    position: absolute;
    left: -9999px;
}

.file-upload-label {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f9fafb;
}

.dark .file-upload-label {
    background: #374151;
    border-color: #4b5563;
}

.file-upload-label:hover {
    border-color: #f59e0b;
    background: #fef3c7;
}

.dark .file-upload-label:hover {
    background: #78350f;
}

.btn-submit {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border: none;
    color: white;
    padding: 16px 32px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(245, 158, 11, 0.4);
}

.btn-back {
    background: #6b7280;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    margin-bottom: 24px;
}

.btn-back:hover {
    background: #4b5563;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-description {
    background: #fef3c7;
    border: 1px solid #fbbf24;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
}

.dark .form-description {
    background: #78350f;
    border-color: #92400e;
}

.form-description p {
    margin: 0;
    color: #92400e;
    font-size: 14px;
}

.dark .form-description p {
    color: #fbbf24;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .form-container {
        padding: 24px;
        margin: 16px;
    }
}
</style>

<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('admin.ranks.index') }}" class="btn-back">
            <i class="fas fa-arrow-left mr-2"></i>
            Назад к списку рангов
        </a>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Редактирование ранга</h1>
            <p class="text-gray-600 dark:text-gray-400">Изменение параметров ранга "{{ $rank->name }}"</p>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <form action="{{ route('admin.ranks.update', $rank->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Info -->
                <div class="form-group">
                    <label class="form-label" for="name">
                        <i class="fas fa-tag mr-2"></i>Название ранга
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control"
                           value="{{ $rank->name }}"
                           required>
                </div>

                <!-- Current Picture & Upload -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image mr-2"></i>Иконка ранга
                    </label>

                    @if($rank->picture)
                        <div class="image-preview-container">
                            <div class="text-sm text-amber-800 dark:text-amber-200 mb-2">Текущая иконка:</div>
                            <img src="{{ asset('storage/' . $rank->picture) }}"
                                 alt="Current rank picture"
                                 class="current-image">
                        </div>
                    @endif

                    <div class="file-upload">
                        <input type="file" name="picture" id="picture" accept="image/*">
                        <label for="picture" class="file-upload-label">
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $rank->picture ? 'Нажмите для замены изображения' : 'Нажмите для выбора изображения' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    PNG, JPG до 2MB
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Turnover Range -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="oborot_min">
                            <i class="fas fa-arrow-up mr-2"></i>Минимальный оборот
                        </label>
                        <input type="number"
                               name="oborot_min"
                               id="oborot_min"
                               class="form-control"
                               value="{{ $rank->oborot_min }}"
                               min="0"
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="oborot_max">
                            <i class="fas fa-arrow-up mr-2"></i>Максимальный оборот
                        </label>
                        <input type="number"
                               name="oborot_max"
                               id="oborot_max"
                               class="form-control"
                               value="{{ $rank->oborot_max }}"
                               min="0"
                               required>
                    </div>
                </div>

                <!-- Rakeback -->
                <div class="form-group">
                    <label class="form-label" for="rakeback">
                        <i class="fas fa-percentage mr-2"></i>Рейкбек (%)
                    </label>
                    <input type="number"
                           name="rakeback"
                           id="rakeback"
                           class="form-control"
                           value="{{ $rank->rakeback }}"
                           step="0.00001"
                           min="0"
                           max="100">
                </div>

                <!-- Daily Bonus Range -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="daily_min">
                            <i class="fas fa-gift mr-2"></i>Дневной бонус мин ($)
                        </label>
                        <input type="number"
                               name="daily_min"
                               id="daily_min"
                               class="form-control"
                               value="{{ $rank->daily_min }}"
                               step="0.01"
                               min="0">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="daily_max">
                            <i class="fas fa-gift mr-2"></i>Дневной бонус макс ($)
                        </label>
                        <input type="number"
                               name="daily_max"
                               id="daily_max"
                               class="form-control"
                               value="{{ $rank->daily_max }}"
                               step="0.01"
                               min="0">
                    </div>
                </div>

                <input type="hidden" name="percent" value="1">

                <!-- Submit Button -->
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save mr-2"></i>
                    Сохранить изменения
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// File upload preview (same as create page)
document.getElementById('picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const label = document.querySelector('.file-upload-label');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            label.innerHTML = `
                <div class="text-center">
                    <img src="${e.target.result}" class="w-16 h-16 object-cover rounded-lg mx-auto mb-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400">${file.name}</div>
                    <div class="text-xs text-gray-500 mt-1">Нажмите для изменения</div>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>

@endsection
