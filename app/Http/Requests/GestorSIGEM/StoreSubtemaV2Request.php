<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubtemaV2Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'subtema_titulo' => 'required|string|max:255',
            'tema_id' => 'required|integer|exists:tema_v2,tema_id',
            'clave_subtema' => 'nullable|string|max:15',
            'orden_indice' => 'nullable|integer|min:0|max:999',
            'imagen' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
            'remove_imagen' => 'nullable|in:1,0',
            'publicado' => 'nullable|boolean',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $datos = parent::validated();
        $datos['publicado'] = $this->boolean('publicado');

        return $datos;
    }
}
