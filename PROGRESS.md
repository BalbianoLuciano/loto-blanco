# Loto Blanco — Progreso de implementación

Última actualización: 2026-04-21

---

## Fase 0 — Bootstrap ✅

- [x] Laravel 11 + Sail (pgsql, redis, meilisearch)
- [x] Dockerfile `pgvector/pgvector:pg16`
- [x] Breeze: Vue 3 + Inertia + TypeScript + Pest
- [x] Tailwind CSS v4 (config via CSS, sin tailwind.config.js)
- [x] shadcn-vue (15 componentes: accordion, avatar, badge, button, card, checkbox, dialog, dropdown-menu, input, label, separator, sheet, switch, tabs, tooltip)
- [x] PHP deps: Horizon, Reverb, Socialite, web-push, log-viewer, Filament
- [x] NPM deps: Geist fonts, lucide-vue-next, laravel-vue-i18n, motion-v, tw-animate-css, @vueuse/core, clsx, tailwind-merge, class-variance-authority, radix-vue
- [x] Design system: tokens OKLCH en `resources/css/app.css` (light + dark + 5 element themes)
- [x] `useElementTheme` composable + `ElementThemeProvider.vue`
- [x] Dark mode: `useDarkMode` composable con `useDark()` de vueuse
- [x] i18n: `laravel-vue-i18n` + `lang/es.json` + `lang/en.json`
- [x] LICENSE AGPLv3
- [x] Design assets en `public/images/emblems/` + `public/images/branding/`
- [x] GitHub repo: `BalbianoLuciano/loto-blanco` (público, pusheado)
- [ ] CI GitHub Actions (Pest + Vitest)
- [ ] Sentry + Uptime Kuma

---

## Fase 1 — Materias + RAG ✅ (parcial)

### Hecho
- [x] Migrations: institutions, users profile, subjects, topics, documents, document_chunks, embeddings (pgvector + HNSW), chat_messages, subject_templates
- [x] Models: Institution, User (updated), Subject, Topic, Document, DocumentChunk, Embedding, ChatMessage
- [x] Seeders: UTN FRRe + 6 templates TUP + user test con 4 materias
- [x] SubjectController CRUD + 4 páginas Vue (Index, Create, Show, Edit)
- [x] DocumentController: upload PDF multifile
- [x] `ParseDocumentJob` → smalot/pdfparser fallback local (python-svc ready)
- [x] `EmbedChunksJob` → OpenAI text-embedding-3-small → pgvector
- [x] `ExtractExercisesJob` encadenado automáticamente post-embedding
- [x] ChatController + ChatService: embed query → pgvector cosine top-5 → Kimi K2
- [x] Chat UI (Pages/Chat/Show.vue) con message history y citation badges
- [x] Config: `services.php` con openai, moonshot, python_svc

### Pendiente
- [ ] Citas clickeables que abren PDF viewer
- [ ] Streaming real (Laravel Reverb) — actualmente es request/response
- [ ] **API keys no configuradas** — el pipeline no se puede probar end-to-end aún

---

## Fase 2 — Ejercicios + Tracker + Flashcards ✅ (parcial)

### Hecho
- [x] Migrations: exercises, exercise_attempts, mastery, flashcards, flashcard_reviews
- [x] Models: Exercise, ExerciseAttempt, Mastery, Flashcard, FlashcardReview
- [x] Relaciones agregadas a User, Subject, Topic
- [x] `FsrsService` — FSRS-5 completo (schedule new/review, stability, difficulty, intervals)
- [x] `ExtractExercisesJob` — LLM extrae ejercicios de PDFs automáticamente
- [x] ExerciseController (index, show, attempt) + mastery update en cada intento
- [x] Pages/Exercises/Index.vue (lista con dificultad, estado, topic)
- [x] Pages/Exercises/Show.vue (player con enunciado, pista, respuesta, feedback, historial)
- [x] FlashcardController (store, review queue, submitReview con FSRS)
- [x] Pages/Flashcards/Review.vue (flip card, 4 ratings: Again/Hard/Good/Easy, progress bar)
- [x] Subject/Show.vue actualizado con tabs: Documents, Exercises, Mastery, Chat
- [x] Mastery dashboard por topic con progress bars
- [x] Dashboard.vue con due flashcards reales

### Pendiente
- [ ] `GenerateExerciseVariationJob` (LLM genera variaciones few-shot)
- [ ] SymPy validator en microservicio Python
- [ ] Flashcards desde highlight (seleccionar texto de chunk → crear flashcard)
- [ ] Flashcards auto-generadas por LLM desde resúmenes de topics

---

## UX / Design System ✅

- [x] Welcome page con branding Loto Blanco (logo, emblemas, CTAs)
- [x] Todas las auth pages migradas a shadcn-vue (Login, Register, ForgotPassword, ResetPassword, ConfirmPassword, VerifyEmail)
- [x] Todas las profile pages migradas a shadcn-vue (Edit, UpdateProfile, UpdatePassword, DeleteUser)
- [x] Dashboard hub con greeting contextual + stats + subjects grid
- [x] AuthenticatedLayout: ElementThemeProvider integrado, navbar con logo/nav/dark mode/idioma/avatar/mobile sheet
- [x] GuestLayout: Card + dark mode toggle + branding
- [x] Element themes completos (foreground, card-foreground, border, input en cada tema)
- [x] 0 componentes Breeze restantes — todo shadcn-vue
- [ ] Selector de idioma funcional (necesita endpoint backend para cambiar locale)

---

## Fase 3 — Calendar + Achievements + Notifications ❌

Todo pendiente:
- [ ] `Event` CRUD
- [ ] OAuth Google Calendar (Socialite) — sync bidireccional
- [ ] Sistema de achievements (eventos de dominio → unlock → notificación)
- [ ] UI de colección de emblemas
- [ ] Web Push VAPID (`minishlink/web-push`) + reglas configurables
- [ ] Streaks de estudio diario
- [ ] Migrations: events, achievements, user_achievements, streaks, notifications

---

## Fase 4 — Polish + i18n + Admin ❌

Todo pendiente:
- [ ] Traducir todas las pantallas ES/EN
- [ ] Filament admin: CRUD users, institutions, achievements, content moderation
- [ ] Performance: lazy loading, query optimization
- [ ] Landing page pública responsive
- [ ] CI GitHub Actions (Pest + Vitest)
- [ ] Sentry + Uptime Kuma
- [ ] Beta privada

---

## Próximo paso inmediato

### 1. Configurar API keys + pipeline de fallback multi-provider

Se decidió usar múltiples providers gratuitos con fallback automático:

**LLM Pipeline** (si uno llega al límite, pasa al siguiente):
```
Google Gemini → Groq → Mistral → Moonshot
```

**Embedding Pipeline**:
```
Google Gemini → Jina AI → Mistral
```

**Cuentas a crear** (todas gratis, sin tarjeta):

| Provider | URL | Para qué |
|---|---|---|
| Google AI Studio | https://aistudio.google.com/ | LLM + Embeddings |
| Groq | https://console.groq.com/ | LLM fallback |
| Mistral | https://console.mistral.ai/ | LLM + Embeddings fallback |
| Moonshot (Kimi) | https://platform.moonshot.cn/ | LLM fallback |
| Jina AI | https://jina.ai/api/ | Embeddings fallback |

**Agregar al `.env`:**
```
GEMINI_API_KEY=...
GROQ_API_KEY=...
MISTRAL_API_KEY=...
MOONSHOT_API_KEY=...
JINA_API_KEY=...
```

### 2. Construir los servicios de fallback
- `App\Services\LlmPipeline` — tryGemini → tryGroq → tryMistral → tryMoonshot
- `App\Services\EmbeddingPipeline` — tryGemini → tryJina → tryMistral
- Actualizar `ChatService`, `ExtractExercisesJob`, `EmbedChunksJob` para usar los pipelines

### 3. Probar el flujo end-to-end con PDFs de Matemáticas
- Upload PDF → parse → embed → extraer ejercicios → chat RAG → flashcards

---

## Datos de prueba

- **URL**: http://localhost (Sail debe estar corriendo: `./vendor/bin/sail up -d`)
- **Vite**: `npm run dev -- --port 5174 --host`
- **Login**: `test@example.com` / `password`
- **User tiene**: 4 materias (Matemáticas, Programación 1, Arquitectura, Organización)
- **PDFs de test**: carpetas locales `Matematicas/`, `Programacion1/` (gitignored)
