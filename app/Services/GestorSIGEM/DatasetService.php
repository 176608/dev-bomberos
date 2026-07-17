<?php

namespace App\Services\GestorSIGEM;

use App\Models\SIGEM\Cuadro;
use Illuminate\Http\UploadedFile;

class DatasetService
{
    public function __construct(
        private Cuadro $cuadro,
    ) {}

    public function procesar(int $cuadro_id, UploadedFile $file): void
    {
        $cuadro = $this->cuadro->obtenerPorId($cuadro_id);

        if (!$cuadro) {
            throw new \RuntimeException('Cuadro no encontrado');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['csv', 'xlsx', 'xls'])) {
            throw new \InvalidArgumentException('Formato de archivo no soportado. Use CSV, XLSX o XLS.');
        }

        $this->parsearYAlmacenar($cuadro, $file, $extension);
    }

    protected function parsearYAlmacenar(Cuadro $cuadro, UploadedFile $file, string $extension): void
    {
        // TODO: Fase 2 — implementar parser de dataset
        //
        // Pendiente:
        // 1. Parsear CSV/XLSX a estructura de filas y columnas
        // 2. Identificar automáticamente encabezados (categorías)
        // 3. Crear CuadroCategoria para eje horizontal y vertical
        // 4. Crear CuadroDato para cada celda de datos
        // 5. Detectar tipos (dato, total, promedio, porcentual)
        //
        // throw new \RuntimeException('Parser de dataset no implementado');
    }
}
