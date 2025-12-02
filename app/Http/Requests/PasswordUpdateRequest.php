<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'curr_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'curr_password.required' => __('errors.tekpass'),
            'password.required' => __('errors.newpass'),
            'password.min' => __('errors.minpass'),
            'password.confirmed' => __('errors.nesov'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
