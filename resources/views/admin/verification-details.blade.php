@extends('panel')

<style>
    .verification-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .verification-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    .verification-card:hover {
        transform: translateY(-4px);
    }

    .card-header {
        background: linear-gradient(135deg, #7c69ef 0%, #4f46e5 100%);
        padding: 1.5rem;
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .user-info {
        padding: 2rem;
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        border-bottom: 1px solid #f1f5f9;
    }

    .user-avatar {
        width: 96px;
        height: 96px;
        border-radius: 20px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }

    .user-id {
        color: #64748b;
        font-size: 0.875rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.875rem;
        gap: 0.5rem;
    }

    .status-badge.pending {
        background: #fef9c3;
        color: #854d0e;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        padding: 2rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-label {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .info-value {
        color: #1e293b;
        font-size: 1rem;
        font-weight: 600;
    }

    .document-section {
        padding: 2rem;
    }

    .document-preview {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .document-preview img {
        width: 100%;
        height: 400px;
        object-fit: contain;
        background: #f8fafc;
    }

    .decision-form {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #1e293b;
        font-weight: 500;
    }

    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        color: #1e293b;
        background-color: white;
        transition: all 0.2s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #7c69ef;
        box-shadow: 0 0 0 3px rgba(124, 105, 239, 0.1);
    }

    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        min-height: 100px;
        resize: vertical;
        transition: all 0.2s ease;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #7c69ef;
        box-shadow: 0 0 0 3px rgba(124, 105, 239, 0.1);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7c69ef 0%, #4f46e5 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
</style>


@section('content')
<div class="verification-container">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Информация о пользователе -->
        <div class="verification-card">
            <div class="card-header">
                <i class="fas fa-user-circle mr-2"></i>
                Информация о пользователе
            </div>

            <div class="user-info">
                <img src="{{ $verification->user->avatar }}"
                     alt="Аватар пользователя"
                     class="user-avatar">

                <div class="user-details">
                    <div class="user-name">{{ $verification->user->username }}</div>
                    <div class="user-id">ID: {{ $verification->user->id }}</div>
                    <div class="mt-3">
                        @if($verification->status === 'pending')
                            <span class="status-badge pending">
                                <i class="fas fa-clock"></i>
                                Ожидает проверки
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Имя</span>
                    <span class="info-value">{{ $verification->first_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Фамилия</span>
                    <span class="info-value">{{ $verification->last_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Дата рождения</span>
                    <span class="info-value">{{ $verification->birth_date->format('d.m.Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Тип документа</span>
                    <span class="info-value">{{ $verification->document_type }}</span>
                </div>
            </div>
        </div>

        <!-- Документы -->
        <div class="verification-card">
            <div class="card-header">
                <i class="fas fa-file-alt mr-2"></i>
                Документы
            </div>
            <div class="document-section">
                <div class="document-preview">
                    <img src="{{ asset('storage/' . $verification->selfie) }}"
                         alt="Селфи с документом">
                </div>
            </div>
        </div>
    </div>

    <!-- Форма решения -->
    @if($verification->status === 'pending')
    <div class="verification-card mt-6">
        <div class="card-header">
            <i class="fas fa-check-circle mr-2"></i>
            Принять решение
        </div>
        <form action="{{ route('admin.verification.update', $verification->id) }}"
              method="POST"
              class="decision-form">
            @csrf
            <div class="form-group">
                <label class="form-label">Решение</label>
                <select name="status" class="form-select" id="verificationStatus">
                    <option value="approved">Подтвердить</option>
                    <option value="rejected">Отклонить</option>
                </select>
            </div>

            <div class="form-group" id="rejectReasonGroup" style="display: none;">
                <label class="form-label">Причина отказа</label>
                <textarea name="reject_reason"
                          class="form-textarea"
                          placeholder="Укажите причину отказа, чтобы пользователь мог исправить ошибки"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Сохранить решение
            </button>
        </form>
    </div>
    @endif
</div>

<script>
    document.getElementById('verificationStatus')?.addEventListener('change', function() {
        const rejectReasonGroup = document.getElementById('rejectReasonGroup');
        rejectReasonGroup.style.display = this.value === 'rejected' ? 'block' : 'none';
    });
</script>
@endsection
