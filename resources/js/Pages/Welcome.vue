<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { useDarkMode } from '@/composables/useDarkMode'
import { trans } from 'laravel-vue-i18n'
import { Droplet, Flame, Mountain, Wind, Flower, Sun, Moon, ArrowRight } from 'lucide-vue-next'

defineProps<{
    canLogin?: boolean
    canRegister?: boolean
}>()

const { isDark, toggleDark } = useDarkMode()

const elements = [
    { icon: Droplet, name: 'Water', label: 'Fluidez', color: 'text-blue-500' },
    { icon: Flame, name: 'Fire', label: 'Intensidad', color: 'text-red-500' },
    { icon: Mountain, name: 'Earth', label: 'Solidez', color: 'text-green-500' },
    { icon: Wind, name: 'Air', label: 'Movimiento', color: 'text-yellow-500' },
]
</script>

<template>
    <Head title="Loto Blanco" />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Top bar -->
        <nav class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2">
                <Flower class="h-6 w-6 text-primary" />
                <span class="font-semibold">Loto Blanco</span>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="ghost" size="icon" class="h-9 w-9" @click="toggleDark()">
                    <Sun v-if="isDark" class="h-4 w-4" />
                    <Moon v-else class="h-4 w-4" />
                </Button>
                <template v-if="canLogin">
                    <Link :href="route('login')">
                        <Button variant="ghost" size="sm">{{ trans('Log in') }}</Button>
                    </Link>
                    <Link v-if="canRegister" :href="route('register')">
                        <Button size="sm">{{ trans('Register') }}</Button>
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Hero -->
        <main class="mx-auto max-w-4xl px-6">
            <div class="flex flex-col items-center pt-20 pb-16 text-center">
                <div class="mb-8">
                    <img
                        src="/images/branding/PiezadelLotoBlanco.webp"
                        alt="Loto Blanco"
                        class="mx-auto h-32 w-32 object-contain"
                    />
                </div>

                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                    Tu estudio,
                    <span class="text-primary">unificado</span>
                </h1>

                <p class="mt-4 max-w-xl text-lg text-muted-foreground">
                    Apuntes, ejercicios adaptativos, flashcards con repetición espaciada,
                    calendario y chat con tus materiales — todo en un solo lugar.
                </p>

                <div class="mt-8 flex gap-3">
                    <Link v-if="canRegister" :href="route('register')">
                        <Button size="lg">
                            Empezar gratis
                            <ArrowRight class="ml-2 h-4 w-4" />
                        </Button>
                    </Link>
                    <Link v-if="canLogin" :href="route('login')">
                        <Button variant="outline" size="lg">
                            {{ trans('Log in') }}
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Elements showcase -->
            <div class="py-16">
                <h2 class="mb-8 text-center text-sm font-medium uppercase tracking-wider text-muted-foreground">
                    Un elemento para cada materia
                </h2>
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                    <div
                        v-for="el in elements"
                        :key="el.name"
                        class="flex flex-col items-center gap-3 rounded-xl border border-border bg-card p-6 transition-colors hover:bg-accent"
                    >
                        <component :is="el.icon" :class="['h-8 w-8', el.color]" />
                        <div class="text-center">
                            <p class="font-medium">{{ trans(el.name) }}</p>
                            <p class="text-xs text-muted-foreground">{{ el.label }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emblems row -->
            <div class="flex justify-center gap-8 py-8 opacity-60">
                <img src="/images/emblems/water.webp" alt="Agua" class="h-16 w-16 object-contain" />
                <img src="/images/emblems/fire.webp" alt="Fuego" class="h-16 w-16 object-contain" />
                <img src="/images/emblems/earth.webp" alt="Tierra" class="h-16 w-16 object-contain" />
                <img src="/images/emblems/lotus.webp" alt="Loto" class="h-16 w-16 object-contain" />
            </div>

            <!-- Footer -->
            <footer class="border-t border-border py-8 text-center text-sm text-muted-foreground">
                <p>Loto Blanco — Open source, AGPLv3</p>
                <p class="mt-1">Hecho para estudiantes de UTN FRRe</p>
            </footer>
        </main>
    </div>
</template>
