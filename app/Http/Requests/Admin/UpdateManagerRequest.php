<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManagerRequest extends FormRequest
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

        $userId = $this->route('user')?->id;

        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', "unique:users,email,{$userId}"],
            'password'      => ['nullable', 'string', 'min:8', 'max:72'],
            'company_id'    => ['required', 'exists:companies,id'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:request_heavy_audits,delete_reports'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'      => 'This email is already used by another account.',
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }
}