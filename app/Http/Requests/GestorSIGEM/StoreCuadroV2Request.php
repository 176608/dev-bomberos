<?php

namespace App\Http\Requests\GestorSIGEM;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuadroV2Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'subtema_id' => 'required|integer|exists:subtema_v2,subtema_id',
            'codigo_cuadro' => 'required|string|max:50',
            'c_titulo' => 'required|string|max:255',
            'c_subtitulo' => 'nullable|string|max:255',
            'publicado' => 'nullable|boolean',
            'tipo_mapa_pdf' => 'nullable|boolean',
            'permite_grafica' => 'nullable|boolean',
            'tipos_grafica_permitida' => 'nullable|json',
            'cabecera_gen' => 'nullable|string',
            'piepagina_gen' => 'nullable|string',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['subtema_id'] = 'sometimes|integer|exists:subtema_v2,subtema_id';
            $rules['codigo_cuadro'] = 'sometimes|string|max:50';
            $rules['c_titulo'] = 'sometimes|string|max:255';
        }

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $datos = parent::validated();
        $datos['publicado'] = $this->boolean('publicado');
        $datos['tipo_mapa_pdf'] = $this->boolean('tipo_mapa_pdf');
        $datos['permite_grafica'] = $this->boolean('permite_grafica');

        return $datos;
    }
}
