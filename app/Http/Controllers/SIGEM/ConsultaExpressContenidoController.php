<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultaExpressContenido;

class ConsultaExpressContenidoController extends Controller
{
    public function update(Request $request, $id)
    {
        $contenido = ConsultaExpressContenido::findOrFail($id);

        $titulo = $request->input('titulo');
        $conceptos = $request->input('conceptos');
        $valores = $request->input('valores');

        if (!$titulo || !$conceptos || !$valores || count($conceptos) !== count($valores)) {
            return back()->with('error', 'Faltan datos o no coinciden los conceptos con los valores.');
        }

        $html = '<table style="width: 100%; border-collapse: collapse;" border="0">';
        $html .= '<thead><tr><td colspan="2"><strong><span style="color: #76933c;">' . e($titulo) . '</span></strong></td></tr></thead>';
        $html .= '<tbody>';

        foreach ($conceptos as $i => $concepto) {
            $valor = $valores[$i] ?? '';
            if (trim($concepto) === '' && trim($valor) === '') continue;

            $html .= '<tr>';
            $html .= '<td style="color: #76933c;">' . e($concepto) . '</td>';
            $html .= '<td style="color: #76933c;" align="right">' . e($valor) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        if (trim($html) === '') {
            return back()->with('error', 'No se pudo generar el contenido HTML.');
        }

        $contenido->ce_contenido = $html;
        $contenido->save();

        return back()->with('success', 'Contenido actualizado correctamente.');
    }
}
