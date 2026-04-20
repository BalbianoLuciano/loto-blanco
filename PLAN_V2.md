# Loto Blanco — Plan V2

Iteración posterior al V1. Asume que V1 está en producción y con usuarios reales dando feedback. Las features acá están priorizadas y pueden moverse según demanda.

**Prerequisito**: V1 estable, con al menos UTN FRRe operativa y 10+ usuarios activos.

---

## Objetivos V2

1. Eliminar carga manual donde se pueda (sync automático con Moodle).
2. Extender el consumo de material (lectura nativa de EPUBs + biblioteca unificada).
3. Preparar al alumno para el momento de la verdad (modo examen simulado).
4. Abrir la plataforma a otras facultades (multi-institución real).
5. Habilitar trabajo colaborativo (aulas compartidas).
6. Dar poder a usuarios avanzados (BYOK).

---

## Features V2

### Feature 1 — Moodle scraping

**Qué**: sync automático de cursos, archivos, calendario y calificaciones desde Moodle institucional.

**Decisión confirmada en V1**: cookie de sesión cifrada, nunca password persistido.

**Diseño**:
- Form: URL de Moodle + user + password → captura `MoodleSession` → cifra y guarda cookie.
- Cifrado: `crypto_aead_xchacha20poly1305` con key derivada Argon2id del password del app user + salt por registro. Key vive en memoria en sesión activa; al logout se borra y hasta próximo login no se puede descifrar.
- Scraper: Guzzle + Symfony DomCrawler. Fallback Playwright Node side-car para flujos con JS.
- Scheduler sync diario. UI de re-login cuando cookie expira (~días/semanas).
- Import de archivos dispara el mismo pipeline V1 (parse → chunk → embed → index).
- Mapping: curso Moodle ↔ materia Loto Blanco.

**Consideraciones legales**:
- ToS de UTN FRRe: revisar si scraping personal es permitido (uso del propio usuario con sus credenciales debería ser aceptable como "consumo autorizado" pero conviene check).
- Test URL confirmada: `https://www.cvfrre.com.ar` con usuario `tup26-41081561` para fixtures.

**Alcance concreto de scraping**:
- `GET /course/view.php?id={N}` → lista de secciones + recursos
- `GET /calendar/view.php` → eventos del alumno
- `GET /user/profile.php` → info + cursos matriculados
- `GET /grade/report/user/index.php?id={N}` → calificaciones
- Descarga de archivos PDF/DOCX respetando cookies

**Estimación**: 2-3 semanas.

---

### Feature 2 — Lector EPUB + biblioteca unificada

**Qué**: leer libros académicos nativamente dentro de Loto Blanco con anotaciones enlazadas al resto del sistema.

**Diseño**:
- `epub.js` embebido en componente Vue.
- Upload EPUB + metadata (ISBN, autor, título) → parser Python extrae texto completo + ToC.
- Anotaciones: highlights + notas persistidas con anchors CFI.
- Biblioteca cross-materia: un mismo libro puede asignarse a varias materias.
- Búsqueda full-text unificada:
  - Primera iteración: Postgres FTS (GIN index sobre `chunks.text`)
  - Si escala: MeiliSearch o Typesense self-hosted
- Link bidireccional: highlight → crear flashcard/ejercicio desde la selección.
- Progreso de lectura por libro (porcentaje + último CFI).

**UX**:
- Vista "Biblioteca" global con filtros (materia, autor, tag).
- Viewer con dos paneles: texto + notas del usuario + preguntas al RAG con contexto del capítulo.

**Estimación**: 2 semanas.

---

### Feature 3 — Mock exams (modo examen simulado)

**Qué**: examen cronometrado con corrección automática para prepararse para parciales.

**Diseño**:
- Generación del examen:
  - Configuración: topics incluidos, duración, cantidad de ejercicios, dificultad objetivo.
  - Source: combina ejercicios extraídos de guías + generados + los nunca vistos por el user (evita repetición).
- Entorno examen:
  - UI bloqueada (sin chat RAG, sin otras pestañas visibles).
  - Timer visible + auto-submit.
  - Anti-cheat light: blur on tab switch, warning (no bloqueante — es autoestudio).
- Corrección:
  - Ejercicios validables por SymPy: automática instantánea.
  - Ejercicios conceptuales: LLM evalúa con rúbrica + feedback.
- Resultado:
  - Score + breakdown por topic
  - Mastery update post-examen (impacto fuerte)
  - Review mode: ver cada ejercicio con solución + explicación
- Histórico de exámenes simulados + progreso a lo largo del tiempo.

**Integración con calendar**:
- Cuando hay parcial real en N días, sugiere mock exam X días antes.
- Achievement "Simulacro completado" por emblema correspondiente.

**Estimación**: 1-2 semanas.

---

### Feature 4 — Multi-institución real

**Qué**: permitir que alumnos de cualquier universidad se registren y configuren su institución.

**Diseño**:
- Seeder de instituciones (UTN todas las regionales, UBA, UNLP, etc.) con URL de Moodle, colores de marca, calendario académico oficial.
- Onboarding: flow de selección; si no está, pueden proponer nueva (admin aprueba en Filament).
- Estructura por institución: materias curriculares precargadas por carrera (opcional).
- Theme override por institución (colores secundarios respetando branding Loto Blanco primario).

**Estimación**: 1 semana.

---

### Feature 5 — Aulas compartidas (colaboración)

**Qué**: compañeros de la misma materia pueden compartir material, ejercicios y notas.

**Diseño**:
- Crear "Aula": grupo de usuarios dentro de una materia (código de invitación).
- Recursos compartidos vs privados por flag:
  - Apuntes, flashcards, anotaciones de lectura: compartibles con visibilidad granular.
  - Exercise attempts: siempre privadas (no se comparten intentos).
- Feed del aula: quién subió qué, qué tema están trabajando los demás.
- Moderación: owner del aula puede quitar usuarios; reportes → Filament.

**Alternativa más simple si la anterior pesa**:
- Solo "exportar apunte/flashcard como link compartible read-only", sin cuenta ni aulas.

**Estimación**: 2-3 semanas (full) / 3 días (alternativa simple).

---

### Feature 6 — BYOK (Bring Your Own Key)

**Qué**: permitir al usuario traer su propia API key del LLM.

**Uso**:
- User power con cuota propia de Claude/GPT/Kimi puede usarla en Loto Blanco.
- Libera cuota compartida del sistema para users casuales.
- Permite modelos más potentes que los del pool gratuito.

**Diseño**:
- Settings → "API keys" → soporta: Anthropic, OpenAI, Moonshot, OpenRouter.
- Key cifrada en DB (mismo mecanismo que Moodle cookie).
- Selector de modelo por request cuando hay key propia.
- Rate limits del pool compartido más laxos cuando el user tiene BYOK configurado.

**Estimación**: 3 días.

---

### Feature 7 — Analytics y observabilidad de uso

**Qué**: dashboards para entender cómo se usa el sistema y mejorar UX.

**Diseño**:
- PostHog self-hosted o Plausible.
- Eventos dominio: upload, chat query, exercise submit, flashcard review, mock exam.
- Dashboard Filament: retención, materia más popular, topics con más fricción.
- Opt-in explícito (por AGPLv3 + privacidad).

**Estimación**: 1 semana.

---

## Roadmap tentativo V2

| Sprint | Feature | Semanas |
|---|---|---|
| 1 | Moodle scraping | 2-3 |
| 2 | Lector EPUB + biblioteca | 2 |
| 3 | Mock exams | 1-2 |
| 4 | Multi-institución real | 1 |
| 5 | BYOK | 0.5 |
| 6 | Analytics | 1 |
| 7 | Aulas compartidas (o versión simple) | 2-3 o 0.5 |

Total V2: ~9-12 semanas según el alcance de aulas compartidas.

---

## Riesgos V2

1. **Moodle scraping frágil** — cualquier actualización de UI de Moodle rompe el scraper. Mitigar con tests contra fixture HTML y monitoreo por alertas.
2. **Copyright de EPUBs** — mismo principio que V1: usuario sube los suyos, nunca publicamos. Reforzar en ToS.
3. **Aulas compartidas + moderación** — contenido compartido requiere moderación reactiva. Empezar con la alternativa simple (share por link) antes de full aulas.
4. **Costo IA al escalar** — si usuarios crecen rápido, el pool compartido saturará cuota gratuita de Kimi. BYOK alivia pero hay que medir antes.

---

## Decisiones abiertas V2

- ¿Aulas compartidas full o versión simple? Revisar con feedback de V1.
- ¿Mantener emblemas Agua/Fuego/Tierra o expandir a más categorías en V2?
- ¿Ofrecer licencia dual (AGPLv3 + comercial) si la UTN FRRe quiere implementación privada?
- ¿API pública para que terceros extiendan Loto Blanco?

---

## Criterio de done V2

- [ ] UTN FRRe sync automático de Moodle funcional end-to-end
- [ ] Al menos 3 instituciones adicionales onboarded
- [ ] Lector EPUB con anotaciones sincronizadas a materias
- [ ] Mock exam completo con corrección automática + review
- [ ] BYOK disponible para los 4 providers principales
- [ ] 100+ usuarios activos mensuales, retención semana-2 ≥ 30%
