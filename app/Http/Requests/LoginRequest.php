<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ];
    }


    public function messages(): array
    {
        return [
            'email.required' => __('errors.inem'),
            'email.email' => __('errors.badem'),
            'password.required' => __('errors.inpass'),
            'password.min' => __('errors.minpass'),
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
