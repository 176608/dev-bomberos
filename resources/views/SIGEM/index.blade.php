@extends('layouts.app')

@section('content')
    @include('partials.header-logos')

    <div class="main-card">
        <div style="text-align: right; margin-bottom: 15px;">
            <a href="{{ asset('logout.php') }}" style="background-color: #2a6e48; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px;">
                Cerrar sesión
            </a>
        </div>

        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid justify-content-center">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#">INICIO</a></li>
                    <li class="nav-item"><a class="nav-link" href="estadistica.php">ESTADÍSTICA</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">CARTOGRAFÍA</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">PRODUCTOS</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">CATÁLOGO</a></li>
                </ul>
            </div>
        </nav>

        <div class="row module-icons">
            <div class="col-md-6">
                <img src="{{ asset('imagenes/iconoesta2.png') }}" alt="Estadística">
                <div class="description">
                    Consultas de información estadística relevante y precisa en cuadros estadísticos,
                    obtenidos de diversas fuentes Municipales, Estatales y Federales.
                </div>
            </div>
            <div class="col-md-6">
                <img src="{{ asset('imagenes/iconoesta3.png') }}" alt="Cartografía">
                <div class="description">
                    En este apartado podrás encontrar mapas temáticos interactivos del Municipio de Juárez.
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <select id="tema-selector" class="form-select">
                        <option value="">Selecciona un tema</option>
                        <option value="poblacion">1. Población</option>
                        <option value="empleo">2. Empleo</option>
                        <option value="industria">3. Industria Maquiladora</option>
                        <option value="vivienda">4. Vivienda</option>
                        <option value="educacion">5. Educación</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="subtema-selector" class="form-select" style="display: none;"></select>
                </div>
            </div>
            <div id="contenido-dinamico" class="mt-4"></div>
        </div>
    </div>

    @include('partials.footer-logos')

    <script>
        const temaSelector = document.getElementById('tema-selector');
        const subtemaSelector = document.getElementById('subtema-selector');
        const contenidoDinamico = document.getElementById('contenido-dinamico');

        const mapaSubtemaId = {
            poblacion: { municipio: 3, localidad: 4 },
            empleo: { trabajadores: 9 },
            industria: { parques: 8, trabajadores: 10 },
            vivienda: { particulares: 6, jefatura: 7 },
            educacion: { grado: 7 }
        };

        function actualizarContenido() {
            const tema = temaSelector.value;
            const subtema = subtemaSelector.value;

            let subtemaId = null;

            if (tema === 'poblacion') {
                subtemaSelector.style.display = 'block';
                subtemaSelector.innerHTML = `
                    <option value="municipio">Municipio</option>
                    <option value="localidad">Localidad</option>
                `;
                subtemaId = mapaSubtemaId[tema][subtema];
            } else if (tema === 'empleo') {
                subtemaSelector.style.display = 'block';
                subtemaSelector.innerHTML = `
                    <option value="trabajadores">Número de trabajadores</option>
                `;
                subtemaId = mapaSubtemaId[tema]['trabajadores'];
            } else if (tema === 'industria') {
                subtemaSelector.style.display = 'block';
                subtemaSelector.innerHTML = `
                    <option value="parques">Parques y Zonas Industriales</option>
                    <option value="trabajadores">Número de trabajadores</option>
                `;
                subtemaId = mapaSubtemaId[tema][subtema] || mapaSubtemaId[tema]['parques'];
            } else if (tema === 'vivienda') {
                subtemaSelector.style.display = 'block';
                subtemaSelector.innerHTML = `
                    <option value="particulares">Total de Viviendas Particulares Habitadas</option>
                    <option value="jefatura">Hogares con jefatura femenina</option>
                `;
                subtemaId = mapaSubtemaId[tema][subtema] || mapaSubtemaId[tema]['particulares'];
            } else if (tema === 'educacion') {
                subtemaSelector.style.display = 'block';
                subtemaSelector.innerHTML = `
                    <option value="grado">Grado promedio de escolaridad</option>
                `;
                subtemaId = mapaSubtemaId[tema]['grado'];
            } else {
                subtemaSelector.style.display = 'none';
            }

            contenidoDinamico.innerHTML = '';

            if (subtemaId) {
                fetch(`/contenido-tema?subtema_id=${subtemaId}`)
                    .then(response => response.text())
                    .then(data => {
                        contenidoDinamico.innerHTML = data;
                    })
                    .catch(error => {
                        contenidoDinamico.innerHTML = `<div class="alert alert-danger">Error al cargar contenido.</div>`;
                        console.error('Error al obtener datos:', error);
                    });
            }
        }

        temaSelector.addEventListener('change', actualizarContenido);
        subtemaSelector.addEventListener('change', actualizarContenido);
    </script>
@endsection
