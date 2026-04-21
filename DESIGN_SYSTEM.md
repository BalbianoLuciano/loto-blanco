# Loto Blanco — Sistema de Diseño

Documentación completa del sistema de diseño rescatado de la versión Next.js previa (`loto-blanco/`) antes de su eliminación. Esta es la **fuente de verdad visual** para la reimplementación en Laravel + Inertia + Vue 3 + shadcn-vue.

---

## Filosofía visual

**Concepto**: El "Loto Blanco" es una flor que se abre pétalo a pétalo — cada pétalo representa un área del saber (una materia). El sistema asigna un **elemento** a cada materia, con su propia paleta cromática que se activa al navegar dentro de ella. El estado neutral (lotus) es el de la plataforma sin contexto específico.

Inspiración: estética minimalista asiática + Avatar (los 4 elementos + loto).

---

## Elementos → Materias

El sistema tiene **5 elementos** (no 3 como se mencionaba inicialmente). Se aplican como tema vía atributo HTML `data-element="<name>"` en un wrapper.

| Elemento | Materia actual | Uso simbólico |
|---|---|---|
| `water` | Matemáticas | Fluidez, abstracción, lógica |
| `fire` | Programación 1 | Intensidad, ejecución, energía |
| `earth` | Arquitectura y Sistemas de Gestión | Solidez, estructura, fundaciones |
| `air` | Organización Empresarial | Movimiento, comunicación, estrategia |
| `lotus` | Plataforma (default) | Neutralidad, equilibrio, contexto global |

**Sistema de logros** (del plan): los emblemas se siguen usando como categorías de achievements. Posibilidades:
- 3 categorías originales (Agua/Fuego/Tierra) como el user confirmó, O
- 5 categorías alineadas al sistema completo. **Decidir en Fase 0** si expandir a 5 o mantener 3.

---

## Color tokens

Todos los colores están en **OKLCH** (perceptualmente uniforme, mejor para gradientes y accesibilidad). Son CSS custom properties nativas — **Tailwind v4 las consume directamente** sin necesidad de `tailwind.config.js`. Se declaran en `:root`/`.dark` y los element themes las sobreescriben via `[data-element]`.

### Tokens base (tema global)

Modo claro (`:root`):
```css
--background: oklch(1 0 0);
--foreground: oklch(0.145 0 0);
--card: oklch(1 0 0);
--card-foreground: oklch(0.145 0 0);
--popover: oklch(1 0 0);
--popover-foreground: oklch(0.145 0 0);
--primary: oklch(0.205 0 0);
--primary-foreground: oklch(0.985 0 0);
--secondary: oklch(0.97 0 0);
--secondary-foreground: oklch(0.205 0 0);
--muted: oklch(0.97 0 0);
--muted-foreground: oklch(0.556 0 0);
--accent: oklch(0.97 0 0);
--accent-foreground: oklch(0.205 0 0);
--destructive: oklch(0.577 0.245 27.325);
--border: oklch(0.922 0 0);
--input: oklch(0.922 0 0);
--ring: oklch(0.708 0 0);
--radius: 0.625rem;

/* Charts */
--chart-1: oklch(0.87 0 0);
--chart-2: oklch(0.556 0 0);
--chart-3: oklch(0.439 0 0);
--chart-4: oklch(0.371 0 0);
--chart-5: oklch(0.269 0 0);

/* Sidebar */
--sidebar: oklch(0.985 0 0);
--sidebar-foreground: oklch(0.145 0 0);
--sidebar-primary: oklch(0.205 0 0);
--sidebar-primary-foreground: oklch(0.985 0 0);
--sidebar-accent: oklch(0.97 0 0);
--sidebar-accent-foreground: oklch(0.205 0 0);
--sidebar-border: oklch(0.922 0 0);
--sidebar-ring: oklch(0.708 0 0);
```

Modo oscuro (`.dark`):
```css
--background: oklch(0.145 0 0);
--foreground: oklch(0.985 0 0);
--card: oklch(0.205 0 0);
--card-foreground: oklch(0.985 0 0);
--popover: oklch(0.205 0 0);
--popover-foreground: oklch(0.985 0 0);
--primary: oklch(0.922 0 0);
--primary-foreground: oklch(0.205 0 0);
--secondary: oklch(0.269 0 0);
--secondary-foreground: oklch(0.985 0 0);
--muted: oklch(0.269 0 0);
--muted-foreground: oklch(0.708 0 0);
--accent: oklch(0.269 0 0);
--accent-foreground: oklch(0.985 0 0);
--destructive: oklch(0.704 0.191 22.216);
--border: oklch(1 0 0 / 10%);
--input: oklch(1 0 0 / 15%);
--ring: oklch(0.556 0 0);
--sidebar: oklch(0.205 0 0);
--sidebar-foreground: oklch(0.985 0 0);
--sidebar-primary: oklch(0.488 0.243 264.376);
--sidebar-primary-foreground: oklch(0.985 0 0);
--sidebar-accent: oklch(0.269 0 0);
--sidebar-accent-foreground: oklch(0.985 0 0);
--sidebar-border: oklch(1 0 0 / 10%);
--sidebar-ring: oklch(0.556 0 0);
```

### Element themes (sobreescriben tokens base)

#### Water — Matemáticas
```css
[data-element="water"] {
  --primary: oklch(0.35 0.1 250);
  --primary-foreground: oklch(0.98 0 0);
  --secondary: oklch(0.55 0.15 250);
  --secondary-foreground: oklch(0.98 0 0);
  --accent: oklch(0.78 0.1 240);
  --accent-foreground: oklch(0.2 0.05 250);
  --background: oklch(0.97 0.01 240);
  --card: oklch(0.99 0.005 240);
  --muted: oklch(0.93 0.02 240);
  --muted-foreground: oklch(0.45 0.05 250);
  --ring: oklch(0.55 0.15 250);
}
```

#### Fire — Programación 1
```css
[data-element="fire"] {
  --primary: oklch(0.4 0.15 25);
  --primary-foreground: oklch(0.98 0 0);
  --secondary: oklch(0.6 0.18 40);
  --secondary-foreground: oklch(0.98 0 0);
  --accent: oklch(0.8 0.12 70);
  --accent-foreground: oklch(0.25 0.08 25);
  --background: oklch(0.97 0.01 40);
  --card: oklch(0.99 0.005 35);
  --muted: oklch(0.93 0.02 40);
  --muted-foreground: oklch(0.45 0.05 25);
  --ring: oklch(0.6 0.18 40);
}
```

#### Earth — Arquitectura y Sistemas
```css
[data-element="earth"] {
  --primary: oklch(0.4 0.12 140);
  --primary-foreground: oklch(0.98 0 0);
  --secondary: oklch(0.55 0.1 140);
  --secondary-foreground: oklch(0.98 0 0);
  --accent: oklch(0.7 0.08 85);
  --accent-foreground: oklch(0.25 0.06 140);
  --background: oklch(0.97 0.01 90);
  --card: oklch(0.99 0.005 90);
  --muted: oklch(0.93 0.02 100);
  --muted-foreground: oklch(0.45 0.05 140);
  --ring: oklch(0.55 0.1 140);
}
```

#### Air — Organización Empresarial
```css
[data-element="air"] {
  --primary: oklch(0.65 0.12 80);
  --primary-foreground: oklch(0.15 0.03 60);
  --secondary: oklch(0.6 0.06 60);
  --secondary-foreground: oklch(0.15 0.03 60);
  --accent: oklch(0.85 0.05 80);
  --accent-foreground: oklch(0.3 0.04 60);
  --background: oklch(0.98 0.005 80);
  --card: oklch(0.99 0.003 80);
  --muted: oklch(0.94 0.01 80);
  --muted-foreground: oklch(0.45 0.04 60);
  --ring: oklch(0.65 0.12 80);
}
```

#### Lotus — Plataforma / default
```css
[data-element="lotus"] {
  --primary: oklch(0.5 0.06 60);
  --primary-foreground: oklch(0.98 0 0);
  --secondary: oklch(0.7 0.08 80);
  --secondary-foreground: oklch(0.2 0.03 60);
  --accent: oklch(0.82 0.06 85);
  --accent-foreground: oklch(0.3 0.04 60);
  --background: oklch(0.97 0.005 70);
  --card: oklch(0.99 0.003 70);
  --muted: oklch(0.93 0.01 70);
  --muted-foreground: oklch(0.5 0.03 60);
  --ring: oklch(0.5 0.06 60);
}
```

---

## Tipografía

- **Sans**: [Geist](https://vercel.com/font) (variable `--font-geist-sans` / `--font-sans`)
- **Mono**: Geist Mono (variable `--font-geist-mono`)
- **Heading**: usa la sans por defecto (`--font-heading: var(--font-sans)`)

En Next.js se cargaba con `next/font/google`. En Vue/Laravel cargar via `@fontsource-variable/geist` + `@fontsource-variable/geist-mono` (npm) o auto-host desde Google Fonts en el HTML layout.

Aplicado globalmente con:
```css
html { @apply font-sans; }
```

---

## Radius scale

Base: `--radius: 0.625rem` (10px). Derivado:

| Token | Fórmula | Valor aprox |
|---|---|---|
| `--radius-sm` | `0.6 × 0.625rem` | 0.375rem (6px) |
| `--radius-md` | `0.8 × 0.625rem` | 0.5rem (8px) |
| `--radius-lg` | `1.0 × 0.625rem` | 0.625rem (10px) |
| `--radius-xl` | `1.4 × 0.625rem` | 0.875rem (14px) |
| `--radius-2xl` | `1.8 × 0.625rem` | 1.125rem (18px) |
| `--radius-3xl` | `2.2 × 0.625rem` | 1.375rem (22px) |
| `--radius-4xl` | `2.6 × 0.625rem` | 1.625rem (26px) |

---

## Spacing

Stock Tailwind v4 (no override custom). Usar tokens estándar (`gap-1` → `gap-24`, `p-*`, `m-*`).

---

## shadcn config

- **Style**: `base-nova`
- **Base color**: `neutral`
- **Icon library**: `lucide`
- **CSS Variables**: sí (habilitado)
- **Prefix**: ninguno
- **RSC**: sí (descartado en migración a Vue)

Aliases (adaptar a Vue/Inertia):
```
components → resources/js/components
ui → resources/js/components/ui
utils → resources/js/lib/utils
lib → resources/js/lib
hooks → resources/js/composables  (renombrar de hooks → composables)
```

---

## Inventario de componentes UI (shadcn)

Base disponibles que ya estaban portados (cada uno requiere re-crear en **shadcn-vue** con los mismos tokens):

- `accordion`
- `avatar`
- `badge`
- `button`
- `card`
- `dialog`
- `dropdown-menu`
- `separator`
- `sheet`
- `tabs`
- `tooltip`

Instalar con:
```bash
npx shadcn-vue@latest add accordion avatar badge button card dialog dropdown-menu separator sheet tabs tooltip
```

---

## Componentes de dominio (para referenciar al reimplementar)

Directorios que existían y habrá que re-crear en Vue:

| Directorio | Propósito estimado |
|---|---|
| `calendar/` | Vista de calendario (antes FullCalendar React). En Vue usar `@fullcalendar/vue3`. |
| `epub/` | Reader de libros (epub.js). El package es framework-agnostic, integración directa en Vue. |
| `exercises/` | Lista + player de ejercicios. |
| `forum/` | Comentarios/foro (si se mantiene — reconsiderar en v2 como "aulas compartidas"). |
| `layout/` | Shell principal (sidebar + navbar + content). |
| `pomodoro/` | Timer de estudio (no estaba en planes, **decidir si se rescata**). |
| `theme/` | Provider de tema elemental. Ver abajo. |
| `theory/` | Render de teoría MDX/markdown. Para Vue usar `@nuxtjs/mdc` o markdown-it. |
| `ui/` | shadcn base. |

---

## Provider de tema elemental

Lógica original (React):
```tsx
<ElementThemeProvider defaultElement="lotus">
  <div data-element={element} className="min-h-screen transition-colors duration-300">
    {children}
  </div>
</ElementThemeProvider>
```

Port a Vue 3 (composable + provide/inject):
```ts
// resources/js/composables/useElementTheme.ts
import { ref, provide, inject, type Ref } from 'vue'

export type Element = 'water' | 'fire' | 'earth' | 'air' | 'lotus'
const KEY = Symbol('elementTheme')

export function provideElementTheme(defaultElement: Element = 'lotus') {
  const element = ref<Element>(defaultElement)
  provide(KEY, element)
  return element
}

export function useElementTheme(): Ref<Element> {
  const el = inject<Ref<Element>>(KEY)
  if (!el) throw new Error('useElementTheme without provider')
  return el
}
```

Wrapper template:
```vue
<template>
  <div :data-element="element" class="min-h-screen transition-colors duration-300">
    <slot />
  </div>
</template>
```

---

## Dark mode

Se activa agregando `.dark` a un ancestor (Tailwind v4 variant `@custom-variant dark (&:is(.dark *))`).

Toggle recomendado: `@vueuse/core` → `useDark()` con persistencia en localStorage.

---

## Animaciones

Dependencias del proyecto original:
- `tw-animate-css` — utilidades Tailwind para animaciones
- `framer-motion` v12 — para animaciones complejas

Para Vue, equivalentes:
- `tw-animate-css` funciona igual (es Tailwind puro, no JS)
- Reemplazar `framer-motion` → **`motion-v`** (Motion for Vue, misma API) o `@vueuse/motion`

---

## Iconografía

**Lucide** via `lucide-vue-next`:
```bash
npm install lucide-vue-next
```

Uso:
```vue
<script setup>import { Flame, Droplet, Leaf, Wind, Flower } from 'lucide-vue-next'</script>
<template><Flame class="w-5 h-5" /></template>
```

Sugeridos por elemento:
- water → `Droplet` o `Waves`
- fire → `Flame`
- earth → `Mountain` o `Leaf`
- air → `Wind`
- lotus → `Flower` o `Flower2`

---

## Assets (archivados en `design-assets/`)

### Emblemas por elemento (optimizados web)
`design-assets/emblems/`:
- `water.webp` — agua
- `fire.webp` — fuego
- `earth.webp` — tierra
- `lotus.webp` — loto (default)
- **Falta `air.webp`** — el tema CSS existe pero no había emblema; crear o encargar
- `four-elements.jpg` — composición de los 4 elementos juntos

### Emblemas control originales (alta resolución, masters)
`design-assets/branding/`:
- `EmblemaAguaControl.webp`
- `EmblemaFuegoControl.webp`
- `EmblemaTierraControl.webp`
- `PiezadelLotoBlanco.webp`

### Misc
`design-assets/misc/`:
- `67995611-4ebd-42d7-a460-a40260f34771_600x600.jpg` — thumbnail sin identificar

---

## Guía de migración a Vue/Inertia (con Tailwind v4 + Sail)

### 1. Setup inicial (via Laravel Sail)

Breeze ya instala Vue 3, Inertia, TypeScript y Tailwind CSS v4. Dependencias adicionales:

```bash
sail npm install tw-animate-css motion-v lucide-vue-next
sail npm install @fontsource-variable/geist @fontsource-variable/geist-mono
sail npm install laravel-vue-i18n @vueuse/core
```

### 2. Tailwind v4 — configuración via CSS (NO tailwind.config.js)

Tailwind v4 elimina el archivo `tailwind.config.js`. Todo se configura en CSS con `@theme`:

```css
/* resources/css/app.css */
@import 'tailwindcss';
@import '@fontsource-variable/geist';
@import '@fontsource-variable/geist-mono';

@custom-variant dark (&:is(.dark *));

@theme inline {
  --font-sans: 'Geist Variable', ui-sans-serif, system-ui, sans-serif;
  --font-mono: 'Geist Mono Variable', ui-monospace, monospace;
  --radius-sm: 0.375rem;
  --radius-md: 0.5rem;
  --radius-lg: 0.625rem;
  --radius-xl: 0.875rem;
}
```

Los color tokens se definen como CSS custom properties normales (`:root` y `.dark`) y se referencian en las clases via `@theme inline` o directamente en los componentes shadcn-vue.

### 3. Copiar tokens de color
Mover todos los tokens OKLCH (sección "Color tokens" de este doc) a `resources/css/app.css` dentro de `:root` y `.dark`. Los element themes van como `[data-element="water"]` etc.

### 4. Instalar shadcn-vue
```bash
sail npx shadcn-vue@latest init
# Confirmar: neutral base color, CSS variables, alias como los listados arriba
```

### 5. Crear provider elemental
Crear `resources/js/composables/useElementTheme.ts` como se mostró arriba.
Crear `resources/js/components/theme/ElementThemeProvider.vue` como wrapper.

### 6. Layouts
Envolver `AppLayout.vue` principal con `<ElementThemeProvider>`. Lo setea según la materia activa en la ruta.

### 7. Fuentes
Ya incluidas en el `@import` del paso 2. Las variables `--font-sans` y `--font-mono` se registran en `@theme inline` para que Tailwind las use con `font-sans` y `font-mono`.

---

## Gaps / preguntas abiertas

1. **Emblema de `air` falta**: hay tema CSS pero no imagen — diseñar o encargar antes de poner viento como opción seleccionable.
2. **Pomodoro**: había directorio en el código original pero no está en los planes V1/V2 — decidir si se mantiene como feature.
3. **Forum**: directorio existe pero la feature equivalente en planes es "Aulas compartidas" (V2). Reutilizar concepto o reescribir.
4. **5 emblemas como categorías de achievements**: los planes mencionan 3 (Agua/Fuego/Tierra); el sistema real tiene 5 (agregar air + lotus, o aclarar que los logros solo usan los 3 con emblema). Recomendado: 4 categorías (water/fire/earth/air) + lotus como categoría especial "platform milestones".
5. **`base-nova` style de shadcn**: verificar que shadcn-vue soporte este style; si no, caer a `default` o `new-york` y ajustar manualmente.

---

## Fuentes originales (referencia, repo borrado)

- `loto-blanco/src/app/globals.css` → todos los tokens de color y radius
- `loto-blanco/src/app/layout.tsx` → setup de fuentes
- `loto-blanco/src/components/theme/ElementThemeProvider.tsx` → provider
- `loto-blanco/components.json` → shadcn config
- `loto-blanco/package.json` → stack de deps (referencia para port)
