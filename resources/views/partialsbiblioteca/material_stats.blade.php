<div class="material-stats-section"
    style="display: flex; flex-wrap: wrap; gap: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: var(--shadow); margin-bottom: 24px;">
   @php
    $totalItems = isset($materialStats) ? collect($materialStats)->sum('total') : 0;
    
    $baseRoute = 'search.simple';
        $iconos = [
        'Libro' => '📚',
        'Revista' => '📰',
        'Periodico' => '📄',
        'Cd_dvd' => '💿',
        'Videocassette' => '📼',
        'Boletin' => '📋',
        'Informe' => '📑',
        'Mapa' => '🗺️',
        'Folleto' => '📄',
    ];
@endphp
    <!-- Contenedor Izquierdo: Botones -->
    <div style="flex: 1; min-width: 300px;">
        <h3
            style="margin-top: 0; color: var(--primary); font-size: 1.2rem; border-bottom: 2px solid var(--primary); padding-bottom: 10px; margin-bottom: 15px;">
            Clasificación</h3>
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;">Limita los resultados por tipo de material.
            Aplica sobre todos los registros o sobre la búsqueda actual.</p>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
            <a href="{{ route('search.simple', ['q' => request('q')]) }}"
                class="btn {{ empty($activeMaterial) ? 'btn-primary' : 'btn-secondary' }}"
                style="display: flex; justify-content: space-between; align-items: center; text-decoration: none; border-radius: 6px; padding: 10px 15px; font-size: 0.95rem; transition: transform 0.2s, background-color 0.3s; text-align: left; width: 100%; box-sizing: border-box;"
                onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <span>🗂️ Todos</span>                <span style="background: {{ empty($activeMaterial) ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.1)' }}; color: inherit; padding: 3px 8px; border-radius: 6px; font-size: 0.85rem; font-weight: bold;">
                    {{ $totalItems }}
                </span>
            </a>
            @if(isset($materialStats) && count($materialStats) > 0)
                @foreach($materialStats as $stat)
                    <a href="{{ route('search.simple', ['q' => request('q'), 'material' => $stat->tipo_material]) }}"
                        class="btn {{ (isset($activeMaterial) && $activeMaterial === $stat->tipo_material) ? 'btn-primary' : 'btn-secondary' }}"
                        style="display: flex; justify-content: space-between; align-items: center; text-decoration: none; border-radius: 6px; padding: 10px 15px; font-size: 0.95rem; transition: transform 0.2s, background-color 0.3s; text-align: left; width: 100%; box-sizing: border-box;"
                        onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                           <span>{{ $iconos[$stat->tipo_material] ?? '📖' }} {{ $stat->tipo_material }}</span>                        <span
                            style="background: {{ (isset($activeMaterial) && $activeMaterial === $stat->tipo_material) ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.1)' }}; color: inherit; padding: 3px 8px; border-radius: 6px; font-size: 0.85rem; font-weight: bold;">
                            {{ $stat->total }}
                        </span>
                    </a>
                @endforeach
            @else
                <div style="grid-column: span 2;">
                    <p style="color: #888; font-style: italic;">No hay tipos de materiales registrados en la base de datos.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Contenedor Derecho: Porcentajes -->
    <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; position: relative; border: 1px solid #eaeaea; border-radius: 6px; padding: 15px; background: #fafafa;">
        <h3 style="margin-top: 0; color: #555; font-size: 1.1rem; border-bottom: 1px solid #eaeaea; padding-bottom: 12px; margin-bottom: 15px; font-weight: 600;">
            Porcentajes
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 16px; width: 100%;">
            @if(isset($materialStats) && count($materialStats) > 0)
                @php
                    $colors = [
                        '#1e7390', // primary blue
                        '#3a7d7c', // primary green/teal
                    ];
                @endphp
                @foreach($materialStats as $index => $stat)
                    @php
                        $percentage = $totalItems > 0 ? number_format(($stat->total / $totalItems) * 100, 2) : "0.00";
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div>
                        <div style="color: {{ $color }}; font-weight: bold; font-size: 0.95rem; margin-bottom: 6px; text-transform: uppercase;">
                         {{ $percentage }}% {{ $iconos[$stat->tipo_material] ?? '📖' }} {{ $stat->tipo_material }}                        </div>
                        <div style="width: 100%; background-color: #e9ecef; border-radius: 10px; height: 14px; overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                            <div style="width: {{ $percentage }}%; background-color: {{ $color }}; height: 100%; border-radius: 10px; transition: width 0.5s ease-in-out;"></div>
                        </div>
                    </div>
                @endforeach
            @else
                <p style="color: #999; text-align: center; font-size: 0.9rem; margin-top: 20px;">No hay datos disponibles</p>
            @endif
        </div>
    </div>
</div>
