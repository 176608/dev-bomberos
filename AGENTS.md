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

## Documentación — jerarquía de fuentes

Prioridad de consulta antes de tocar cualquier lógica de dominio (orden decreciente):

1. **Especificaciones — fuente única de verdad:** `archivoslocales/Especificaciones/` (actualizado y verificado contra el código). **Prioridad SIEMPRE sobre `documentacion tecnica`.**
2. **Contexto para agentes:** `Docs/` en la raíz del repo. Resúmenes concisos por módulo (`Docs/README.md`, `Docs/SistemaAux.md`, `Docs/SGIEM.md`, `Docs/SIGEM.md`, `Docs/SGU.md`, `Docs/SGD.md`, `Docs/Bomberos.md`). Puerta de entrada recomendada; se mantienen sincronizados con el código (ver `11` — modalidad *spec-anchored*).
3. **Legacy (NO usar como fuente):** `archivoslocales/documentacion tecnica/`. Solo referencia histórica: `Manual_SGD.md`, `Requisitos Funcionales y No funcionales_revisada.xlsx` (obsoleto → versión MD en Especificaciones) y versiones antiguas de los docs 01–06.
4. **Históricos:** `archivoslocales/Old files/` (SQL/migraciones viejos, no activos).

Directorio **local** de trabajo: `archivoslocales/` (especificaciones, SQL crudo del schema, históricos). Directorio de **contexto para agentes**: `Docs/`.

Archivos críticos de Especificaciones:

- `12_Requisitos_Funcionales_y_No_Funcionales.md` — **documento fundamental y orientativo** del sistema: 54 RF (RF-01 a RF-54) y 35 RNF (RNF-01 a RNF-35). Leer antes de cambios de dominio.
- `01`…`05` — docs técnicos SGIEM, SIGEM, SGU, análisis holístico y modelo C4.
- `06_Listado_Bugs.md` — bugs documentados y decisiones del equipo (consultar antes de tocar áreas ya analizadas; respetar estados Aceptado/Pendiente).
- `10_Mapa_E_R.md` — entidad-relación del dataset y métricas.
- `11_Spec_Driven_Development_Metodologia.md` — metodología SDD del proyecto.

## Flujo de trabajo SDD (recomendado)

Trabajar en modalidad **spec-driven** con los distintos modelos que ofrece la plataforma opencode (planificador/explorador/implementador/revisor):

- **Especificar antes de codificar:** consulta `archivoslocales/Especificaciones/` (y el resumen en `Docs/`) antes de modificar lógica de dominio; valida contra `12_Requisitos_Funcionales_y_No_Funcionales.md`.
- **No dejar que el código derive de la especificación:** al terminar un cambio, actualiza los resúmenes afectados de `Docs/` (modalidad *spec-anchored*, ver `11`). La verificación falla si derivan.
- Usa los modelos/skills de opencode más adecuados a la fase: exploración, planificación, implementación o revisión.