<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Consulta Express</h5>
    </div>
    <div class="card-body">
        <div class="container" id="consulta-express-container">
            <div class="row">
                <!-- Columna de imagen -->
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="{{ asset('imagenes/express.png') }}" alt="Consulta Express" class="img-fluid rounded shadow-sm" style="max-height: 220px;">
                </div>
                
                <!-- Columna de selectores -->
                <div class="col-md-3">
                    @php
                        // Importar modelos
                        use App\Models\SIGEM\ce_tema;
                        use App\Models\SIGEM\ce_subtema;
                        use App\Models\SIGEM\ce_contenido;
                        
                        // Obtener la URL base actual para mantener otros parámetros
                        $currentUrl = url()->current();
                        $baseUrl = strtok($currentUrl, '?'); // Elimina parámetros existentes
                        
                        // Preservar el parámetro 'section' si existe
                        $sectionParam = request()->has('section') ? 'section=' . request('section') : '';
                        $baseUrlWithSection = $baseUrl . ($sectionParam ? '?' . $sectionParam : '');
                        
                        // Obtener datos iniciales
                        $temas = ce_tema::orderBy('ce_tema_id')->get();
                        
                        // Manejo de selección
                        $tema_id = request('ce_tema_id');
                        $subtema_id = request('ce_subtema_id');
                        
                        // Si hay subtema_id, cargar contenido
                        $contenido = null;
                        $fecha_actualizacion = null;
                        
                        if ($subtema_id) {
                            $contenido = ce_contenido::where('ce_subtema_id', $subtema_id)
                                ->orderBy('created_at', 'desc')
                                ->first();
                            
                            if ($contenido) {
                                $fecha_actualizacion = $contenido->updated_at->format('d/m/Y H:i:s');
                            }
                        }
                        
                        // Cargar subtemas si hay tema seleccionado
                        $subtemas = ($tema_id) 
                            ? ce_subtema::where('ce_tema_id', $tema_id)->get() 
                            : collect([]);
                    @endphp
                    
                    <form method="GET" action="{{ url()->current() }}" id="ce_form">
                        <!-- Mantener el parámetro section si estamos en partial -->
                        @if(request()->has('section'))
                            <input type="hidden" name="section" value="{{ request('section') }}">
                        @endif
                        
                        <div class="form-group mb-3">
                            <label for="ce_tema_select" class="form-label">Tema:</label>
                            <select id="ce_tema_select" name="ce_tema_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Seleccione un tema...</option>
                                @foreach($temas as $tema)
                                    <option value="{{ $tema->ce_tema_id }}" {{ $tema_id == $tema->ce_tema_id ? 'selected' : '' }}>
                                        {{ $tema->tema }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="ce_subtema_select" class="form-label">Subtema:</label>
                            <select id="ce_subtema_select" name="ce_subtema_id" class="form-select" {{ count($subtemas) > 0 ? '' : 'disabled' }} onchange="this.form.submit()">
                                <option value="">{{ count($subtemas) > 0 ? 'Seleccione un subtema...' : 'Primero seleccione un tema' }}</option>
                                @foreach($subtemas as $subtema)
                                    <option value="{{ $subtema->ce_subtema_id }}" {{ $subtema_id == $subtema->ce_subtema_id ? 'selected' : '' }}>
                                        {{ $subtema->ce_subtema }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @if($tema_id && count($subtemas) > 0 && !$subtema_id)
                            <button type="submit" class="btn btn-primary w-100">
                                Consultar <i class="bi bi-arrow-right-circle ms-1"></i>
                            </button>
                        @endif
                    </form>
                </div>
                
                <!-- Columna de contenido -->
                <div class="col-md-6">
                    <div id="ce_contenido_container" class="border rounded p-3" style="min-height: 250px; max-height: 500px; overflow-y: auto;">
                        @if($contenido)
                            {!! $contenido->ce_contenido !!}
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-info-circle fs-2"></i>
                                <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($contenido && $fecha_actualizacion)
                        <div id="ce_metadata" class="text-end text-muted small mt-2">
                            Última actualización: <span id="ce_fecha_actualizacion">{{ $fecha_actualizacion }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>