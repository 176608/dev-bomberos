<?php

namespace App\Http\Controllers\Biblioteca;
use App\Http\Controllers\Bomberos\Controller;
use App\Models\Biblioteca\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    // Campos permitidos para búsqueda (evita inyección de SQL)
    private $allowedFields = ['titulo', 'autor', 'isbn', 'clasificacion', 'idbiblioteca', ''];
    
    // Materiales permitidos (evita filtros maliciosos)
    private $allowedMaterials = ['Libro', 'Revista', 'Periodico', 'Cd_dvd', 'VideoCassette', 'Boletin', 'Informe', 'Mapa','Folleto', ''];

    public function index()
    {
        $books = Book::paginate(10);
        return view('biblioteca.index', compact('books'));
    }

    /**
     * BÚSQUEDA SIMPLE - CON VALIDACIONES DE SEGURIDAD
     */
    public function search(Request $request)
    {
        // 🔐 VALIDACIÓN DE INPUTS
        $query = substr(trim($request->input('q', '')), 0, 200); // Máximo 200 caracteres
        $activeMaterial = $request->input('material');
        
        // Validar que el material sea permitido
        if ($activeMaterial && !in_array($activeMaterial, $this->allowedMaterials)) {
            $activeMaterial = '';
        }

        $books = Book::query();

        if ($activeMaterial) {
            $books->where('tipo_material', $activeMaterial);
        }

        if ($query) {
            // Separar por espacios y validar cada término
            $terms = preg_split('/\s+/', $query);
            foreach ($terms as $term) {
                $term = trim($term);
                // Validar que el término no esté vacío y sea seguro (solo letras, números, espacios y signos básicos)
                if ($term && preg_match('/^[\p{L}\p{N}\s\.\,\-\_\:\;\'\"]+$/u', $term)) {
                    $books->where(function ($q) use ($term) {
                        $searchTerm = '%' . mb_strtolower($term, 'UTF-8') . '%';
                        $q->whereRaw("LOWER(TRIM(titulo)) LIKE ?", [$searchTerm])
                            ->orWhereRaw("LOWER(TRIM(autor)) LIKE ?", [$searchTerm])
                            ->orWhereRaw("LOWER(TRIM(isbn)) LIKE ?", [$searchTerm])
                            ->orWhereRaw("LOWER(TRIM(clasificacion)) LIKE ?", [$searchTerm]);
                    });
                }
            }
        }

        $books = $books->orderBy('id', 'desc')->get();

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

    /**
     * BÚSQUEDA AVANZADA - CON VALIDACIONES DE SEGURIDAD
     */
    public function advanced(Request $request)
    {
        // 🔐 VALIDACIÓN Y SANITIZACIÓN DE INPUTS
        $term1 = substr(trim($request->input('term1', '')), 0, 200);
        $term2 = substr(trim($request->input('term2', '')), 0, 200);
        $term3 = substr(trim($request->input('term3', '')), 0, 200);
        
        // Validar campos permitidos
        $field1 = in_array($request->input('field1'), $this->allowedFields) ? $request->input('field1') : '';
        $field2 = in_array($request->input('field2'), $this->allowedFields) ? $request->input('field2') : '';
        $field3 = in_array($request->input('field3'), $this->allowedFields) ? $request->input('field3') : '';
        
        // Validar operadores
        $op1 = strtoupper($request->input('operator1', 'AND'));
        $op2 = strtoupper($request->input('operator2', 'AND'));
        $op1 = in_array($op1, ['AND', 'OR', 'NOT']) ? $op1 : 'AND';
        $op2 = in_array($op2, ['AND', 'OR', 'NOT']) ? $op2 : 'AND';
        
        // Validar filtros adicionales
        $library = $request->input('library');
        if ($library && !in_array($library, ['IMIP', 'BIBLIO1', 'BIBLIO2', ''])) {
            $library = '';
        }
        
        $material = $request->input('material');
        if ($material && !in_array($material, $this->allowedMaterials)) {
            $material = '';
        }

        $books = Book::query();
        
        // Función auxiliar para aplicar búsqueda (con sanitización)
        $applySearch = function($query, $term, $field) {
            // Sanitizar término: solo permitir caracteres seguros
            if (!preg_match('/^[\p{L}\p{N}\s\.\,\-\_\:\;\'\"]+$/u', $term)) {
                return; // Terminar si el término contiene caracteres sospechosos
            }
            
            $searchTerm = '%' . mb_strtolower($term, 'UTF-8') . '%';
            if ($field && $field !== '') {
                $query->whereRaw("LOWER(TRIM({$field})) LIKE ?", [$searchTerm]);
            } else {
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw("LOWER(TRIM(titulo)) LIKE ?", [$searchTerm])
                      ->orWhereRaw("LOWER(TRIM(autor)) LIKE ?", [$searchTerm])
                      ->orWhereRaw("LOWER(TRIM(isbn)) LIKE ?", [$searchTerm])
                      ->orWhereRaw("LOWER(TRIM(clasificacion)) LIKE ?", [$searchTerm]);
                });
            }
        };
        
        // Término 1
        if ($term1) {
            $applySearch($books, $term1, $field1);
        }
        
        // Término 2
        if ($term2) {
            if ($op1 === 'OR') {
                $books->orWhere(function($q) use ($term2, $field2, $applySearch) {
                    $applySearch($q, $term2, $field2);
                });
            } else {
                $applySearch($books, $term2, $field2);
            }
        }
        
        // Término 3
        if ($term3) {
            if ($op2 === 'OR') {
                $books->orWhere(function($q) use ($term3, $field3, $applySearch) {
                    $applySearch($q, $term3, $field3);
                });
            } else {
                $applySearch($books, $term3, $field3);
            }
        }
        
        // Filtros adicionales (ya validados arriba)
        if ($library && $library !== '') {
            $books->where('idbiblioteca', $library);
        }
        if ($material && $material !== '') {
            $books->where('tipo_material', $material);
        }

        $books = $books->orderBy('id', 'DESC')->get();

        // Estadísticas
        $totalBooks = Book::count();
        $totalBibliotecas = 1;
        $materialStats = Book::select('tipo_material', DB::raw('count(*) as total'))
            ->whereNotNull('tipo_material')
            ->where('tipo_material', '!=', '')
            ->groupBy('tipo_material')
            ->get();

        // 🔐 LOGGING DE BÚSQUEDAS (para monitoreo de seguridad)
        Log::info('Búsqueda avanzada realizada', [
            'terms' => [$term1, $term2, $term3],
            'fields' => [$field1, $field2, $field3],
            'results_count' => $books->count(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return view('biblioteca.advanced', [
            'books' => $books,
            'viewMode' => 'advanced',
            'query' => $term1,
            'totalBooks' => $totalBooks,
            'totalBibliotecas' => $totalBibliotecas,
            'materialStats' => $materialStats,
            'activeMaterial' => $material,
            'term1' => $term1, 'term2' => $term2, 'term3' => $term3,
            'field1' => $field1, 'field2' => $field2, 'field3' => $field3,
            'operator1' => $op1, 'operator2' => $op2,
            'library' => $library,
            'exact' => $request->boolean('exact'),
            'hasSearch' => $term1 || $term2 || $term3 || $library || $material,
        ]);
    }
}