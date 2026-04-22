<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { trans } from 'laravel-vue-i18n'
import { Dumbbell, CheckCircle, XCircle, ArrowLeft } from 'lucide-vue-next'

interface Exercise {
    id: number
    statement: string
    difficulty: number
    source_type: string
    hint: string | null
    topic_name: string | null
    attempts_count: number
    last_correct: boolean | null
}

const props = defineProps<{
    subject: { id: number; name: string; element: string }
    exercises: Exercise[]
}>()

const difficultyLabels = ['', 'Muy fácil', 'Fácil', 'Medio', 'Difícil', 'Muy difícil']
const difficultyColors = ['', 'text-green-600', 'text-green-500', 'text-yellow-500', 'text-orange-500', 'text-red-500']
</script>

<template>
    <Head :title="`${trans('Exercises')} — ${subject.name}`" />

    <AuthenticatedLayout :element="subject.element">
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('subjects.show', subject.id)">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h2 class="text-xl font-semibold text-foreground">{{ trans('Exercises') }}</h2>
                    <p class="text-sm text-muted-foreground">{{ subject.name }}</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div v-if="exercises.length === 0" class="text-center py-16 text-muted-foreground">
                    <Dumbbell class="mx-auto h-12 w-12 mb-4 opacity-50" />
                    <p class="text-lg">No hay ejercicios todavía</p>
                    <p class="mt-1">Subí PDFs con guías de ejercicios y se extraerán automáticamente</p>
                </div>

                <div v-else class="space-y-3">
                    <Link
                        v-for="exercise in exercises"
                        :key="exercise.id"
                        :href="route('exercises.show', exercise.id)"
                        class="block"
                    >
                        <Card class="transition-all hover:shadow-md">
                            <CardContent class="flex items-start gap-4 py-4 px-5">
                                <div class="mt-0.5">
                                    <CheckCircle v-if="exercise.last_correct === true" class="h-5 w-5 text-green-500" />
                                    <XCircle v-else-if="exercise.last_correct === false" class="h-5 w-5 text-red-400" />
                                    <Dumbbell v-else class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm line-clamp-2">{{ exercise.statement }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <Badge variant="outline" :class="difficultyColors[exercise.difficulty]">
                                            {{ difficultyLabels[exercise.difficulty] }}
                                        </Badge>
                                        <Badge v-if="exercise.topic_name" variant="secondary">
                                            {{ exercise.topic_name }}
                                        </Badge>
                                        <Badge v-if="exercise.attempts_count > 0" variant="secondary">
                                            {{ exercise.attempts_count }} intentos
                                        </Badge>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
