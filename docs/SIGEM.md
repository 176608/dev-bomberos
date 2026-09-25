# SIGEM — Visor Público de Estadísticas

> Fuente de verdad: `archivoslocales/Especificaciones/02_Documentacion_SIGEM_Visor_Publico.md`. Resumen *spec-anchored*.

## Qué es

Cara **pública** de consulta de cuadros estadísticos (dataset, gráficas, mapas y exportación a Excel). Consume el contenido producido por SGIEM (solo `publicado=true`).

- Prefijo de rutas: `sigem-v2` · ruta raíz `/sigem-v2` · archivo `routes/VisorSIGEM/laravel_v2.php`.
- Middleware: grupo web + `throttle:60,1` en todo `sigem-v2` + `throttle:30,1` en `/track` + `log.404` global (grupo web, detección de escaneo).
- **V1 legacy (`/sigem`): NO tocar**, está en retiro; solo referencia en `routes/SIGEM/laravel.php` y partials v1.

## Estructura

- Controladores (5): `SIGEMV2Controller` (páginas + consulta express), `VisorCuadroController` (dataset/grafica/mapa/documento + `verificarAccesoCuadro` + credenciales), `DatasetViewController` (inspección), `DocumentoController` (exportar Excel con filtrado de secciones `?v/h/s=`), `Controller` (base: `registrarEvento`, track, detección de bots).
- Servicios (5): `CatalogoService`, `EstadisticaService`, `DatasetViewService`, `ConsultaExpressService`, `CuadroExcelExport` (export `app/Exports/`).
- Modelos: `Cuadro`, `CuadroCategoria`, `CuadroDato`, `CuadroSeccion`, `PubVisita`, `PubVisitante`, `ce_tema`, `ce_subtema`, `ce_contenido`.
- Vistas: `resources/views/VisorSIGEM/` (`layouts/visor.blade.php`, `pages/*`, `cuadro/*`).

## Flujo maestro: `GET /cuadro/{id}/dataset`

```
verificarAccesoCuadro:
  - Cuadro no existe → abort(404)
  - !publicado sin credenciales (Estadístico/Desarrollador/Administrador) → abort(404) (respuesta 404 también en JSON)
  - tipo_mapa_pdf=1 → redirect /mapa (muestra el PDF, no hay dataset)

Cache::remember("visor_cuadro_estado_{id}", 300)  ← TTL 300 s
  - miss → DatasetViewService::datosCuadro (categorías verticales/horizontales, secciones, celdas)

registrarEvento('dataset') si no es bot:
  - PubVisitante::firstOrCreate([ip_hash]) → total_visitas+1
  - PubVisita::create({_vuid, ip_hash, cuadro_id, evento:'dataset', credenciales})
```

La grilla se construye en el **servidor** (Blade + `@json($estadoInicial)`); el JS renderiza tablas y pide secciones por AJAX (`/dataset/seccion/{s}/data`, cacheada por sección TTL 300). Mientras carga: spinner por sección + contador «N de M secciones» + botón Reintentar en error (2026-09-22). Breadcrumb de cuadro (partial `VisorSIGEM/partials/breadcrumb_cuadro.blade.php` en dataset/gráfica/mapa): botones hover con separador caret y **código del cuadro** al final; el botón de subtema regresa al tema con `?subtema=ID` (SSR, sidebar activa); los cuadros de Estadística navegan en la **misma pestaña**. Toolbar del dataset: checkboxes ocultos por defecto (preferencia persistente `localStorage sigem.dataset.showCb`), botones «Mostrar/Ocultar», «Activar categorías», «Ver categorías desactivadas» y «Reactivar todas las secciones» con visibilidad condicional. La credencial de previsualización del visor es `puedePrevisualizar()` (Dev, Estadístico o Administrador; renombrada desde `esDesarrollador()` el 2026-09-22).

Convenciones visuales de la grilla (2026-09-25, `cuadro/dataset.blade.php`): las categorías de **columna** centran su texto horizontal y verticalmente, igual que SGIEM modo datos (padres con `colspan` incluidas, `align-middle`); las etiquetas de **fila** (sencillas, padres e hijas) alinean a la izquierda, con sangría adicional en las hijas (`tbody th.sub-cat`); el **pivote** no se altera. La primera fila visible de cada categoría padre de fila (salvo la primera del cuerpo) lleva un separador `border-top:2px` con el color pleno del tema (fallback `#adb5bd`) para delimitar el bloque de cada padre. Solo estética: no cambia render ni lógica. Encabezado de cuadro (2026-09-25, GEI): en `dataset`, `grafica`, `mapa` y la vista de API (`dataset_view/show`) los metadatos van en una fila de 8 columnas (icono + **título** + subtítulo, breadcrumb y badge «no publicado») y las acciones en 4 columnas; el botón de enlace queda solo con iconos (sin el texto «Enlace»).

## Reglas duras

- El visor **solo lee** BD y **solo escribe métricas** (`pub_visita`/`pub_visitante`). La invalidación de caché la hace **SGIEM** (`invalidarCacheVisor`).
- Identidad anónima: cookie `_vuid` (`SetVisitorUuid`, UUID httpOnly, 10 años, `secure` dinámico). IP se almacena **hasheada** (`HashIp`, HMAC-SHA256 con `IP_HASH_SALT`), nunca en claro para visitantes.
- Detección de bots por User-Agent (allowlist) → cuenta pero marca `es_bot=1`.
- Exportación Excel: la selección de secciones viaja en la cadena de consulta (`?s=`, parse de ids con excepciones); **no valida firma HMAC** (hallazgo A15 del 06) — mitigación vigente: `throttle` + `log.404`. Rendimiento (R9 del 06 §10, 2026-09-18): **sin límites artificiales** — el export intenta siempre (el visor es la referencia de tamaño, carga seccional); `CuadroExcelExport` usa autosize de columnas **condicional** (solo si el cuadro real ≤ 20k celdas); plan para cuadros >20k queda en pendientes del 06 (memory_limit del hosting o export en cola). Layout del `.xlsx` (2026-09-25): todo el contenido (logo, título, subtítulo, secciones, grilla y pie) vive desde la **columna B** con la columna A vacía de ancho fijo 12; el logo (B1) queda separado del título por dos filas extra; autosize de B en adelante; cada grupo de categoría padre de fila (salvo el primero) lleva borde superior `medium` con el color del tema (fallback `#adb5bd`) en la primera fila visible de su bloque. Estilo tema en Excel (2026-09-25, GEI): pivote con color pleno y centrado H/V (borde inferior `medium` del tema), categorías padre al 75 %, hojas al 50 %, hijas verticales al 25 %, datos alternados al 12.5 % y totales al 60 % en negrita (misma paleta `hexToIntensity` del visor; si no hay tema se conserva la paleta anterior).
- Seguridad pública: `throttle:60,1` (grupo `sigem-v2`), 30/min en `/track`, `log.404` global (20×404 en 300 s → registro de IP para análisis/bloqueo administrativo), headers `SecurityHeaders`, `no-store` en rutas públicas (`PreventBackHistory`).
- Render híbrido servidor+cliente (no SPA); no cambiar el modelo de los datos de la grilla sin coordinar con `DatasetViewService` y `dataset.blade.php`.

## Gotchas del equipo (06)

`V1` Excel roto fuera de producción es falsa alarma (falta `vendor/`), `V2`/`V3` dependencias v1 purgadas, `V4` la clave de caché no incluye `publicado` (leak ≤5 min, pendiente — no empeorar), `V5` cookie `_vuid` con `secure` dinámico, `V6` consulta express pública es modal de inicio (sin página propia).

## Requisitos asociados

RF-15 a RF-32 (módulos SIGEM y sus accesos/métricas); RNF-02 (responsive), RNF-09/15 (rate limiting), RNF-10 (seudonimización de IP), RNF-22 (caché TTL 300 con invalidación síncrona), RNF-23 (carga ≤3 s p95).