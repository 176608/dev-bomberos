<div style="background:white; padding:20px; border-radius:8px; box-shadow:var(--shadow); margin-bottom:24px;">
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
                @if(isset($activeMaterial) && $activeMaterial)
                    <input type="hidden" name="material" value="{{ $activeMaterial }}">
                @endif
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
</div>
