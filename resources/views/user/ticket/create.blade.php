<x-layouts.app>
    <div class="support-ticket-create-container">
        <h1 class="support-ticket-title">{{ __('Создать новый тикет') }}</h1>

        <form action="{{ route('support.ticket.store') }}" method="POST" class="support-ticket-form">
            @csrf
            <div class="support-ticket-form-group">
                <label for="subject" class="support-ticket-label">{{ __('Тема') }}</label>
                <input type="text" class="support-ticket-input" id="subject" name="subject" required placeholder="{{ __('Введите тему') }}">
            </div>

            <button type="submit" class="support-ticket-submit-btn">{{ __('Создать тикет') }}</button>
        </form>
    </div>
</x-layouts.app>

<style>
    .support-ticket-create-container {
        background-color: #2b2d33;
        padding: 30px;
        border-radius: 10px;
        max-width: 600px;
        margin: 0 auto;
        color: white;
    }

    .support-ticket-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .support-ticket-form-group {
        margin-bottom: 20px;
    }

    .support-ticket-label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .support-ticket-input {
        width: 100%;
        padding: 10px;
        background-color: #35383e;
        border: 1px solid #444;
        border-radius: 8px;
        color: white;
        font-size: 14px;
    }

    .support-ticket-input::placeholder {
        color: #b9bbbe;
    }

    .support-ticket-submit-btn {
        background-color: #4a90e2;
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .support-ticket-submit-btn:hover {
        background-color: #357ab8;
    }
</style>
