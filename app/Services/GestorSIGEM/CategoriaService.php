<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\Cuadro;
use App\Models\SIGEM\CuadroCategoria;
use App\Models\SIGEM\CuadroDato;

class CategoriaService
{
    public function __construct(
        private Cuadro $cuadro,
        private CuadroCategoria $cuadroCategoria,
        private CuadroDato $cuadroDato,
    ) {}

    public function agregar(int $cuadro_id, array $datos): CuadroCategoria
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $datos['cuadro_id'] = $cuadro->cuadro_id;

        return $this->cuadroCategoria->create($datos);
    }

    public function actualizarCategorias(int $cuadro_id, array $categorias): void
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        foreach ($categorias as $categoria) {
            if (isset($categoria['categoria_id'])) {
                $cat = $this->cuadroCategoria->find($categoria['categoria_id']);
                if ($cat) {
                    $cat->update($categoria);
                }
            } else {
                $categoria['cuadro_id'] = $cuadro_id;
                $this->cuadroCategoria->create($categoria);
            }
        }
    }

    public function actualizarDato(int $cuadro_id, int $dato_id, string $valor): CuadroDato
    {
        $dato = $this->cuadroDato->find($dato_id);

        if (!$dato || $dato->cuadro_id != $cuadro_id) {
            throw new \RuntimeException('Dato no encontrado');
        }

        $dato->update(['valor' => $valor]);

        return $dato;
    }

    public function obtenerDato(int $dato_id): ?CuadroDato
    {
        return $this->cuadroDato->find($dato_id);
    }
}
