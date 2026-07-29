<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Contracts\View\View;

class CuadroExcelExport implements FromView, ShouldAutoSize, WithTitle, WithDrawings
{
    private const LOGO_PATH = 'imagenes/logoadmin.png';

    public function __construct(
        private string $codigoCuadro,
        private string $tituloCuadro,
        private string $subtituloCuadro,
        private ?string $piePagina,
        private array $seccionesData, // [['seccion' => [...], 'estado' => [...]], ...]
        private bool $mostrarLogo = true,
    ) {}

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
        $drawing->setHeight(45);
        $drawing->setCoordinates('A1');

        return [$drawing];
    }
}
