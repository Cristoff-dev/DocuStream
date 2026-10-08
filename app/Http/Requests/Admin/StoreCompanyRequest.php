<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255', 'unique:companies,name'],
            'registration_number' => ['required', 'string', 'max:50', 'unique:companies,registration_number'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'                => 'A company with this name already exists.',
            'registration_number.unique' => 'This Tax ID is already registered.',
        ];
    }
}