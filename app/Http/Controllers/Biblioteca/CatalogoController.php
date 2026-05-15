<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Bomberos\Controller; 
use App\Models\Biblioteca\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    /**
     * Vista PÚBLICA del catálogo (home)
     */
    public function publicIndex(Request $request)
    {
        $query = trim($request->input('q', ''));
        $activeMaterial = $request->input('material');
        
        $books = Book::query();

        if ($activeMaterial) {
            $books->where('tipo_material', $activeMaterial);
        }

        if ($query) {
            $searchTerm = '%' . mb_strtolower($query, 'UTF-8') . '%';
            $books->where(function ($q) use ($searchTerm) {
                $q->whereRaw("LOWER(TRIM(titulo)) LIKE ?", [$searchTerm])
                  ->orWhereRaw("LOWER(TRIM(autor)) LIKE ?", [$searchTerm])
                  ->orWhereRaw("LOWER(TRIM(isbn)) LIKE ?", [$searchTerm])
                  ->orWhereRaw("LOWER(TRIM(clasificacion)) LIKE ?", [$searchTerm]);
            });
        }

        $books = $books->orderBy('id', 'DESC')->get();

        $totalBooks = Book::count();
        $totalBibliotecas = 1;
        
        $materialStats = Book::select('tipo_material', DB::raw('count(*) as total'))
            ->whereNotNull('tipo_material')
            ->where('tipo_material', '!=', '')
            ->groupBy('tipo_material')
            ->get();

        return view('biblioteca.index', [
            'books' => $books,
            'query' => $query,
            'totalBooks' => $totalBooks,
            'totalBibliotecas' => $totalBibliotecas,
            'viewMode' => 'simple',
            'materialStats' => $materialStats,
            'activeMaterial' => $activeMaterial
        ]);
    }
}