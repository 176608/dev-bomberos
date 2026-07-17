<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;

class StoreContenidoCERequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'ce_subtema_id' => 'required|integer|exists:consulta_express_subtema,ce_subtema_id',
            'titulo_tabla' => 'required|string|max:255',
            'pie_tabla' => 'nullable|string|max:500',
            'tabla_filas' => 'required|integer|min:1|max:50',
            'tabla_columnas' => 'required|integer|min:1|max:20',
        ];
    }
}
