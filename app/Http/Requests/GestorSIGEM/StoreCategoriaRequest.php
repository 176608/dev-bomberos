<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'eje' => 'required|in:horizontal,vertical',
            'padre_id' => 'nullable|integer|exists:cuadro_categoria,categoria_id',
            'nombre' => 'required|string|max:255',
            'orden' => 'required|integer|min:0',
            'tipo' => 'required|in:dato,total,promedio,porcentual',
        ];
    }
}
