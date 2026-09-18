# SGIEM — Gestor de Contenido Estadístico (admin v2)

> Fuente de verdad: `archivoslocales/Especificaciones/01_Documentacion_SGIEM_Gestor_Contenido_Estadistico.md`. Resumen *spec-anchored*.

## Qué es

Backend de **administración del contenido estadístico** que consume el visor SIGEM. Escribe tema/subtema/cuadro/dataset y consulta express; la publicación controlada la lee el visor (`/sigem-v2`).

- Prefijo de rutas: `sgiem.admin` · ruta raíz `/sgiem/admin` · archivo `routes/GestorSIGEM/web.php`.
- Middleware de grupo: `auth` + `role:Administrador,Desarrollador,Estadistico` (los roles de captura NO entran).

## Estructura

- Controladores (5): `AdminController` (dashboard + cambios + detalle auditoría), `TemaController` (temas/subtemas), `CuadroV2Controller` (CRUD cuadros, publicación, dataset/grafica/documento manage, PDFs), `DatasetController` (editor AJAX, 25 endpoints), `ConsultaExpressController`.
- Servicios (7): `TemaService`, `SubtemaService`, `CuadroV2Service`, `DatasetService` (~1020 ln, grilla + operaciones), `DatasetGridPresenter`, `ConsultaExpressService`, `AuditoriaDatasetService`.
- FormRequests (7) en `app/Http/Requests/GestorSIGEM/` con `authorize()` por rol + `validate()`.
- Vistas: `resources/views/GestorSIGEM/layout.blade.php` (shell) + `GestorSIGEM/admin/*`.

## Modelos (v2, `App\Models\SIGEM\`)

```
tema_v2 1──< subtema_v2 1──< cuadro_v2 1──< cuadro_secciones
                              │            └──< cuadro_datos (cat_vertical × cat_horizontal, seccion_id)
                              └──< cuadro_categoria (eje vertical/horizontal, padre_id ≤ 2 niveles)
```

- `CuadroCategoria`: jerarquía 2 niveles (`padre_id`, autorelación), eje, `orden`, `tipo` (dato/total/promedio/porcentual).
- `CuadroDato`: una fila por celda (`valor`, `valor_crudo`, `fila`, `columna`).
- `CuadroSecciones`: copias de estructura con datos propios; `header`/`footer` **sanitizados con `HtmlSanitizer`**.
- `Cuadro`: metadatos (`codigo_cuadro` único, `c_titulo`, `publicado`, `tipo_mapa_pdf`, `tipos_grafica_permitida` JSON, `pivot_label`, `pdf_file`).
- Consulta express: `ce_tema`, `ce_subtema`, `ce_contenido` (`tabla_datos` JSON 2D).

## Flujos clave (detalle y diagramas en Especificaciones/01)

1. **CRUD temas/subtemas**: orden automático; imagen de subtema vía `SecureFileUpload`; eliminar tema con subtemas asociados lanza excepción.
2. **CRUD cuadros**: `pie_pagina`/`piepagina_gen` pasan por `HtmlSanitizer`; `toggle-publicado` invierte flag; al eliminar se borra el PDF de disco si existe.
3. **Editor de dataset** (corazón del gestor): grilla relacional editada por AJAX (crear desde vacío pide el **nombre del pivote** — obligatorio, placeholder «Concepto» — y genera cuadrícula 1×1; el tamaño crece agregando filas/columnas en Modo Diseño; filas/columnas, jerarquía hijo, clonar categoría individual con validación de hermanos en el modal, y clonar-lista transaccional FIFO con barra de progreso, celdas, pegado, secciones, importar estructura entre cuadros, pivot, tipos de gráfica, regenerar, limpiar).
4. **Auditoría del dataset por sesión**: apertura → snapshot en caché (8 h); cierre → compara firma y registra solo si cambió; cierre diferido vía middleware `CerrarSesionAuditoriaDataset`.
5. **Consulta Express**: CRUD de temas/contenidos CE con dimensión validada y estructura 2D.
6. **Cambios/auditoría**: `GET /cambios` unifica `auditoria_sgiem` + `auditoria_datasets`; detalle con `?tipo=dataset`.
7. **Dashboard**: métricas de visita (13 KPIs) sobre `pub_visita`/`pub_visitante`.

## Reglas duras (sí o sí al tocar este módulo)

- **Cada mutación llama `invalidarCacheVisor($cuadroId)`** (`Cache::forget` de `visor_cuadro_estado_{id}` y `visor_cuadro_{id}_seccion_{seccionId}`) — definida en `CuadroV2Controller.php:26-34` y `DatasetController.php:18-24`.
- **Transacciones (`DB::transaction`)** en las 4 operaciones destructivas del dataset: `generarGrilla`, `pasteGrid`, `eliminarDataset`, `importarEstructura` (requisito RNF-32). El clonado de categorías (individual `clonarCategoria` y lista `clonarListaCategoria`) también corre dentro de transacción (todo-o-nada).
- **Unicidad de nombres por scope**: no pueden existir dos categorías hermanas con el mismo nombre — mismo scope = raíces del mismo eje (`padre_id IS NULL`) o hijos del mismo padre; sí se permiten nombres iguales bajo padres distintos. Enforcement: los modales del editor validan antes de enviar (`makeValidateSibling` con el eje/padre reales de la fuente) y el backend garantiza el invariante con auto-sufijo ` (n)` vía `generarNombreUnico` en `agregarFila`/`agregarColumna`/`agregarHijo`/`clonarCategoria`/`clonarListaCategoria`; el renombrado lo implementa `actualizarCategoria` (flag `_renombrado`).
- **Sanitizar HTML** (`HtmlSanitizer`) todo input enriquecido; **nunca** deshabilitar.
- **Auditar** con los patrones existentes: trait `AuditableSgiem` para modelos; `AuditoriaDatasetService` para sesiones de dataset. No añadir auditoría por celda (ruido; ver decisión en 06 G4 y doc 01 Sec 5).
- **Autorización**: `authorize()` por rol en FormRequests + middleware `role:` en rutas.
- **No exponer internos** (`$e->getMessage()`, nombres de tablas/columnas, SQL) en respuestas al usuario (bug G5). Mensaje genérico + `Log::error`.
- No depender de rutas v1 (`/sigem/*`) ni de módulos legacy.
- Ajuste de accesibilidad del editor de dataset: el tamaño de los botones de acción de categorías (filas y columnas) es elegible por radio («Extra-chicos / Medianos / Grandes») junto a los tabs de modo, visible solo en Diseño; preferencia en `localStorage` (`sgiem.dataset.btnSize`) + `sessionStorage` — no hardcodear tamaños de esos botones fuera de las variables CSS (`--btn-pad`/`--btn-font`).

## Gotchas del equipo (06)

`G1` CRUD de cuadros 100% por modales (no hay `create/show/edit`), `G2/G6/S5` mapa de roles unificado, `G3` `codigo_cuadro` único, `G6` importación por archivo purgada (conservar "Importar configuración" entre cuadros), `G10` política Dev (máx 2 cuentas, solo 1 Dev da de alta Devs).

## Requisitos asociados

RF-33 a RF-46 (módulos SGIEM); RNF-26 (controladores ≤300 ln y lógica en servicios), RNF-29 (desacoplado de rutas públicas, comunicación vía BD + servicios), RNF-32 (transacciones), RNF-35 (trazabilidad del dataset por sesión).