@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estadistica.css') }}">
@endpush

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if(isset($modo_vista) && $modo_vista === 'desde_catalogo')

        @elseif(isset($modo_vista) && $modo_vista === 'navegacion_tema_con_subtemas')
            <!-- NUEVA VISTA: Incluir el partial específico para tema con subtemas -->
            @include('partials.estadistica_tema_con_subtemas')
            
            
        @else
            <!-- NUEVO ENFOQUE: Incluir la vista de navegación de temas -->
             Entro aca?
            @include('partials.estadistica_navegacion')
        @endif
    </div>
</div>

<script>
</script>