<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Contracts\View\View;

class CuadroExcelExport implements FromView, WithTitle, WithDrawings, WithEvents
{
    private const LOGO_PATH = 'imagenes/IMIP_icon_text.png';
    private const LIMITE_AUTOANCHO_CELDAS = 20000;

    private int $totalCeldas = 0;
    private int $maxColumnas = 1;

    public function __construct(
        private string $codigoCuadro,
        private string $tituloCuadro,
        private string $subtituloCuadro,
        private ?string $piePagina,
        private array $seccionesData, // [['seccion' => [...], 'estado' => [...]], ...]
        private bool $mostrarLogo = true,
    ) {
        foreach ($seccionesData as $sd) {
            $estado = $sd['estado'] ?? [];
            foreach (($estado['data'] ?? []) as $fila) {
                $this->totalCeldas += count($fila);
            }

            $hdrs = $estado['headers'] ?? [];
            $numLabelCols = 1;
            if (!empty($hdrs) && !empty($hdrs[0]) && ($hdrs[0][0]['tipo'] ?? '') === 'corner') {
                $numLabelCols = $hdrs[0][0]['colspan'] ?? 1;
            }
            $cols = $numLabelCols + count($estado['horizontales'] ?? []);
            if ($cols > $this->maxColumnas) $this->maxColumnas = $cols;
        }

        $this->maxColumnas = max(1, $this->maxColumnas);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if ($this->totalCeldas > self::LIMITE_AUTOANCHO_CELDAS) return;

                $sheet = $event->sheet->getDelegate();
                for ($i = 1; $i <= $this->maxColumnas; $i++) {
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
                }
            },
        ];
    }

    public function view(): View
    {
        return view('exports.cuadro_excel', [
            'codigoCuadro' => $this->codigoCuadro,
            'tituloCuadro' => $this->tituloCuadro,
            'subtituloCuadro' => $this->subtituloCuadro,
            'piePagina' => $this->piePagina,
            'seccionesData' => $this->seccionesData,
            'mostrarLogo' => $this->mostrarLogo,
        ]);
    }

    public function title(): string
    {
        return mb_substr($this->codigoCuadro, 0, 31);
    }

    public function drawings()
    {
        $path = public_path(self::LOGO_PATH);
        if (!file_exists($path)) return [];

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath($path);
        $drawing->setHeight(90);
        $drawing->setCoordinates('A1');

        return [$drawing];
    }
}
