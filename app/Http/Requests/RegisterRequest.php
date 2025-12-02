<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Currency;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        // Используем symbol вместо name для проверки валюты
        $availableCurrencies = Currency::pluck('symbol')->toArray();

        return [
            'username' => ['required', 'string', 'max:30', 'min:3', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6'],
            'currency' => ['required', 'string', Rule::in($availableCurrencies)],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => __('Имя пользователя не может быть пустым'),
            'username.min' => __('Имя пользователя должно содержать минимум 3 символа'),
            'username.max' => __('Имя пользователя не должно превышать 30 символов'),
            'username.regex' => __('Имя пользователя может содержать только латинские буквы, цифры и нижнее подчеркивание'),
            'username.unique' => __('Это имя пользователя уже занято'),
            'email.required' => __('Укажите E-mail'),
            'email.email' => __('Неправильное значение для E-mail'),
            'email.unique' => __('Этот E-mail уже зарегистрирован в системе'),
            'password.required' => __('Укажите пароль'),
            'password.min' => __('Пароль не может быть меньше чем 6 символов'),
            'currency.required' => __('Выберите валюту'),
            'currency.in' => __('Недопустимая валюта'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
