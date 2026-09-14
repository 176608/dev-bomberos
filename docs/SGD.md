# SGD — Sistema de Gestión de Dictámenes

> Fuente de verdad: código fuente + `archivoslocales/documentacion tecnica/Manual_SGD.md` (manual de usuario, **legacy** — verificar contra el código). Resumen *spec-anchored*.
> Nota: SGD **no forma parte** de los 54 RF de `12_Requisitos_Funcionales_y_No_Funcionales.md` (ese alcance cubre SIGEM/SGIEM/SGU). Es un módulo auxiliar interno del IMIP.

## Qué es

Gestión de dictámenes y oficios del Instituto Municipal de Investigación y Planificación (IMIP). Dos caras:

| Cara | Ruta | Acceso |
|---|---|---|
| Gestor | `/admin/GestorDictamenes` | `auth` + roles propios |
| Visor público | `/VisorDictamenes` | Público, solo lectura |

- Archivos de rutas: `routes/GestorDictamenes/web.php` y `routes/VisorDictamenes/web.php`.

## Roles (nombres con espacio — cuidado al usar `role:`)

`Administrador Dictamenes`, `Editor Dictamenes`, `Desarrollador` (acceso total, bypass ya documentado en CheckRole). Permisos por acción:

| Acción | Admin | Editor |
|---|---|---|
| Ver listado / historial | sí | sí |
| Crear dictamen | sí | no |
| Editar dictamen | sí | sí |
| Deshabilitar / restaurar / ver deshabilitados | sí | no |
| Subir / eliminar archivos | sí | no |
| Descargar / ligar / desvincular archivos | sí | sí |

## Estructura

- Controlador único: `app/Http/Controllers/GestorDictamenes/DictamenController.php` (~567 ln) + `VisorDictamenes/DictamenController.php` (público).
- Modelos en `App\Models\GestorDictamenes\`:
  - `Dictamen` (tabla `dictamenes`): constantes `STATUSES` (`ENVIADO`, `BORRADOR PARA FIRMA`, `EN REVISION`, `INFORMATIVO`, `S/D`), `DESHABILITADO`, `MESES`, `AUTOFILL_OFICIO_RECIBIDO`; campos `anio/dia/mes/fecha_raw` derivados de `fecha`; relación `creador()`/`actualizador()` → `SGU\User`; `archivosLigados()`.
  - `DictamenArchivo` (tabla `dictamenes_archivos`): `dictamen_id`, `anio`, `nombre_archivo`, `created_by`.
  - `AuditoriaDictamen` (tabla `auditoria_dictamenes`, PK `auditoria_id`): `user_id`, `dictamen_id`, `accion`, `datos_previos`/`datos_nuevos` (arrays).
- Vistas: `resources/views/gestor-dictamenes/` y `resources/views/visor-dictamenes/`.

## Flujos clave

1. **Listado**: filtros combinados (estatus, año, mes, revisado_por, dependencia, tipo_dictamen) + anotación del estado de liga de archivos por número de oficio + datos para gráfica (mes × estatus).
2. **Crear/editar**: `store`/`update`; textos pasan por `mayus()` (MAYÚSCULAS); fecha → `anio/dia/mes/fecha_raw`; autorelleno de `oficio_recibido` según `tipo_dictamen` (`AUTOFILL_OFICIO_RECIBIDO`).
3. **Archivos**: disco `storage/app/dictamenes` organizado por año; subida con manejo de conflictos de nombre (revisar/reemplazar/dejar); liga automática por coincidencia de número de oficio y manual vía modal; descarga/eliminación con control por rol.
4. **Deshabilitar/restaurar**: `destroy` marca `DESHABILITADO` (soft, no se elimina); `restore` vuelve como `S/D`; listado de deshabilitados en `GET /deleted`.
5. **Historial**: `GET /historial` (últimos N registros) con comparación antes/después campo por campo.

## Reglas duras

- **Auditar** toda operación en `AuditoriaDictamen` (crear/modificar/deshabilitar/restaurar) con `user_id`, `datos_previos`/`datos_nuevos` (seguir el patrón del `DictamenController`).
- Registrar `created_by`/`updated_by` en todo cambio.
- Consistencia: eliminar archivo desvincula de dictámenes; deshabilitar excluye del listado principal y del visor público.
- El visor público **no** expone estatus, observaciones ni número de oficio de salida.

## Gotchas (Manual_SGD sec. 13, verificar con código)

Solo `.doc/.docx` (hasta 20 MB según manual), sin exportación, historial limitado a ~1000 registros, estatus DESHABILITADO no seleccionable directo, campos en mayúsculas, liga automática por coincidencia de prefijo.