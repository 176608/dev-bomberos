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
        $rules = [
            'ce_tema_id' => 'required|integer|exists:consulta_express_tema,ce_tema_id',
            'ce_subtema_nombre' => 'required|string|max:255',
            'titulo_tabla' => 'required|string|max:255',
            'pie_tabla' => 'nullable|string|max:500',
            'tabla_filas' => 'required|integer|min:1|max:50',
            'tabla_columnas' => 'required|integer|min:1|max:20',
        ];

        $filas = (int) $this->input('tabla_filas', 0);
        $columnas = (int) $this->input('tabla_columnas', 0);

        for ($f = 0; $f < $filas; $f++) {
            for ($c = 0; $c < $columnas; $c++) {
                $rules["celda_{$f}_{$c}"] = 'nullable|string';
            }
        }

        return $rules;
    }
}
