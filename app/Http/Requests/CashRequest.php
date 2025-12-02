<?php

namespace App\Http\Requests;

use App\Models\Currency;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CashRequest extends FormRequest
{
    public function rules(): array
    {
        return ;
    }

    public function messages(): array
    {
        return ;
    }


    public function authorize(): bool
    {
        return true;
    }
}
