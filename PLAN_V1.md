# Loto Blanco — Plan V1

Sistema unificado open-source para que alumnos universitarios gestionen todas sus materias en un solo lugar: apuntes, ejercicios adaptativos, bibliografía, calendario, chat con los materiales.

---

## Contexto

Luciano (alumno de **UTN FRRe** / TUP) tiene poquísimo tiempo y material académico disperso. Necesita un SaaS unificado, gratuito, open-source, que la comunidad estudiantil pueda mantener vía PRs. El repo actual (`Facultad/`) se vacía por completo y se empieza de cero con Laravel + Inertia + Vue 3. La marca "Loto Blanco" se mantiene con toda su estética (paleta, tipografía, logos, emblemas Agua/Fuego/Tierra, layouts).

---

## Decisiones confirmadas

| Decisión | Valor |
|---|---|
| Modelo | SaaS hospedado multi-tenant, 100% gratuito |
| Licencia | **AGPLv3** (protección contra re-hosting comercial) |
| Stack | Laravel 11 + Inertia + Vue 3 + shadcn-vue + Tailwind |
| DB | Postgres 16 + pgvector |
| Branding | Loto Blanco (paleta, tipografía, logos, emblemas, layouts existentes) |
| Gamificación | Sistema de logros basado en emblemas Agua / Fuego / Tierra |
| Spaced repetition | FSRS flashcards incluido en v1 |
| i18n | ES (default) + EN desde v1 (`laravel-vue-i18n`) |
| Admin panel | Filament desde v1 |
| MVP scope | Vertical slice end-to-end de Matemáticas |
| Moodle | Scraping con cookie de sesión cifrada (solo v2) |
| Ejercicios | RAG-first (extraer de guía) → LLM variaciones → SymPy valida |
| Institución inicial | UTN FRRe hardcodeada; `https://www.cvfrre.com.ar` |
| AI provider | **API directa de Moonshot (Kimi K2)** — free tier generoso, sin intermediarios |
| Embeddings | OpenAI `text-embedding-3-small` |
| BYOK | Descartado v1 (todo gratis, cuota compartida con rate limit) |

---

## Branding Loto Blanco

Rescatar del proyecto previo (antes de borrarlo):
- Paleta de colores (extraer de CSS tokens o imágenes)
- Tipografía (fonts y escalas)
- Logos y assets (`EmblemaAguaControl.webp`, `EmblemaFuegoControl.webp`, `EmblemaTierraControl.webp`, `PiezadelLotoBlanco.webp`)
- Estructura de layouts (capturar screenshots antes de borrar)

Los 3 emblemas se usan como **categorías de logros**:
- **Agua** → fluidez (consistencia diaria, racha de días estudiando, flashcards revisadas)
- **Fuego** → intensidad (ejercicios resueltos, mastery alcanzado, temas completados)
- **Tierra** → solidez (material procesado, biblioteca organizada, materias activas)

---

## i18n

Bilingüe ES/EN desde día uno. Razón: el usuario valora que los alumnos salgan bilingües y se familiaricen con terminología técnica en inglés.

- Librería: `laravel-vue-i18n` + locales en `resources/lang/{es,en}/`
- Selector de idioma en navbar, persiste por usuario
- Strings técnicos con traducciones paralelas ES/EN (glosario incluido)
- Contenido generado por LLM: respeta el idioma de la pregunta del user

---

## Arquitectura

### Componentes
1. **Laravel 11 monolito** — Inertia/Vue + API + jobs.
2. **Postgres 16 + pgvector** — datos + embeddings.
3. **Redis + Horizon** — colas async.
4. **Cloudflare R2** — storage S3-compat, sin egress fees.
5. **Microservicio Python (FastAPI)** — parsing de PDFs (Unstructured) + validación SymPy. Dockerizado.
6. **Moonshot Kimi K2 API** — LLM default (directo, sin OpenRouter).
7. **OpenAI embeddings** — `text-embedding-3-small`.
8. **Web Push VAPID** — notificaciones gratis (`minishlink/web-push`).
9. **Filament** — panel admin embebido.

### Hosting
- Hetzner CPX21 (~€6/mes) + Cloudflare DNS/R2.
- Docker Compose: app, postgres, redis, python-svc.

---

## Data model (core)

```
users
institutions           (UTN FRRe preloaded; URL Moodle, colores, etc.)
subjects               (materia del user: nombre, color, emblema asignado)
topics                 (unidades dentro de una materia)
documents              (PDFs subidos)
document_chunks        (texto troceado + metadata)
embeddings             (vector, HNSW index)
exercises              (source_type: extracted|generated, difficulty, statement, solution, verified_by)
exercise_attempts      (answer, correct, time_spent, feedback)
mastery                (user × topic; ease, interval, due_at — FSRS)
flashcards             (front, back, user × topic; FSRS state)
flashcard_reviews
events                 (parciales/TPs/clases/entregas; google_event_id opcional)
achievements           (def: name, element_emblema, criteria)
user_achievements      (user × achievement, unlocked_at)
streaks                (user, current_days, longest)
notifications
```

---

## Flujos V1

### 1. Onboarding
Alta → selecciona UTN FRRe → crea materia "Matemáticas" → sube PDFs → pipeline async parsea/embed/indexa → disponible.

### 2. Chat RAG
Pregunta → embed → top-k pgvector (HNSW) → prompt con citas → Kimi K2 streaming → respuesta con fuentes clickeables.

### 3. Ejercicios (flujo híbrido)
1. Extraer ejercicios de la guía oficial vía RAG al subir PDFs.
2. Presentar ordenados por dificultad estimada.
3. Cuando mastery ≥ threshold en un topic, LLM genera variaciones usando extraídos como few-shot.
4. Microservicio Python valida simbólicamente con SymPy antes de persistir.
5. Mastery adaptativo estilo FSRS actualiza dificultad siguiente.

### 4. Flashcards (spaced repetition)
- Generación: desde un chunk (highlight → flashcard) o auto-generadas por LLM desde resúmenes de temas.
- Revisión diaria: algoritmo FSRS, cola "due today" en home.
- Link bidireccional flashcard ↔ topic ↔ ejercicio.

### 5. Calendar + Google
CRUD eventos → OAuth Google → two-way sync cada 15 min → Web Push VAPID según reglas configurables.

### 6. Achievements
Eventos del sistema (ejercicio resuelto, flashcard revisada, topic completado, PDF subido, racha de N días) → evalúa criteria → unlock achievement → notificación in-app + toast con el emblema.

---

## Fases de implementación

### Fase 0 — Bootstrap (1-2 días)
- Repo limpio: `laravel new loto-blanco --inertia`
- Vue 3 + shadcn-vue + Tailwind configurado
- `docker-compose.yml`: app, postgres16+pgvector, redis, minio, python-svc
- Filament instalado para admin
- Fortify auth
- `laravel-vue-i18n` + archivos `es.json`, `en.json`
- LICENSE (AGPLv3) + README inicial
- CI GitHub Actions: Pest + Vitest
- Sentry + Uptime Kuma

### Fase 1 — Materias + RAG (semanas 1-2)
- Seed: institution UTN FRRe + materias TUP típicas
- Upload drag&drop + progress
- `ParseDocumentJob` → python-svc → chunks → `EmbedChunksJob` → pgvector
- Index HNSW
- Chat UI streaming (Laravel Reverb)
- Citas clickeables que abren PDF viewer

### Fase 2 — Ejercicios + Tracker + Flashcards (semanas 2-4)
- `ExtractExercisesJob` (RAG sobre guía)
- `GenerateExerciseVariationJob` + SymPy validator en python-svc
- Exercise player con feedback inmediato
- FSRS engine (`tusqasi/fsrs-php` o implementación propia)
- Flashcards: crear desde highlight o auto-LLM
- Dashboard mastery por topic

### Fase 3 — Calendar + Achievements + Notifications (semana 4-5)
- `Event` CRUD
- OAuth Google Calendar (Socialite) — sync bidireccional
- Sistema de achievements basado en eventos de dominio
- UI de colección de emblemas
- Web Push VAPID + reglas configurables
- Streaks de estudio diario

### Fase 4 — Polish + i18n coverage + Admin (semana 5-6)
- Traducir todas las pantallas ES/EN
- Filament: CRUD users, institutions, achievements, content moderation
- Performance: lazy loading, query optimization
- Landing page pública + documentación básica
- Beta privada con compañeros de la TUP

---

## Stack concreto

| Capa | Elección |
|---|---|
| Framework | Laravel 11 |
| Frontend | Vue 3 + Inertia + shadcn-vue + Tailwind + TypeScript |
| DB | Postgres 16 + pgvector (ext) |
| Queue | Redis + Horizon |
| Storage | Cloudflare R2 |
| Auth | Fortify + Sanctum |
| Realtime | Laravel Reverb (WebSockets self-hosted) |
| LLM | Moonshot Kimi K2 API (directa) |
| Embeddings | OpenAI `text-embedding-3-small` |
| PDF parsing | Python + Unstructured + Mathpix fallback |
| Math validation | SymPy (python-svc endpoint) |
| Calendar | Google OAuth (Socialite) |
| Push | Web Push VAPID (`minishlink/web-push`) |
| Admin | Filament |
| i18n | `laravel-vue-i18n` |
| Gamification | eventos domain-driven + achievements table |
| Spaced repetition | FSRS algorithm |
| Hosting | Hetzner CPX21 + Cloudflare |
| CI/CD | GitHub Actions → deploy por SSH |
| Monitoring | Sentry + Uptime Kuma |
| Testing | Pest + Vitest + Playwright |
| License | AGPLv3 |

---

## Seguridad

- Vars de entorno cifradas (`env:encrypt`)
- Rate limit por usuario (LLM requests, ejercicios generados/día)
- Cost cap global con circuit breaker
- Consentimiento explícito en ToS para procesamiento de PDFs
- Export completo + delete cascade por usuario (compliance)
- Los PDFs los sube el usuario (copyright responsibility del user)
- **Nunca** committear PDFs de facultad al repo público — agregar a `.gitignore`
- Ley 25.326 Argentina: revisar antes de lanzar público

---

## Costos estimados (50 users activos/mes)

| Item | Costo |
|---|---|
| Hetzner CPX21 | €6 |
| Cloudflare R2 (50GB) | $0.75 |
| Kimi K2 API | $0 (free tier) → escalable |
| OpenAI embeddings | ~$2 |
| Sentry / Uptime Kuma | Free |
| **Total** | **~€10-15/mes** |

---

## Cuentas a crear antes de empezar

1. **Hetzner Cloud** (VPS) — tarjeta + verificación
2. **Cloudflare** (DNS + R2) — gratuito para arranque
3. **Moonshot AI / Kimi** — API key
4. **OpenAI** — API key para embeddings (mínimo $5 de crédito)
5. **GitHub organization** — repo público open-source
6. **Sentry** — free tier

---

## Gaps resueltos (desde la última versión)

✅ Nombre: Loto Blanco
✅ Visual heritage: paleta/tipografía/logos/emblemas/layouts — todo
✅ Gamificación: sí, con emblemas Agua/Fuego/Tierra
✅ Spaced repetition: sí (FSRS)
✅ i18n: ES + EN desde v1
✅ BYOK: descartado v1
✅ Billing: 100% gratis
✅ Admin: Filament
✅ Institución inicial: UTN FRRe preloaded
✅ Moodle URL test: `https://www.cvfrre.com.ar`
✅ Licencia: AGPLv3
✅ Test fixtures: PDFs locales pero `.gitignore` evita publicación

---

## Dudas abiertas menores (no bloquean arranque)

1. Dominio final del proyecto (comprar cuando arranque deploy)
2. Extracción automática de paleta/tipo desde el `loto-blanco/` previo: documentar tokens antes de borrar
3. Thresholds iniciales del FSRS y del mastery adaptativo (tunear con uso real)

---

## Próximos pasos (en la nueva sesión/IDE)

1. Leer este plan + el plan V2 (`loto-blanco-v2.md`).
2. Antes de borrar `Facultad/loto-blanco/`, extraer:
   - Tokens de color del Tailwind config
   - Tipografías de `package.json` / CSS
   - Screenshots de layouts clave
   - Copiar los 4 assets de emblemas a un lugar seguro
3. Verificar que `.git/` está pusheado y que podemos dejar el repo remoto vacío.
4. `rm -rf` del contenido de `Facultad/` (conservar `.git/`) + commit "chore: reset for Laravel rewrite".
5. `laravel new .` + bootstrap según Fase 0.
6. Crear cuentas externas de la sección anterior.
7. Arrancar Fase 1 con los PDFs de Matemáticas como fixtures locales (sin committear).

---

## Verificación end-to-end (criterios de v1 done)

- [ ] Registrar cuenta nueva + seleccionar UTN FRRe
- [ ] Crear materia "Matemáticas" + asignar emblema
- [ ] Subir un PDF de la guía + esperar indexing
- [ ] Hacer pregunta en chat → respuesta con citas que abren PDF
- [ ] Generar ejercicios de un topic → resolver → SymPy valida
- [ ] Mastery avanza → sube dificultad
- [ ] Crear flashcard desde highlight → revisar al día siguiente (FSRS)
- [ ] Crear evento de parcial → sync con Google Calendar
- [ ] Recibir push notification 24h antes
- [ ] Desbloquear primer achievement de emblema Agua/Fuego/Tierra
- [ ] Cambiar idioma a EN → toda la UI traducida
- [ ] Acceder como admin a Filament + ver data
