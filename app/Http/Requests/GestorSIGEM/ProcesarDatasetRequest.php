<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;

class ProcesarDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->hasAnyRole(['Administrador', 'Desarrollador', 'Estadistico']);
    }

    public function rules(): array
    {
        return [
            'dataset' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ];
    }
}
