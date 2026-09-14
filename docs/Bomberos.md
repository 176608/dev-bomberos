# Bomberos — Autenticación Global + Registro de Hidrantes

> No tiene especificación propia. El flujo de autenticación está documentado en `archivoslocales/Especificaciones/03_Documentacion_SGU_Usuarios_Accesos_Metricas.md` (sec. 3.2, 4.1). Resumen *spec-anchored*: verificar contra el código.
> Nota: módulo auxiliar — no forma parte de los 54 RF de `12_Requisitos_Funcionales_y_No_Funcionales.md`.

## Qué es

Dos responsabilidades en `routes/Bomberos/web.php` + controladores de `app/Http/Controllers/Bomberos/`:

1. **Autenticación global** (login en 2 pasos + resets) que usan TODOS los módulos del sistema.
2. **Sistema de hidrantes** con paneles por rol y consultor público.

## Autenticación global (cómo funciona en el sistema)

- `GET /login` → `showLoginForm` · `POST /login` (email, paso 1, `login.throttle`) · `GET/POST /login/pin` (paso 2: PIN o contraseña) · `POST /logout`.
- `POST /login/check-email` — validación anti-enumeración (`checkEmail`, respuesta uniforme).
- `GET/POST /password/reset` (`throttle:5,30`).
- Detalles completos del flujo en `docs/SGU.md` y Especificaciones/03.

## Sistema de hidrantes

### Rutas

| Ruta | Controlador | Rol | Función |
|---|---|---|---|
| `/consultor`, `/consultor/buscar`, `/hidrante-pdf/{id}` | `DashboardController` | Público | Landing + búsqueda + PDF |
| `/admin` | `AdminController` | `Administrador,Desarrollador` | Panel admin (usuarios, PIN) |
| `/desarrollador` | `DesarrolladorController` | `Desarrollador` | Panel dev |
| `/capturista` | `CapturistaController` | `Capturista,Desarrollador` | Registro/edición de hidrantes |
| `/registrador/*` (catalogos zonas/vías) | `RegistradorController` | `Registrador,Desarrollador` | CRUD catálogos |
| `/hidrantes/*`, `/configuracion/*` | `CapturistaController` | `Capturista,Administrador,Desarrollador` | CRUD hidrantes + configuración |

### Estructura

- Controladores: `AdminController`, `DesarrolladorController`, `CapturistaController` (~1240 ln), `RegistradorController`, `DashboardController`, `Controller` (base común), `Auth/LoginController`, `Auth/PasswordResetController`.
- Modelos en `App\Models\Bomberos\`: `Hidrante`, `Calles`, `Colonias`, `CatalogoCalle`, `ConfiguracionCapturista`, `CambioEnHidrante`.

### Modelo `Hidrante` (tabla `hidrantes`)

- `stat` (código de estatus 3 dígitos, ej. `000` = desactivado), fecha/calles/colonias, llaves, presión, estado, marca, año, observaciones, `oficial`, `create_user_id`/`update_user_id`.
- `calcularStat()`: puntaje de completitud sobre 13+ campos (total variable según `y_calle`/`colonia` presentes), devuelve porcentaje de 3 dígitos.
- **Gotcha de huso horario**: los accessors `getCreatedAtAttribute`/`getUpdatedAtAttribute` restan 6 horas (UTC→local) — al manipular fechas, tenerlo en cuenta.

### Flujos clave

- **Capturista**: CRUD de hidrantes (DataTables, columnas configurables guardadas en `ConfiguracionCapturista` por usuario), activar/desactivar (`stat`), resumen y **historial de cambios** (`CambioEnHidrante`).
- **Registrador**: mantiene catálogos de zonas y vías.
- **Consultor público**: busca por id, valida activos, genera PDF con DomPDF (solo hidrantes activos; los `stat='000'` se ocultan salvo rol autorizado).

## Reglas duras

- No modificar el flujo de login sin revisar SGU (`docs/SGU.md`) y Especificaciones/03 — es compartido por todos los módulos.
- Respetar el reparto de roles por ruta de `routes/Bomberos/web.php` (no ampliar permisos sin decisión del equipo).
- Los catálogos de calles/colonias son datos existentes (no crear modelos nuevos duplicados).