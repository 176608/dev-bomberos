# SistemaAuxiliar

Sistema Laravel 12 (PHP >= 8.2) modular de estadísticas oficiales y módulos auxiliares (SIGEM/SGIEM, SGU, dictámenes, bomberos, biblioteca). Todo el dominio y la UI están en **español**.

## Comandos

- Instalar/actualizar PHP: `composer install` / `composer update`; frontend: `npm install`.
- Servidor local: `php artisan serve` + `npm run dev` (Vite + Tailwind 4).
- Build frontend: `npm run build`.
- Tests: `vendor/bin/phpunit` (PHPUnit, NO Pest; suites `Unit` y `Feature` en `phpunit.xml`). Ojo: los tests corren **sin base de datos** (env `DB_*` comentado en `phpunit.xml`).
- Formato PHP: `vendor/bin/pint` (laravel/pint, PSR-12).

## Arquitectura multi-módulo

`routes/web.php` carga un archivo de rutas por módulo. Cada módulo agrupa controladores, modelos (namespaced) y algunos servicios.

| Módulo | Prefijo de rutas | Descripción |
|---|---|---|
| SIGEM | `sigem` | Estadísticas v1 (público + admin) |
| VisorSIGEM | `sigem-v2` | Visor público v2: cuadros, datasets, mapas, exportaciones |
| GestorSIGEM | `sgiem.admin` | Backend admin v2: Tema/Subtema/Cuadro/Dataset/ConsultaExpress |
| SGU | `sgu.admin` | Gestión de usuarios y auditoría |
| GestorDictamenes | `admin/GestorDictamenes` | Backend de dictámenes: subidas, enlaces, historial |
| VisorDictamenes | ruta pública | Visor público de dictámenes |
| Bomberos | panels por rol | Registro de hidrantes (roles `Capturista`/`Registrador`) |
| Biblioteca | búsqueda con `throttle:60,1` | Catálogo de libros |

Convenciones estructurales:
- Modelos: `App\Models\<Modulo>\<Nombre>` (ej. `App\Models\SIGEM\Tema`, `App\Models\SGU\User`).
- Servicios: `app/Services/<Modulo>/` (ej. `GestorSIGEM`, `VisorSIGEM`).
- Form requests: `app/Http/Requests/` (ej. `GestorSIGEM/`).

## Roles y autorización

- Autorización por rol propia mediante middleware `CheckRole` (se usa inline como `role:...`). Roles conocidos: `Capturista`, `Registrador`, `Administrador`, `Desarrollador`, `Estadistico`, y roles de auditoría en SGU.
- Otros middleware relevantes: `LoginRateLimiter`, `SecurityHeaders`, `PreventBackHistory`, `PasswordResetRequired`, `DebugByRole`, `LogSuspicious404`, `SetVisitorUuid`.

## Auditoría

- Patrón por módulo vía traits: `App\Models\SIGEM\Traits\AuditableSgiem`, `App\Models\SGU\Traits\AuditableUsuario`.
- Modelos de auditoría: `AuditoriaSgiem`, `AuditoriaDataset`, `AuditoriaAcceso`, `AuditoriaUsuario`, `AuditoriaDictamen`; `HashIp` en `app/Traits/`.
- Respetar el patrón existente al añadir modelos auditables.

## Seguridad

- Subidas de archivos: `app/Services/SecureFileUpload.php` + `FileContentValidator.php`.
- Saneado HTML: `app/Services/HtmlSanitizer.php`.
- No deshabilitar estos controles al agregar subidas o inputs.

## Base de datos — IMPORTANTE

- `database/migrations/` está vacío: el schema **NO** se gestiona con migraciones de Laravel.
- El schema real está en SQL crudo: `archivoslocales/sql/` (activo) y versiones históricas en `archivoslocales/Old files/sql/` y `archivoslocales/Old files/migrations/`.
- Al modificar el schema, editar los `.sql` correspondientes en `archivoslocales/sql/` manteniendo el orden de creación.

## Convenciones

- UI, mensajes y dominio en **español** (títulos, vistas, rutas nombradas, mensajes).
- Indentación de 4 espacios (`.editorconfig`), sin tabs.
- No añadir comentarios en código salvo que se soliciten explícitamente.
- Seguir el estilo del módulo tocado y `vendor/bin/pint`.
- No commitear `.env` ni credenciales.

## Documentación

- Documentación técnica interna en `archivoslocales/documentacion tecnica/` (modelo C4, docs SGIEM/SIGEM/SGU, listas de bugs, análisis RF/RNF). Consultarla antes de tocar lógica de dominio.