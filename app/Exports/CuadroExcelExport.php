<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class CuadroExcelExport implements FromView, ShouldAutoSize, WithTitle
{
    public function __construct(
        private array $estado,
        private string $codigoCuadro,
        private string $tituloCuadro,
        private string $subtituloCuadro,
        private ?string $piePagina,
    ) {}

    public function view(): View
    {
        return view('exports.cuadro_excel', [
            'estado' => $this->estado,
            'codigoCuadro' => $this->codigoCuadro,
            'tituloCuadro' => $this->tituloCuadro,
            'subtituloCuadro' => $this->subtituloCuadro,
            'piePagina' => $this->piePagina,
        ]);
    }

    public function title(): string
    {
        return mb_substr($this->codigoCuadro, 0, 31);
    }
}
