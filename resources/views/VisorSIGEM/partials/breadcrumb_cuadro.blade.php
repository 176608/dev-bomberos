{{-- contexto de navegación — Estadística › Tema › Subtema › Código --}}
<nav aria-label="breadcrumb" class="mb-2 pb-1 visor-breadcrumb">
    <ol class="breadcrumb small mb-0 align-items-center">
        <li class="breadcrumb-item"><a class="bc-btn" href="{{ route('sigem.v2.estadistica') }}">Estadística</a></li>
        @if($cuadro->subtema && $cuadro->subtema->tema)
            <li class="bc-sep" aria-hidden="true"><i class="bi bi-caret-right-fill"></i></li>
            <li class="breadcrumb-item">
                <a class="bc-btn" href="{{ route('sigem.v2.estadistica.tema', $cuadro->subtema->tema->tema_id) }}">{{ $cuadro->subtema->tema->tema_titulo }}</a>
            </li>
        @endif
        @if($cuadro->subtema)
            <li class="bc-sep" aria-hidden="true"><i class="bi bi-caret-right-fill"></i></li>
            <li class="breadcrumb-item">
                @if($cuadro->subtema->tema)
                    <a class="bc-btn" href="{{ route('sigem.v2.estadistica.tema', ['tema_id' => $cuadro->subtema->tema->tema_id, 'subtema' => $cuadro->subtema->subtema_id]) }}">{{ $cuadro->subtema->subtema_titulo }}</a>
                @else
                    <span class="bc-text">{{ $cuadro->subtema->subtema_titulo }}</span>
                @endif
            </li>
        @endif
        <li class="bc-sep" aria-hidden="true"><i class="bi bi-caret-right-fill"></i></li>
        <li class="breadcrumb-item active" aria-current="page"><code>{{ $cuadro->codigo_cuadro }}</code></li>
    </ol>
</nav>

@once
<style>
.visor-breadcrumb .breadcrumb-item + .breadcrumb-item::before { content: none; }
.visor-breadcrumb .bc-sep { display: flex; align-items: center; padding: 0 .35rem; color: #adb5bd; font-size: .7rem; }
.visor-breadcrumb .bc-text { color: #6c757d; }
.visor-breadcrumb .bc-btn {
    display: inline-block;
    padding: .2rem .65rem;
    border: 1px solid var(--bs-success);
    border-radius: .5rem;
    color: var(--bs-success);
    text-decoration: none;
    background: transparent;
    transition: all .2s ease;
}
.visor-breadcrumb .bc-btn:hover {
    background: var(--bs-success);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 .25rem .5rem rgba(25, 135, 84, .25);
}
.visor-breadcrumb .breadcrumb-item.active code { font-size: .78rem; }
</style>
@endonce
