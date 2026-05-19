<div style="background:white; padding:20px; border-radius:8px; box-shadow:var(--shadow); margin-bottom:24px;">
    <h2 style="margin-bottom: 25px;"><i class="fas fa-search-plus"></i> Búsqueda Avanzada</h2>

    <form id="advancedSearchForm" method="GET" action="{{ route('search.advanced') }}">
        <div class="search-section">
            <div class="search-section-title">
                <i class="fas fa-sliders-h"></i> Parámetros de búsqueda
                <span> (combina múltiples términos para refinar los resultados)</span>
            </div>

            <!-- GRID -->
            <div class="form-grid adv-grid" style="display:grid; grid-template-columns: 160px 1fr 220px; gap:12px 16px; align-items:center;">
                <!-- FILA 1 -->
                <div class="form-group">
                    <label>Palabra a Buscar:</label>
                </div>

                <div class="form-group">
                    <input type="text" name="term1" placeholder="Término 1" value="{{ $term1 ?? '' }}" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                </div>

                <div class="form-group">
                    <select name="field1" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                        <option value="">Cualquier Campo</option>
                        <option value="titulo" {{ ($field1 ?? '') == 'titulo' ? 'selected' : '' }}>Título</option>
                        <option value="autor" {{ ($field1 ?? '') == 'autor' ? 'selected' : '' }}>Autor</option>
                        <option value="isbn" {{ ($field1 ?? '') == 'isbn' ? 'selected' : '' }}>ISBN</option>
                        <option value="clasificacion" {{ ($field1 ?? '') == 'clasificacion' ? 'selected' : '' }}>Clasificación</option>
                        <option value="idbiblioteca" {{ ($field1 ?? '') == 'idbiblioteca' ? 'selected' : '' }}>ID Biblioteca</option>
                    </select>
                </div>

                <!-- FILA 2 -->
                <div class="form-group">
                    <select name="operator1" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                        <option value="AND" {{ ($operator1 ?? '') == 'AND' ? 'selected' : '' }}>AND</option>
                        <option value="OR" {{ ($operator1 ?? '') == 'OR' ? 'selected' : '' }}>OR</option>
                        <option value="NOT" {{ ($operator1 ?? '') == 'NOT' ? 'selected' : '' }}>NOT</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="text" name="term2" placeholder="Término 2" value="{{ $term2 ?? '' }}" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                </div>

                <div class="form-group">
                    <select name="field2" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                        <option value="">Cualquier Campo</option>
                        <option value="titulo" {{ ($field2 ?? '') == 'titulo' ? 'selected' : '' }}>Título</option>
                        <option value="autor" {{ ($field2 ?? '') == 'autor' ? 'selected' : '' }}>Autor</option>
                        <option value="isbn" {{ ($field2 ?? '') == 'isbn' ? 'selected' : '' }}>ISBN</option>
                        <option value="clasificacion" {{ ($field2 ?? '') == 'clasificacion' ? 'selected' : '' }}>Clasificación</option>
                        <option value="idbiblioteca" {{ ($field2 ?? '') == 'idbiblioteca' ? 'selected' : '' }}>ID Biblioteca</option>
                    </select>
                </div>

                <!-- FILA 3 -->
                <div class="form-group">
                    <select name="operator2" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                        <option value="AND" {{ ($operator2 ?? '') == 'AND' ? 'selected' : '' }}>AND</option>
                        <option value="OR" {{ ($operator2 ?? '') == 'OR' ? 'selected' : '' }}>OR</option>
                        <option value="NOT" {{ ($operator2 ?? '') == 'NOT' ? 'selected' : '' }}>NOT</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="text" name="term3" placeholder="Término 3" value="{{ $term3 ?? '' }}" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                </div>

                <div class="form-group">
                    <select name="field3" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                        <option value="">Cualquier Campo</option>
                        <option value="titulo" {{ ($field3 ?? '') == 'titulo' ? 'selected' : '' }}>Título</option>
                        <option value="autor" {{ ($field3 ?? '') == 'autor' ? 'selected' : '' }}>Autor</option>
                        <option value="isbn" {{ ($field3 ?? '') == 'isbn' ? 'selected' : '' }}>ISBN</option>
                        <option value="clasificacion" {{ ($field3 ?? '') == 'clasificacion' ? 'selected' : '' }}>Clasificación</option>
                        <option value="idbiblioteca" {{ ($field3 ?? '') == 'idbiblioteca' ? 'selected' : '' }}>ID Biblioteca</option>
                    </select>
                </div>

                <!-- FILTROS ADICIONALES -->
                <div class="form-group adv-label-col">
                    <label>Biblioteca:</label>
                </div>

                <div class="form-group adv-span-col" style="grid-column: 2 / span 2;">
                   <select name="library" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
    <option value="">Todas las bibliotecas</option> 
    <option value="IMIP" {{ ($library ?? '') == 'IMIP' ? 'selected' : '' }}>IMIP</option>
</select>
                </div>

                <div class="form-group adv-label-col">
                    <label>Materiales:</label>
                </div>

                <div class="form-group adv-span-col" style="grid-column: 2 / span 2;">
                    <select name="material" style="width:100%; padding:8px; border:1px solid var(--gray-300); border-radius:4px;">
                        <option value="">Todos los materiales</option>
                        <option value="Libro" {{ ($material ?? '') == 'Libro' ? 'selected' : '' }}>Libro</option>
                        <option value="Revista" {{ ($material ?? '') == 'Revista' ? 'selected' : '' }}>Revista</option>
                        <option value="Periodico" {{ ($material ?? '') == 'Periodico' ? 'selected' : '' }}>Periodico</option>
                        <option value="Cd_dvd" {{ ($material ?? '') == 'Cd_dvd' ? 'selected' : '' }}>Cd_dvd</option>
                        <option value="VideoCassette" {{ ($material ?? '') == 'VideoCassette' ? 'selected' : '' }}>VideoCassette</option>
                        <option value="Boletin" {{ ($material ?? '') == 'Boletin' ? 'selected' : '' }}>Boletin</option>
                        <option value="Informe" {{ ($material ?? '') == 'Informe' ? 'selected' : '' }}>Informe</option>
                        <option value="Mapa" {{ ($material ?? '') == 'Mapa' ? 'selected' : '' }}>Mapa</option>


                    </select>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="adv-btn-row" style="grid-column: 1 / span 3; display: flex; justify-content: center; gap: 15px; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--gray-100);">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 40px; font-weight: bold; cursor: pointer;">
                        <i class="fas fa-search"></i> Buscar ahora
                    </button>
                    <button type="button" class="btn btn-warning" style="padding: 12px 40px; font-weight: bold; cursor: pointer;" onclick="window.location.href='{{ route('search.advanced') }}'">
                        <i class="fas fa-undo"></i> Limpiar filtros
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
@media (max-width: 767.98px) {
    .adv-grid {
        grid-template-columns: 1fr !important;
    }
    .adv-grid .adv-span-col {
        grid-column: 1 !important;
    }
    .adv-grid .adv-btn-row {
        grid-column: 1 !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
    }
    .adv-grid .adv-btn-row button {
        width: 100% !important;
        padding: 12px !important;
    }
}
</style>
