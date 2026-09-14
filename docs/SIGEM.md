# SIGEM — Visor Público de Estadísticas

> Fuente de verdad: `archivoslocales/Especificaciones/02_Documentacion_SIGEM_Visor_Publico.md`. Resumen *spec-anchored*.

## Qué es

Cara **pública** de consulta de cuadros estadísticos (dataset, gráficas, mapas y exportación a Excel). Consume el contenido producido por SGIEM (solo `publicado=true`).

- Prefijo de rutas: `sigem-v2` · ruta raíz `/sigem-v2` · archivo `routes/VisorSIGEM/laravel_v2.php`.
- Middleware: grupo web + `throttle:60,1` en `/cuadro/*` + `throttle:30,1` en `/track` + `log.404` (detección de escaneo).
- **V1 legacy (`/sigem`): NO tocar**, está en retiro; solo referencia en `routes/SIGEM/laravel.php` y partials v1.

## Estructura

- Controladores (5): `SIGEMV2Controller` (páginas + consulta express), `VisorCuadroController` (dataset/grafica/mapa/documento + `verificarAccesoCuadro` + credenciales), `DatasetViewController` (inspección), `DocumentoController` (exportar Excel con firma `?s=`), `Controller` (base: `registrarEvento`, track, detección de bots).
- Servicios (5): `CatalogoService`, `EstadisticaService`, `DatasetViewService`, `ConsultaExpressService`, `CuadroExcelExport` (export `app/Exports/`).
- Modelos: `Cuadro`, `CuadroCategoria`, `CuadroDato`, `CuadroSeccion`, `PubVisita`, `PubVisitante`, `ce_tema`, `ce_subtema`, `ce_contenido`.
- Vistas: `resources/views/VisorSIGEM/` (`layouts/visor.blade.php`, `pages/*`, `cuadro/*`).

## Flujo maestro: `GET /cuadro/{id}/dataset`

```
verificarAccesoCuadro:
  - Cuadro no existe → abort(404)
  - !publicado → abort(403, 'El cuadro no está disponible')
  - permite_acceso_publico=1 → OK
  - requiere credencial → BD (tieneCredenciales) o sesión (24 h) → si no, redirect a página de credenciales
  - tipo_mapa_pdf=1 → redirect /documento (muestra el PDF, no hay dataset)

Cache::remember("visor_cuadro_estado_{id}", 300)  ← TTL 300 s
  - miss → DatasetViewService::datosCuadro (categorías verticales/horizontales, secciones, celdas)

registrarEvento('dataset') si no es bot:
  - PubVisitante::firstOrCreate([ip_hash]) → total_visitas+1
  - PubVisita::create({_vuid, ip_hash, cuadro_id, evento:'dataset', credenciales})
```

La grilla se construye en el **servidor** (Blade + `@json($estadoInicial)`); el JS renderiza tablas y pide secciones por AJAX (`/dataset/seccion/{s}/data`, cacheada por sección TTL 300).

## Reglas duras

- El visor **solo lee** BD y **solo escribe métricas** (`pub_visita`/`pub_visitante`). La invalidación de caché la hace **SGIEM** (`invalidarCacheVisor`).
- Identidad anónima: cookie `_vuid` (`SetVisitorUuid`, UUID httpOnly, 10 años, `secure` dinámico). IP se almacena **hasheada** (`HashIp`, sha1 + salt), nunca en claro para visitantes.
- Detección de bots por User-Agent (allowlist) → cuenta pero marca `is_bot=1`.
- Exportación Excel requiere firma HMAC de secciones (`?s=`), validada en `DocumentoController`.
- Seguridad pública: throttle 60/30, `log.404` (20 errores en 300 s → bloqueo de IP), headers `SecurityHeaders`, `no-store` en rutas públicas (`PreventBackHistory`).
- Render híbrido servidor+cliente (no SPA); no cambiar el modelo de los datos de la grilla sin coordinar con `DatasetViewService` y `dataset.blade.php`.

## Gotchas del equipo (06)

`V1` Excel roto fuera de producción es falsa alarma (falta `vendor/`), `V2`/`V3` dependencias v1 purgadas, `V4` la clave de caché no incluye `publicado` (leak ≤5 min, pendiente — no empeorar), `V5` cookie `_vuid` con `secure` dinámico, `V6` consulta express pública es modal de inicio (sin página propia).

## Requisitos asociados

RF-15 a RF-32 (módulos SIGEM y sus accesos/métricas); RNF-02 (responsive), RNF-09/15 (rate limiting), RNF-10 (seudonimización de IP), RNF-22 (caché TTL 300 con invalidación síncrona), RNF-23 (carga ≤3 s p95).