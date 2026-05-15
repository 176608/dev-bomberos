<!-- Barra de búsqueda pública -->
@unless(auth()->check())
<div style="background:white; padding:20px; border-radius:8px; box-shadow:var(--shadow); margin-bottom:24px;">
    @if(isset($viewMode) && $viewMode === 'advanced')
        <h2><i class="fas fa-search"></i> Búsqueda avanzada</h2>

        <form id="advancedSearchForm" method="GET" action="{{ route('search.advanced') }}">
            <!-- 🔹 BLOQUE: BÚSQUEDA RÁPIDA -->
            <div class="search-section">
                <div class="search-section-title">
                    🔍 Búsqueda rápida
                    <span> (usa solo este campo si quieres algo rápido)</span>
                </div>

                <div style="display: flex; gap: 8px; position: relative; margin-bottom: 16px;">
                    <div style="flex: 1; position: relative; min-width: 0;">
                        <input
                            type="text"
                            name="q"
                            placeholder="Título, autor, ISBN o clasificación..."
                            value="{{ $query ?? '' }}"
                            style="width: 100%; padding: 12px 48px 12px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 1rem; box-sizing: border-box;"
                        >
                        <button
                            type="button"
                            id="clearSearchBtnAdvanced"
                            class="clear-btn"
                            title="Limpiar búsqueda"
                            style="
                                position: absolute;
                                right: 12px;
                                top: 50%;
                                transform: translateY(-50%);
                                background: none;
                                border: none;
                                color: #999;
                                font-size: 1.2rem;
                                cursor: pointer;
                                padding: 0;
                                line-height: 1;
                            "
                        >
                            ×
                        </button>
                    </div>

                    <button type="submit"
                        style="padding: 12px 24px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>

            <!-- SEPARADOR -->
            <div class="search-divider">
                o usa búsqueda avanzada
            </div>

            <!-- 🔹 BLOQUE: BÚSQUEDA AVANZADA -->
            <div class="search-section">
                <div class="search-section-title">
                    ⚙️ Búsqueda avanzada
                    <span> (combina múltiples términos)</span>
                </div>

                <!-- GRID ORIGINAL -->
                <div class="form-grid" style="display:grid; grid-template-columns: 160px 1fr 220px; gap:12px 16px; align-items:center;">                
                    <!-- FILA 1 -->
                    <div class="form-group">
                        <label>Palabra a Buscar:</label>
                    </div>

                    <div class="form-group">
                        <input type="text" name="term1" placeholder="Término 1" value="{{ $term1 ?? '' }}">
                    </div>

                    <div class="form-group">
                        <select name="field1">
                            <option value="">Cualquier Campo</option>
                            <option value="titulo" {{ $field1 == 'titulo' ? 'selected' : '' }}>Título</option>
                            <option value="autor" {{ $field1 == 'autor' ? 'selected' : '' }}>Autor</option>
                            <option value="isbn" {{ $field1 == 'isbn' ? 'selected' : '' }}>ISBN</option>
                            <option value="clasificacion" {{ $field1 == 'clasificacion' ? 'selected' : '' }}>Clasificación</option>
                            <option value="idbiblioteca" {{ $field1 == 'idbiblioteca' ? 'selected' : '' }}>ID Biblioteca</option>
                        </select>
                    </div>

                    <!-- FILA 2 -->
                    <div class="form-group">
                        <select name="operator1">
                            <option value="AND" {{ ($operator1 ?? '') == 'AND' ? 'selected' : '' }}>AND</option>
                            <option value="OR" {{ ($operator1 ?? '') == 'OR' ? 'selected' : '' }}>OR</option>
                            <option value="NOT" {{ ($operator1 ?? '') == 'NOT' ? 'selected' : '' }}>NOT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" name="term2" placeholder="Término 2" value="{{ $term2 ?? '' }}">
                    </div>

                    <div class="form-group">
                        <select name="field2">
                            <option value="">Cualquier Campo</option>
                            <option value="titulo" {{ $field2 == 'titulo' ? 'selected' : '' }}>Título</option>
                            <option value="autor" {{ $field2 == 'autor' ? 'selected' : '' }}>Autor</option>
                            <option value="isbn" {{ $field2 == 'isbn' ? 'selected' : '' }}>ISBN</option>
                            <option value="clasificacion" {{ $field2 == 'clasificacion' ? 'selected' : '' }}>Clasificación</option>
                            <option value="idbiblioteca" {{ $field2 == 'idbiblioteca' ? 'selected' : '' }}>ID Biblioteca</option>
                        </select>
                    </div>

                    <!-- FILA 3 -->
                    <div class="form-group">
                        <select name="operator2">
                            <option value="AND" {{ ($operator2 ?? '') == 'AND' ? 'selected' : '' }}>AND</option>
                            <option value="OR" {{ ($operator2 ?? '') == 'OR' ? 'selected' : '' }}>OR</option>
                            <option value="NOT" {{ ($operator2 ?? '') == 'NOT' ? 'selected' : '' }}>NOT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" name="term3" placeholder="Término 3" value="{{ $term3 ?? '' }}">
                    </div>

                    <div class="form-group">
                        <select name="field3">
                            <option value="">Cualquier Campo</option>
                            <option value="titulo" {{ $field3 == 'titulo' ? 'selected' : '' }}>Título</option>
                            <option value="autor" {{ $field3 == 'autor' ? 'selected' : '' }}>Autor</option>
                            <option value="isbn" {{ $field3 == 'isbn' ? 'selected' : '' }}>ISBN</option>
                            <option value="clasificacion" {{ $field3 == 'clasificacion' ? 'selected' : '' }}>Clasificación</option>
                            <option value="idbiblioteca" {{ $field3 == 'idbiblioteca' ? 'selected' : '' }}>ID Biblioteca</option>
                        </select>
                    </div>

                    <!-- FILTROS -->
                    <div class="form-group">
                        <label>Bibliotecas</label>
                    </div>

                    <div class="form-group" style="grid-column: 2 / span 2;">
                        <select name="library">
                            <option value="">Todas</option>
                            <option value="IMIP" {{ $library == 'IMIP' ? 'selected' : '' }}>IMIP</option>
                            <option value="BIBLIO1" {{ $library == 'BIBLIO1' ? 'selected' : '' }}>Biblioteca Central</option>
                            <option value="BIBLIO2" {{ $library == 'BIBLIO2' ? 'selected' : '' }}>Biblioteca Norte</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Materiales</label>
                    </div>

                    <div class="form-group" style="grid-column: 2 / span 2;">
                        <select name="material">
                            <option value="">Todos los materiales</option>
                            <option value="libro" {{ $material == 'libro' ? 'selected' : '' }}>Libro</option>
                            <option value="revista" {{ $material == 'revista' ? 'selected' : '' }}>Revista</option>
                            <option value="Periodico" {{ ($material ?? '') == 'Periodico' ? 'selected' : '' }}>Periodico</option>
                            <option value="Cd_dvd" {{ ($material ?? '') == 'Cd_dvd' ? 'selected' : '' }}>Cd_dvd</option>
                           <option value="VideoCassette" {{ ($material ?? '') == 'VideoCassette' ? 'selected' : '' }}>VideoCassette</option>
                           <option value="Boletin" {{ ($material ?? '') == 'Boletin' ? 'selected' : '' }}>Boletin</option>
                           <option value="Informe" {{ ($material ?? '') == 'Informe' ? 'selected' : '' }}>Informe</option>
                           <option value="Mapa" {{ ($material ?? '') == 'Mapa' ? 'selected' : '' }}>Mapa</option>

                        </select>
                    </div>

                    <!-- BOTÓN -->
                    <div style="grid-column: 1 / span 3; display: flex; justify-content: center;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <h2>🔍 Búsqueda de Libros</h2>
        <form id="searchForm" method="GET" action="{{ route('search.simple') }}" class="mb-4">
            <div style="display: flex; gap: 8px; position: relative;">
                <div style="flex: 1; position: relative;">
                    <input
                        type="text"
                        name="q"
                        id="searchInput"
                        value="{{ $query ?? '' }}"
                        placeholder="Título, autor, ISBN o clasificación..."
                        style="width: 100%; padding: 12px 48px 12px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 1rem; box-sizing: border-box;"
                    >
                    <button
                        type="button"
                        id="clearSearchBtn"
                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; font-size: 1.2rem; cursor: pointer; z-index: 2;"
                        title="Limpiar búsqueda"
                    >
                        ×
                    </button>
                </div>
                <button
                    type="submit"
                    style="padding: 12px 24px; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; flex-shrink: 0;"
                >
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>
    @endif
</div>
@endunless
