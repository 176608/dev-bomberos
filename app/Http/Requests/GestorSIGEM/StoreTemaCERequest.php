<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTemaCERequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $ceTemaId = $this->route('id');

        return [
            'tema' => [
                'required',
                'string',
                'max:255',
                Rule::unique('consulta_express_tema', 'tema')->ignore($ceTemaId, 'ce_tema_id'),
            ],
        ];
    }
}
