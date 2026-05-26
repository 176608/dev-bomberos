<div class="material-stats-section"
    style="background: white; padding: 20px; border-radius: 10px; box-shadow: var(--shadow);">
    
   @php
    $totalItems = isset($materialStats) ? collect($materialStats)->sum('total') : 0;
    
    $iconos = [
        'Libro' => '📚',
        'Revista' => '📄',
        'Periodico' => '📰',
        'Cd_dvd' => '💿',
        'VideoCassette' => '📼',
        'Boletin' => '📋',
        'Informe' => '📑',
        'Mapa' => '🗺️',
        'Folleto' => '📄',
    ];

    // PALETA IMIP - Solo azules, verdes y beige sobrios
   $colores = [
    'Libro' => '#145066',        // Azul oscuro (en lugar de beige/naranja)
    'Revista' => '#3a7d7c',      // Verde/Teal
    'Periodico' => '#2d5f5e',    // Verde oscuro
    'Cd_dvd' => '#1e7390',       // Azul IMIP
    'VideoCassette' => '#4a9b9a',// Verde medio
    'Boletin' => '#2a6f6e',      // Verde azulado
    'Informe' => '#1a5d73',      // Azul medio
    'Mapa' => '#2d6b6a',         // Verde oscuro (en lugar de beige/naranja)
    'Folleto' => '#3a7d7c',      // Verde/Teal
];
    $totalMateriales = isset($materialStats) ? $materialStats->count() : 0;
@endphp

    <!-- Título -->
    <h3 style="margin: 0 0 12px 0; color: var(--primary); font-size: 1.1rem; border-bottom: 2px solid var(--primary); padding-bottom: 8px;">
        Clasificación
    </h3>

    <!-- Estilos CSS optimizados -->
    <style>
        .mat-card {
            display: flex; flex-direction: column; align-items: center; justify-content: center; 
            text-decoration: none; border-radius: 8px; padding: 12px 5px; min-height: 80px; 
            transition: all 0.2s ease; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        .mat-card:hover { transform: translateY(-2px); box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .mat-icon { font-size: 1.8rem; margin-bottom: 5px; line-height: 1; }
        .mat-name { font-weight: 600; font-size: 0.85rem; line-height: 1.1; }
        .mat-count { font-size: 0.8rem; opacity: 0.85; margin-top: 4px; font-weight: 600; background: rgba(0,0,0,0.08); padding: 2px 8px; border-radius: 12px; }
        
        /* Desktop: 5 columnas en 2 filas */
        .mat-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 12px; }
        
        /* Tablet: 3 columnas */
        @media (max-width: 900px) {
            .mat-row { grid-template-columns: repeat(3, 1fr); gap: 10px; }
        }
        
        /* Móvil: 2 columnas ULTRA COMPACTO sin espacios blancos */
        @media (max-width: 550px) {
            .mat-row { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 6px; 
                margin-bottom: 6px;
                grid-auto-rows: 1fr; /* Filas del mismo alto */
            }
            .mat-card { 
                min-height: 50px !important; 
                padding: 6px 4px !important; 
                border-radius: 6px !important;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                width: 100%;
            }
            .mat-icon { 
                font-size: 1.2rem !important; 
                margin-bottom: 2px !important; 
            }
            .mat-name { 
                font-size: 0.7rem !important; 
                line-height: 1 !important;
            }
            .mat-count { 
                font-size: 0.65rem !important; 
                padding: 1px 5px !important; 
                margin-top: 2px !important; 
            }
            /* Si el último botón queda solo, ocupa todo el ancho */
            .mat-row .mat-card:last-child:nth-child(odd) {
                grid-column: span 2;
            }
        }
    </style>

    <!-- FILA 1: Todos + Primeros 4 materiales -->
    <div class="mat-row">
        
        <!-- Botón "Todos" -->
        <a href="{{ route('search.simple', ['q' => request('q')]) }}"
            class="mat-card"
            style="background: {{ empty($activeMaterial) ? 'var(--primary)' : '#f8f9fa' }}; color: {{ empty($activeMaterial) ? 'white' : '#333' }}; border-left: 4px solid var(--primary);">
            <span class="mat-icon">🗂️</span>
            <span class="mat-name">Todos</span>
            <span class="mat-count">{{ $totalItems }}</span>
        </a>

        @if(isset($materialStats) && count($materialStats) > 0)
            @foreach($materialStats->take(4) as $stat)
                @php
                    $color = $colores[$stat->tipo_material] ?? '#1e7390';
                    $isActive = isset($activeMaterial) && $activeMaterial === $stat->tipo_material;
                @endphp
                <a href="{{ route('search.simple', ['q' => request('q'), 'material' => $stat->tipo_material]) }}"
                    class="mat-card"
                    style="background: {{ $color }}; color: white; border: none;">
                    <span class="mat-icon">{{ $iconos[$stat->tipo_material] ?? '' }}</span>
                    <span class="mat-name">{{ $stat->tipo_material }}</span>
                    <span class="mat-count" style="background: rgba(255,255,255,0.3);">{{ $stat->total }}</span>
                </a>
            @endforeach
        @endif
    </div>

    <!-- FILA 2: Resto de materiales -->
    @if($totalMateriales > 4)
    <div class="mat-row" style="margin-bottom: 0;">
        @foreach($materialStats->skip(4) as $stat)
            @php
                $color = $colores[$stat->tipo_material] ?? '#1e7390';
                $isActive = isset($activeMaterial) && $activeMaterial === $stat->tipo_material;
            @endphp
            <a href="{{ route('search.simple', ['q' => request('q'), 'material' => $stat->tipo_material]) }}"
                class="mat-card"
                style="background: {{ $color }}; color: white; border: none;">
                <span class="mat-icon">{{ $iconos[$stat->tipo_material] ?? '📖' }}</span>
                <span class="mat-name">{{ $stat->tipo_material }}</span>
                <span class="mat-count" style="background: rgba(255,255,255,0.3);">{{ $stat->total }}</span>
            </a>
        @endforeach
    </div>
    @endif
</div>