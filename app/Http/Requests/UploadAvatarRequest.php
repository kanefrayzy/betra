<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'max:1024', 'mimes:jpg,jpeg,png,gif,webp'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Загрузите файл для аватара',
            'avatar.max' => 'Размер аватара не может превыщать 1 МБ',
            'avatar.image' => 'Фомат аватара должен быть изображением',
            'avatar.mimes' => 'Фомат аватара должен быть изображением (jpg, png, gif, webp)',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
