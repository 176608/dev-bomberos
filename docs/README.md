# Docs — Contexto para Agentes del Sistema Auxiliar

Este directorio es la **capa de contexto para agentes** (humanos o IA) que manipulan el código de este repositorio. Aquí hay resúmenes concisos y accionables por módulo, sincronizados con el código en modalidad ***spec-anchored*** (ver `archivoslocales/Especificaciones/11_Spec_Driven_Development_Metodologia.md`).

> **Fuente única de verdad:** `archivoslocales/Especificaciones/` (actualizado y verificado contra el código). Este directorio **no** sustituye a las especificaciones: es la puerta de entrada y el mapa rápido. Si un documento de `Docs/` y el código divergen, actualizar `Docs/` para reflejar el código (y revisar la especificación).

## Jerarquía de fuentes (prioridad)

1. **`archivoslocales/Especificaciones/`** — fuentes de verdad, actualizadas. **Prioridad máxima.**
2. **`Docs/`** — resúmenes de contexto por módulo (este directorio).
3. **`archivoslocales/documentacion tecnica/`** — **legacy**. No usar como fuente; solo referencia histórica (p. ej. `Manual_SGD.md`).
4. **`archivoslocales/Old files/`** — versiones históricas (SQL y migraciones viejas). No tocar como activo.

Archivo fundamental del sistema: `archivoslocales/Especificaciones/12_Requisitos_Funcionales_y_No_Funcionales.md` (54 RF + 35 RNF). Revisarlo antes de cualquier cambio de dominio.

## Mapa de módulos → documentos

| Módulo | Prefijo/ruta | Docs | Especificación fuente |
|---|---|---|---|
| Todo el sistema (holístico) | — | `SistemaAux.md` | 04 (holístico), 05 (C4), 07 (flujo/mapa), 10 (E-R), 11 (SDD), 12 (RF/RNF) |
| SGIEM (gestor admin v2) | `/sgiem/admin` | `SGIEM.md` | 01, 05 (sec 3.2), 06 (G1–G10), 12 (RF-33..46) |
| SIGEM (visor público) | `/sigem-v2` (+ v1 legacy `/sigem`) | `SIGEM.md` | 02, 04, 06 (V1–V6), 12 (RF-15..32) |
| SGU (usuarios y métricas) | `/sgu/admin` | `SGU.md` | 03, 05 (sec 4.3), 06 (S1–S12), 12 (RF-47..54) |
| SGD (gestión de dictámenes) | `/admin/GestorDictamenes` + `/VisorDictamenes` | `SGD.md` | `documentacion tecnica/Manual_SGD.md` (legacy), código |
| Bomberos (auth global + hidrantes) | `/login`, paneles por rol, `/consultor` | `Bomberos.md` | 03 (sec. auth), código |
| Biblioteca (catálogo) | `/biblioteca`, `/search` | (resumen en `SistemaAux.md`) | código |

## Cómo usar estos documentos

1. Antes de tocar lógica de dominio, leer el `Docs/*.md` del módulo afectado.
2. Consultar la especificación correspondiente en `archivoslocales/Especificaciones/` para el detalle (flujos, diagramas, decisiones).
3. Verificar contra `12_Requisitos_Funcionales_y_No_Funcionales.md` si el cambio afecta un requisito.
4. Consultar `06_Listado_Bugs.md` antes de trabajar en áreas ya analizadas (decisiones del equipo).
5. Al terminar el cambio, actualizar el resumen de `Docs/` si el comportamiento del código cambió (modalidad *spec-anchored*).