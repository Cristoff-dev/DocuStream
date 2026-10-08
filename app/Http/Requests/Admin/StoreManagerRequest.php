<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore(
                    \App\Models\User::withTrashed()->where('email', $this->email)->value('id')
                ),
            ],
            'password'     => ['required', 'string', 'min:8', 'max:72'],
            'company_id'   => ['required', 'exists:companies,id'],
            'permissions'  => ['nullable', 'array'],
            'permissions.*'=> ['string', 'in:request_heavy_audits,delete_reports'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'      => 'This email address is already in use.',
            'company_id.exists' => 'The selected company does not exist.',
            'password.min'      => 'Password must be at least 8 characters.',
        ];
    }
}