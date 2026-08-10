<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\TemaV2;
use App\Services\HtmlSanitizer;
use Illuminate\Support\Facades\Storage;

class CuadroV2Service
{
    public function __construct(
        private Cuadro $cuadro,
    ) {}

    public function listar(): array
    {
        $cuadros = $this->cuadro->obtenerTodos();
        $temas = TemaV2::with('subtemas')->orderBy('orden_indice', 'asc')->get();

        return [
            'cuadros' => $cuadros,
            'temas' => $temas,
        ];
    }

    public function crear(array $datos): Cuadro
    {
        $datos['publicado'] = $datos['publicado'] ?? false;
        $datos['tipo_mapa_pdf'] = $datos['tipo_mapa_pdf'] ?? false;
        $datos['permite_grafica'] = $datos['permite_grafica'] ?? false;

        if (isset($datos['pie_pagina'])) {
            $datos['pie_pagina'] = HtmlSanitizer::sanitize($datos['pie_pagina']);
        }
        if (isset($datos['piepagina_gen'])) {
            $datos['piepagina_gen'] = HtmlSanitizer::sanitize($datos['piepagina_gen']);
        }

        if (isset($datos['tipos_grafica_permitida']) && is_array($datos['tipos_grafica_permitida'])) {
            $datos['tipos_grafica_permitida'] = json_encode($datos['tipos_grafica_permitida']);
        }

        return $this->cuadro->crear($datos);
    }

    public function obtenerPorId(int $id): ?Cuadro
    {
        return $this->cuadro->obtenerPorId($id);
    }

    public function actualizar(int $id, array $datos): Cuadro
    {
        $cuadro = $this->cuadro->obtenerPorId($id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $datos['publicado'] = $datos['publicado'] ?? $cuadro->publicado;
        $datos['tipo_mapa_pdf'] = $datos['tipo_mapa_pdf'] ?? $cuadro->tipo_mapa_pdf;
        $datos['permite_grafica'] = $datos['permite_grafica'] ?? $cuadro->permite_grafica;

        if (array_key_exists('pie_pagina', $datos)) {
            $datos['pie_pagina'] = HtmlSanitizer::sanitize($datos['pie_pagina']);
        }
        if (array_key_exists('piepagina_gen', $datos)) {
            $datos['piepagina_gen'] = HtmlSanitizer::sanitize($datos['piepagina_gen']);
        }

        if (isset($datos['tipos_grafica_permitida']) && is_array($datos['tipos_grafica_permitida'])) {
            $datos['tipos_grafica_permitida'] = json_encode($datos['tipos_grafica_permitida']);
        }

        $cuadro->actualizar($datos);

        return $cuadro;
    }

    public function eliminar(int $id): string
    {
        $cuadro = $this->cuadro->obtenerPorId($id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        if ($cuadro->pdf_file) {
            Storage::disk('mapas')->delete($cuadro->pdf_file);
        }

        $codigo = $cuadro->codigo_cuadro;
        $cuadro->eliminar();

        return $codigo;
    }

    public function togglePublicado(int $id): bool
    {
        $cuadro = $this->cuadro->obtenerPorId($id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $cuadro->publicado = !$cuadro->publicado;
        $cuadro->save();

        return $cuadro->publicado;
    }

    public function datosJson(int $id): ?array
    {
        $cuadro = $this->cuadro->obtenerPorId($id);

        if (!$cuadro) {
            return null;
        }

        $data = $cuadro->toArray();
        $data['tema_id'] = $cuadro->subtema ? $cuadro->subtema->tema_id : null;

        return $data;
    }
}
