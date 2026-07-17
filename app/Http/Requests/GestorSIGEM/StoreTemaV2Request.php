<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTemaV2Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $temaId = $this->route('id');

        return [
            'tema_titulo' => 'required|string|max:255',
            'clave_tema' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('tema_v2', 'clave_tema')->ignore($temaId, 'tema_id'),
            ],
            'orden_indice' => 'nullable|integer|min:0|max:999',
            'publicado' => 'nullable|boolean',
            'color' => 'nullable|string|max:7',
            'icono' => 'nullable|string|max:50',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $datos = parent::validated();
        $datos['publicado'] = $this->boolean('publicado');

        return $datos;
    }
}
