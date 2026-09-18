# SGU — Sistema de Gestión de Usuarios, Accesos y Métricas

> Fuente de verdad: `archivoslocales/Especificaciones/03_Documentacion_SGU_Usuarios_Accesos_Metricas.md`. Resumen *spec-anchored*.

## Qué es

Administra usuarios y roles, registra accesos (autenticación y auditoría) y presenta métricas de uso del visor SIGEM.

- Prefijo de rutas: `sgu.admin` · ruta raíz `/sgu/admin` · archivo `routes/SGU/web.php`.
- Middleware de grupo: `auth` + `role:Administrador,Desarrollador`.
- **Autenticación global** (login en 2 pasos) vive en `routes/Bomberos/web.php` + `app/Http/Controllers/Bomberos/Auth/LoginController.php` — el mismo login sirve a todos los módulos.

## Estructura

- Controladores (3) en `app/Http/Controllers/SGU/`: `DashboardController` (métricas del visor), `GestorController` (CRUD usuarios + PIN + estado), `AuditorController` (auditoría de accesos y de usuarios).
- Modelos en `App\Models\SGU\`: `User` (tabla `users`: `name`, `email`, `password`, `role`, `status`, `log_in_status`, `initial_token`), `AuditoriaUsuario`, `Traits\AuditableUsuario`. En `App\Models\SIGEM\`: `AuditoriaAcceso`.
- Vistas: `resources/views/sgu/` (`layouts/admin.blade.php`, `admin/dashboard.blade.php`, `gestor/usuarios.blade.php`, `auditor/accesos.blade.php`, `auditor/usuarios.blade.php`).
- Métricas: lee `pub_visita`/`pub_visitante` (las produce el visor SIGEM).

## Flujo: login (AJAX + un POST)

```
POST /login/check-email (login.throttle 10/60s por IP + RateLimiter 5/300s por email)
  → respuesta uniforme anti-enumeración: requires_pin / requires_password según log_in_status
POST /login {email, password o pin} (login.throttle)
  → PIN contra initial_token hasheado (log_in_status 1/2) o contraseña (Auth::attempt)
  → status inactivo → fallo auditado
  → session()->regenerate() (anti-fixation) + AuditoriaAcceso + redirect según rol
```

Primer acceso (status 1/2): PIN correcto (hash `initial_token`, marcador de sesión `pin_verificado`) → redirige a `/password/reset` para crear contraseña (política RNF-06) y editar el nombre si status=1; el PIN no se persiste en claro. Mapa de roles: Dev/Admin → `sgu.admin.index`, Estadístico → `sgiem.admin.index`, otros → su panel.

## Flujo: gestión de usuarios (`routes/SGU/web.php:17-21`)

- `GET /sgu/admin/gestor/usuarios` → listado con filtro de estado.
- `POST /sgu/admin/gestor/usuarios` → alta + PIN aleatorio hasheado (`initial_token`) + status inicial.
- `PUT /sgu/admin/gestor/usuarios/{user}` → edición de rol y `status` (la baja/desactivación se hace vía `update`; no existe DELETE ni `toggle-estado` — ver 06 S1).
- `POST /sgu/admin/gestor/usuarios/{user}/generar-pin` → nuevo PIN (se muestra **una sola vez** en modal, ver 06 S9).

## Reglas duras

- **Auditar todo acceso a SGU y todo cambio de usuario**: `AuditoriaAcceso` (login/fallo/logout) y `AuditoriaUsuario` (vía trait `AuditableUsuario` — respetar el patrón al modificar `User`). Requisito RNF-17/18.
- **Anti-enumeración**: `checkEmail` y mensajes de error unificados (no revelar si el email existe).
- **PIN**: no persistir en sesión; mostrar una sola vez; regeneración auditada.
- **No exponer internos** en respuestas (S11).
- Conservar el endpoint de `checkEmail` (lo consume el flujo de login).

## Gotchas del equipo (06)

`S1` ruta `DELETE /admin/users/{user}` purgada; `S3` no usar migraciones como referencia (BD manual); `S5` mapa de roles unificado; `S7` no hay expiración de PIN; `S9` PIN en modal con copiar/confirmación; `S4` dashboard con `->get()` sin paginar (pendiente rediseño) — no empeorar sin consulta; R2b (06 §10): `ultimasVisitas` con `limit($limiteVisitas)` + selector «Últimas 100/250/500» (`?visitas`); R11: auditoría de accesos y usuarios con `paginate(100)` (2026-09-18).

## Requisitos asociados

RF-02 a RF-14 (login, roles, primer acceso), RF-47 a RF-54 (módulos SGU); RNF-06/07 (política y hash de contraseñas), RNF-09 (rate limit de login), RNF-18 (auditoría de accesos), RNF-19 (validación de estado en cada request), RNF-28 (SGU con rutas/controladores/vistas propios).