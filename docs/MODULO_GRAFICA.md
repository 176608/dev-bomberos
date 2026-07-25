# Módulo de Gráfica (Standalone)

## Arquitectura

### Frontend

El módulo de gráfica tiene su propia vista Blade independiente del editor de dataset:

```
resources/views/GestorSIGEM/admin/grafica.blade.php
```

Se sirve a través de `GestorSIGEM.layout` (con el navbar del admin) mediante la variable `$crud_view`, al igual que el resto de vistas del panel.

### Ruta

```
GET /sgiem/admin/cuadros/{id}/grafica
→ name: sgiem.admin.cuadros.grafica
→ CuadroV2Controller::graficaManage()
```

### Controlador

`CuadroV2Controller::graficaManage($id)` (`app/Http/Controllers/GestorSIGEM/CuadroV2Controller.php:100`):

1. Obtiene el `Cuadro` mediante `CuadroV2Service::obtenerPorId()`
2. Obtiene el estado completo del dataset mediante `DatasetService::obtenerEstado()` (mismo endpoint que el editor de tabla)
3. Renderiza la vista `GestorSIGEM.admin.grafica` con `$cuadro` y `$estadoInicial`

No se necesita un service nuevo — `DatasetService::obtenerEstado()` ya devuelve todo lo que la gráfica requiere: verticales, horizontales, headers, labels, data grid, secciones, tipos_grafica_permitida.

## Funcionalidad

### Panel de Categorías

El panel lateral (`#chart-panel`) permite controlar la visibilidad de cada categoría:

- **Checkbox padre**: toggle all children on/off. Icono de carpeta abierta/cerrada.
- **Checkbox hijo**: toggle individual. Sincroniza el estado del padre (checked si todos visibles, unchecked si ninguno).
- **Eje X**: select que define qué eje se usa como labels (verticales/horizontales).
- **Agrupar por**: agrupa series por padre horizontal o vertical.
- **Sección**: select para cambiar entre secciones del dataset.

### Jerarquía padre-hijo

La relación padre-hijo se construye a partir de `padre_id` en los arrays `estado.verticales`/`estado.horizontales` (provienen de la BD). Los nombres de los padres se resuelven desde `estado.headers`/`estado.labels` buscando celdas con `tipo === 'parent'`.

### Generación de datos para Chart.js

`buildChartData()` transforma la grilla plana del dataset en datasets de Chart.js:

1. **Selección de ejes**: intercambia labels/series según el eje X seleccionado.
2. **Filtro de visibilidad**: excluye categorías desmarcadas en el panel.
3. **Agrupación**: si se selecciona agrupar por padre, colorea las series según el grupo.
4. **Parseo de valores**: `parseCellValue()` limpia separadores de miles (comas) antes de convertir a número.

### Parseo de valores numéricos

`parseCellValue(val)` (`grafica.blade.php:154`):

| Entrada | Salida | Notas |
|---------|--------|-------|
| `"1,212,522"` | `1212522` | Comas de miles eliminadas |
| `"785,054"` | `785054` | Comas de miles eliminadas |
| `"85.5"` | `85.5` | Decimales con punto |
| `"85,5"` | `85.5` | **No manejado** (se interpreta como coma de miles → `855`) |
| `42` (number) | `42` | Ya es número, se pasa directo |
| `""` | `NaN` | Vacío → NaN |
| `undefined` | `NaN` | Indefinido → NaN |

### API calls

Las llamadas a la API usan el mismo `BASE` que el editor de dataset (`/sgiem/admin/cuadros/{id}/dataset/...`) porque los endpoints de estado, secciones y tipos de gráfica pertenecen al grupo de rutas del dataset.

| Llamada | Endpoint | Propósito |
|---------|----------|-----------|
| `GET /seccion/{sid}/data` | `BASE + '/seccion/' + sid + '/data'` | Cambiar sección activa |
| `PUT /tipos-grafica` | `BASE + '/tipos-grafica'` | Guardar tipos de gráfica asignados |

## Cambios realizados

### Archivos creados

| Archivo | Descripción |
|---------|-------------|
| `resources/views/GestorSIGEM/admin/grafica.blade.php` | Frontend standalone de gráfica con panel de categorías, controles y Chart.js |

### Archivos modificados

| Archivo | Cambio |
|---------|--------|
| `routes/GestorSIGEM/web.php` | Nueva ruta `GET /{id}/grafica` |
| `app/Http/Controllers/GestorSIGEM/CuadroV2Controller.php` | Nuevo método `graficaManage()` |
| `resources/views/GestorSIGEM/admin/cuadros.blade.php` | Botón "Gráfica" habilitado (apunta a la nueva ruta) |
| `resources/views/GestorSIGEM/admin/dataset_manage.blade.php` | Botón "Gráfica" reemplazado por link. Se eliminaron ~550 líneas de código de gráfica (HTML, CSS, JS). |

### Flujo de navegación

```
Lista de cuadros → [Dataset] → /dataset   (editor de tabla)
Lista de cuadros → [Gráfica] → /grafica   (gráfica standalone)
Editor de tabla  → [Gráfica] → /grafica   (link en mode-tabs)
Gráfica          → [Dataset] → /dataset   (link en toolbar)
```
