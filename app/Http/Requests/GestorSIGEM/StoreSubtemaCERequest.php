<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubtemaCERequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->hasAnyRole(['Administrador', 'Desarrollador', 'Estadistico']);
    }

    public function rules(): array
    {
        return [
            'ce_tema_id' => 'required|integer|exists:consulta_express_tema,ce_tema_id',
            'ce_subtema' => 'required|string|max:255',
        ];
    }
}
