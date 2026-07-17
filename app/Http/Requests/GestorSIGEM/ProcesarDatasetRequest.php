<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;

class ProcesarDatasetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'dataset' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ];
    }
}
