<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { trans } from 'laravel-vue-i18n'
import { Flower, RotateCcw, Check, Zap, Star } from 'lucide-vue-next'
import { ref, computed } from 'vue'
import axios from 'axios'

interface FlashcardItem {
    id: number
    front: string
    back: string
    state: string
    reps: number
    stability: number
    difficulty: number
    topic_name: string | null
}

const props = defineProps<{
    cards: FlashcardItem[]
    totalDue: number
    totalCards: number
}>()

const queue = ref([...props.cards])
const currentIndex = ref(0)
const flipped = ref(false)
const completed = ref(0)
const startTime = ref(Date.now())

const current = computed(() => queue.value[currentIndex.value] ?? null)
const remaining = computed(() => queue.value.length - currentIndex.value)

function flip() {
    flipped.value = true
}

async function rate(rating: number) {
    if (!current.value) return

    const timeSpent = Date.now() - startTime.value

    try {
        await axios.post(route('flashcards.submitReview', current.value.id), {
            rating,
            time_spent_ms: timeSpent,
        })
    } catch { /* continue anyway */ }

    completed.value++
    flipped.value = false
    currentIndex.value++
    startTime.value = Date.now()
}

const ratings = [
    { value: 1, label: 'Otra vez', icon: RotateCcw, color: 'text-red-500 hover:bg-red-50' },
    { value: 2, label: 'Difícil', icon: Zap, color: 'text-orange-500 hover:bg-orange-50' },
    { value: 3, label: 'Bien', icon: Check, color: 'text-green-500 hover:bg-green-50' },
    { value: 4, label: 'Fácil', icon: Star, color: 'text-blue-500 hover:bg-blue-50' },
]
</script>

<template>
    <Head :title="trans('Flashcards')" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <!-- Header stats -->
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="text-xl font-semibold">{{ trans('Flashcards') }}</h1>
                    <div class="flex gap-3 text-sm text-muted-foreground">
                        <span>{{ completed }} revisadas</span>
                        <span>{{ remaining }} restantes</span>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="mb-8 h-2 rounded-full bg-muted overflow-hidden">
                    <div
                        class="h-full rounded-full bg-primary transition-all duration-300"
                        :style="{ width: queue.length ? `${(completed / queue.length) * 100}%` : '0%' }"
                    />
                </div>

                <!-- Done state -->
                <div v-if="!current" class="text-center py-20">
                    <Flower class="mx-auto h-16 w-16 mb-4 text-primary/50" />
                    <h2 class="text-2xl font-bold mb-2">
                        {{ completed > 0 ? '¡Sesión completa!' : 'No hay flashcards pendientes' }}
                    </h2>
                    <p class="text-muted-foreground">
                        {{ completed > 0
                            ? `Revisaste ${completed} flashcards.`
                            : 'Creá flashcards desde tus materiales de estudio.' }}
                    </p>
                </div>

                <!-- Card -->
                <div v-else>
                    <Card
                        class="cursor-pointer select-none transition-all min-h-[280px] flex items-center justify-center"
                        @click="!flipped && flip()"
                    >
                        <CardContent class="p-8 text-center w-full">
                            <Badge v-if="current.topic_name" variant="outline" class="mb-4">
                                {{ current.topic_name }}
                            </Badge>

                            <div v-if="!flipped">
                                <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Pregunta</p>
                                <p class="text-lg whitespace-pre-wrap">{{ current.front }}</p>
                                <p class="mt-6 text-sm text-muted-foreground">Tocá para ver la respuesta</p>
                            </div>

                            <div v-else>
                                <p class="text-xs uppercase tracking-wider text-muted-foreground mb-3">Respuesta</p>
                                <p class="text-lg whitespace-pre-wrap">{{ current.back }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Rating buttons -->
                    <div v-if="flipped" class="mt-6 grid grid-cols-4 gap-2">
                        <Button
                            v-for="r in ratings"
                            :key="r.value"
                            variant="outline"
                            class="flex flex-col items-center gap-1 h-auto py-3"
                            :class="r.color"
                            @click="rate(r.value)"
                        >
                            <component :is="r.icon" class="h-5 w-5" />
                            <span class="text-xs">{{ r.label }}</span>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
