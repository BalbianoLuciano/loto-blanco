# Workflow — Loto Blanco

Proceso iterativo de trabajo por fases. Adaptado del workflow original del proyecto Next.js para el stack Laravel + Inertia + Vue 3.

---

## Archivos meta (crear cuando se arranque la implementación)

Estos archivos viven en `docs/` y se mantienen actualizados sesión a sesión:

| Archivo | Propósito | Cuándo se crea |
|---|---|---|
| `docs/CURRENT_WORK.md` | Estado actual + próximo paso | Fase 0 |
| `docs/LEARNED_RULES.md` | Reglas y errores aprendidos (Laravel/Vue/Inertia/Filament/etc.) | Primer error que valga la pena documentar |
| `docs/COMPONENTS_REGISTRY.md` | Registro de componentes Vue creados | Primer componente de dominio |

---

## 1. Inicio de sesión

1. Leer `docs/CURRENT_WORK.md` → saber dónde estamos (fase + próximo paso)
2. Leer `docs/LEARNED_RULES.md` → recordar reglas del stack
3. Consultar `PLAN_V1.md` (o `PLAN_V2.md`) si hace falta contexto de la fase
4. Consultar `DESIGN_SYSTEM.md` si el trabajo es de UI
5. Continuar desde la tarea pendiente marcada 🔲

---

## 2. Ejecución por fase

### Reglas
- **Una fase a la vez.** No avanzar sin validar la actual con el usuario.
- Marcar tareas como ✅ en `CURRENT_WORK.md` al completarlas.
- Si aparece un error recurrente del stack, documentarlo en `LEARNED_RULES.md`.
- Si se crea un componente reutilizable, registrarlo en `COMPONENTS_REGISTRY.md`.
- Commits atómicos por funcionalidad; mensaje claro en ES o EN (consistencia dentro del proyecto).

### Ante errores
1. Identificar si es un patrón recurrente (Laravel/Inertia/Vue/Filament/shadcn-vue/pgvector).
2. Documentar en `LEARNED_RULES.md`: descripción, ejemplo incorrecto, ejemplo correcto, razón.
3. Continuar con la solución.

---

## 3. Fin de sesión

1. Actualizar `docs/CURRENT_WORK.md`:
   - Marcar completados con ✅
   - Agregar detalles en "Completados recientes"
   - Dejar claro el próximo paso con 🔲
2. Si hubo reglas nuevas → `docs/LEARNED_RULES.md`
3. Si hubo componentes nuevos → `docs/COMPONENTS_REGISTRY.md`
4. Confirmar estado con el usuario antes de cerrar.

---

## 4. Fases del proyecto

### V1 (ver `PLAN_V1.md` para detalle)

| Fase | Nombre | Dependencias |
|---|---|---|
| 0 | Bootstrap (Laravel + Inertia + Vue + shadcn-vue + Filament + i18n + CI) | ninguna |
| 1 | Materias + upload PDFs + RAG chat | Fase 0 |
| 2 | Ejercicios + tracker + flashcards (FSRS) | Fase 1 |
| 3 | Calendar + Google sync + achievements + push | Fase 1 |
| 4 | Polish + i18n coverage + admin | Fases 1-3 |

### V2 (ver `PLAN_V2.md` para detalle)

| Sprint | Feature |
|---|---|
| 1 | Moodle scraping |
| 2 | Lector EPUB + biblioteca unificada |
| 3 | Mock exams |
| 4 | Multi-institución real |
| 5 | BYOK |
| 6 | Analytics |
| 7 | Aulas compartidas |

### Notas
- Fases 1, 2, 3 dentro de V1 son secuenciales (2 y 3 pueden paralelizarse si hay más manos).
- V2 sprints 4, 5, 6 son independientes entre sí.
- Cualquier fase nueva descubierta durante la ejecución se agrega a `CURRENT_WORK.md` y eventualmente a los PLAN_*.md.
