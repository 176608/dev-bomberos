# SGU — Sistema de Gestión de Usuarios, Accesos y Métricas

> Fuente de verdad: `archivoslocales/Especificaciones/03_Documentacion_SGU_Usuarios_Accesos_Metricas.md`. Resumen *spec-anchored*.

## Qué es

Administra usuarios y roles, registra accesos (autenticación y auditoría) y presenta métricas de uso del visor SIGEM.

- Prefijo de rutas: `sgu.admin` · ruta raíz `/sgu/admin` · archivo `routes/SGU/web.php`.
- Middleware de grupo: `auth` + `role:Administrador,Desarrollador`.
- **Autenticación global** (login en 2 pasos) vive en `routes/Bomberos/web.php` + `app/Http/Controllers/Bomberos/Auth/LoginController.php` — el mismo login sirve a todos los módulos.

## Estructura

- Controladores (3) en `app/Http/Controllers/SGU/`: `DashboardController` (métricas del visor), `GestorController` (CRUD usuarios + PIN + estado), `AuditorController` (auditoría de accesos y de usuarios).
- Modelos en `App\Models\SGU\`: `User` (tabla `users`: `email`, `password`, `pin`, `rol`, `estado`, `nivel`, `last_login_at`, `last_login_ip`), `AuditoriaUsuario`, `Traits\AuditableUsuario`. En `App\Models\SIGEM\`: `AuditoriaAcceso`.
- Vistas: `resources/views/sgu/` (`dashboard.blade.php`, `gestor.blade.php`, `auditoria_accesos.blade.php`, `auditoria_usuarios.blade.php`) + `auth/` (login/pin).
- Métricas: lee `pub_visita`/`pub_visitante` (las produce el visor SIGEM).

## Flujo: login en 2 pasos

```
POST /login {email} (LoginRateLimiter: 10/60s por IP; 5/60s por email)
  → existe usuario? (respuesta anti-enumeración unificada "Credenciales incorrectas")
  → inactivo? → fallo auditado
  → session login_email → GET /login/pin
POST /login/pin {pin_or_password} → PIN (primer acceso) o contraseña
  → Auth::login + regenerar sesión (anti-fixation) + last_login_at/ip
  → AuditoriaAcceso (login) → redirect según rol
```

Primer acceso: PIN de un solo uso → el sistema solicita crear contraseña (`PasswordResetRequired`). Mapa de roles: Dev/Admin → `sgu.admin.index`, Estadístico → `sgiem.admin.index`, otros → su panel.

## Flujo: gestión de usuarios

- `POST /gestor/usuarios/crear` → alta + PIN aleatorio (`password_hash`).
- `PUT /gestor/usuarios/{id}/actualizar` → edición; contraseña re-encriptada si viene.
- `GET /gestor/usuarios/{id}/generar-pin` → nuevo PIN (se muestra **una sola vez** en modal, ver 06 S9).
- `DELETE /.../eliminar` → soft-delete (flag) + auditoría; `POST /.../toggle-estado` → activar/inactivar + auditoría.

## Reglas duras

- **Auditar todo acceso a SGU y todo cambio de usuario**: `AuditoriaAcceso` (login/fallo/logout) y `AuditoriaUsuario` (vía trait `AuditableUsuario` — respetar el patrón al modificar `User`). Requisito RNF-17/18.
- **Anti-enumeración**: `checkEmail` y mensajes de error unificados (no revelar si el email existe).
- **PIN**: no persistir en sesión; mostrar una sola vez; regeneración auditada.
- **No exponer internos** en respuestas (S11).
- Conservar el endpoint de `checkEmail` (lo consume el flujo de login).

## Gotchas del equipo (06)

`S1` ruta `DELETE /admin/users/{user}` purgada; `S3` no usar migraciones como referencia (BD manual); `S5` mapa de roles unificado; `S7` no hay expiración de PIN; `S9` PIN en modal con copiar/confirmación; `S4` dashboard con `->get()` sin paginar (pendiente rediseño) — no empeorar sin consulta.

## Requisitos asociados

RF-02 a RF-14 (login, roles, primer acceso), RF-47 a RF-54 (módulos SGU); RNF-06/07 (política y hash de contraseñas), RNF-09 (rate limit de login), RNF-18 (auditoría de accesos), RNF-19 (validación de estado en cada request), RNF-28 (SGU con rutas/controladores/vistas propios).