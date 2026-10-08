<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        if ($this->has('action')) {
            return [
                'action' => ['required', 'string', 'in:suspend,activate'],
            ];
        }

        $company = $this->route('company');
        $id = $company?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                "unique:companies,name,{$id},id",
            ],
            'registration_number' => [
                'required',
                'string',
                'max:50',
                "unique:companies,registration_number,{$id},id",
            ],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'                => 'Another company already uses this name.',
            'registration_number.unique' => 'This Tax ID belongs to another company.',
        ];
    }
}