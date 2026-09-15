# Sistema Auxiliar — Visión Holística (contexto para agentes)

> Fuentes de verdad: `archivoslocales/Especificaciones/04_Documentacion_Holistica_Middleware_Backend_Frontend.md`, `05_Documentacion_Modelo_C4.md`, `07_Documentacion_Tecnica_Flujo_y_Mapa.md`, `10_Mapa_E_R.md`, `11_Spec_Driven_Development_Metodologia.md`, `12_Requisitos_Funcionales_y_No_Funcionales.md`.
> Resumen *spec-anchored*: si el código cambia, actualiza este archivo.

## Qué es

Sistema **Laravel 12** (PHP ^8.2, MySQL/MariaDB), monotoblero en español, con módulos estadísticos (SIGEM/SGIEM), gestión de usuarios (SGU) y módulos auxiliares (SGD/Bomberos/Biblioteca). Todo el dominio y la UI en **español**.

## Mapa de módulos (montados desde `routes/web.php`)

| Módulo | Ruta raíz | Público | Middleware de panel |
|---|---|---|---|
| Bomberos (auth global + hidrantes) | `/` · `/login` · `/consultor` · paneles por rol | Parcial (login, consultor) | `auth` + `role:` por panel |
| SIGEM v1 (legacy, retiro) | `/sigem` | Sí (visor v1) | — |
| SIGEM v2 (Visor público) | `/sigem-v2` | Sí | `throttle:60,1` |
| SGIEM v2 (Gestor admin) | `/sgiem/admin` | No | `role:Administrador,Desarrollador,Estadistico` |
| SGU (usuarios y métricas) | `/sgu/admin` | No | `role:Administrador,Desarrollador` |
| SGD Gestor | `/admin/GestorDictamenes` | No | `role:Administrador Dictamenes,Editor Dictamenes,Desarrollador` |
| SGD Visor | `/VisorDictamenes` | Sí | — |
| Biblioteca | `/biblioteca` · `/search` | Sí | `throttle:60,1` |

## Roles conocidos

`Administrador`, `Desarrollador`, `Estadistico`, `Capturista`, `Registrador`, `Consultor` (público), `Administrador Dictamenes`, `Editor Dictamenes`, y roles de auditoría en SGU. Autorización por **middleware propio `CheckRole`** (uso inline `role:...`).

Mapa de redirección por rol (unificado, ver `06` S5): `Desarrollador`/`Administrador` → `sgu.admin.index`; `Estadistico` → `sgiem.admin.index`; los paneles `Capturista`/`Registrador`/Dictámenes redirigen a los suyos.

## Pipeline HTTP (grupo `web`, orden efectivo)

`EncryptCookies → Session → CSRF → PreventBackHistory → SecurityHeaders → SetVisitorUuid → DebugByRole → CerrarSesionAuditoriaDataset → (ruta)`

Middlewares propios: `CheckRole` (role), `LoginRateLimiter` (login.throttle), `LogSuspicious404` (log.404, escaneo de rutas), `DebugByRole`, `PreventBackHistory`, `SecurityHeaders`, `SetVisitorUuid`, `CerrarSesionAuditoriaDataset`, `PasswordResetRequired`.

## Auditoría (patrón por módulo, respetar al añadir modelos)

| Modelo auditable | Tabla | Trait/Service |
|---|---|---|
| TemaV2, SubtemaV2, Cuadro, ce_* | `auditoria_sgiem` | `App\Models\SIGEM\Traits\AuditableSgiem` |
| Dataset (sesión de edición) | `auditoria_datasets` | `App\Services\GestorSIGEM\AuditoriaDatasetService` |
| User / SGU | `auditoria_usuarios` / `auditoria_accesos` | `App\Models\SGU\Traits\AuditableUsuario` |
| Dictamen (SGD) | `auditoria_dictamenes` | Manual en `DictamenController` (SGD) |

Auditoría de accesos: `AuditoriaAcceso` (login/fallo/logout). Hash de IP: trait `HashIp` en `app/Traits/`.

## Seguridad transversal (NO deshabilitar)

- Subidas de archivos seguras: `app/Services/SecureFileUpload.php` + `FileContentValidator.php`.
- Saneado HTML con allowlist: `app/Services/HtmlSanitizer.php`.
- No exponer `$e->getMessage()` ni internos al cliente (deuda compartida G5/S11, ver `06`): mensaje genérico + `Log::error`.
- Config de middleware: `bootstrap/app.php`.

## Auditoría de requisitos y ciberseguridad (2026-09-14)

Revisión RF/RNF completa documentada en `Especificaciones/13_Auditoria_Cumplimiento_RF.md` (inventario + matriz RF-01..54: 52/54 implementados, RF-49 y RF-54 parciales) y `14_Auditoria_Ciberseguridad_RNF.md` (RNF + superficie de ataque + suite de pruebas de seguridad). Hallazgos A1–A17 en `06` §9 — **A1 crítico corregido el 2026-09-14** (extensión de subida derivada del tipo validado + `containsExecutableContent()` activo); también corregidos A5 (`log.404` global), A6 (throttles públicos), A8 (sanitización gestor) y A10 (`.env.example`). Verificación dinámica pendiente de `vendor/` (BD local en pausa).

## Base de datos — IMPORTANTE

- `database/migrations/` **está vacío**: el schema NO se gestiona con migraciones de Laravel.
- Schema real en SQL crudo: `archivoslocales/sql/` (activo; hoy solo `08_create_auditoria_datasets.sql` y `09_limpiar_auditoria_sgiem_dataset.sql`). Históricos: `archivoslocales/Old files/sql/` y `archivoslocales/Old files/migrations/`.
- Al modificar el schema, editar los `.sql` correspondientes en `archivoslocales/sql/` manteniendo el orden de creación. Las tablas nuevas se crean manualmente en BD (ver nota de `06` G4).
- No usar las migraciones del framework como referencia (están desactualizadas; la BD se maneja manual).

## Comandos

- `composer install` / `composer update` / `npm install`
- `php artisan serve` + `npm run dev` (Vite + Tailwind 4 — Vite actualmente retirado, ver `06` T9)
- `vendor/bin/phpunit` (PHPUnit, NO Pest; corre **sin BD**, `DB_*` comentado en `phpunit.xml`)
- `vendor/bin/pint` (formato PSR-12)

## Metodología SDD

El proyecto se trabaja **spec-driven**. Las especificaciones (`archivoslocales/Especificaciones/**`) son la fuente de verdad (modalidad *spec-anchored*, ver `11`); `Docs/` mantiene resúmenes sincronizados; `12_Requisitos_Funcionales_y_No_Funcionales.md` es el contrato de requisitos del sistema (54 RF + 35 RNF). Consultar **antes** de tocar lógica de dominio y actualizar `Docs/` tras los cambios.