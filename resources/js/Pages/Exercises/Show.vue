<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { trans } from 'laravel-vue-i18n'
import { ArrowLeft, Lightbulb, CheckCircle, XCircle, Send } from 'lucide-vue-next'
import { ref } from 'vue'
import axios from 'axios'

interface Attempt {
    id: number
    answer: string
    correct: boolean
    feedback: string | null
    created_at: string
}

const props = defineProps<{
    subject: { id: number; name: string; element: string }
    exercise: { id: number; statement: string; difficulty: number; hint: string | null; solution: string | null }
    attempts: Attempt[]
}>()

const answer = ref('')
const submitting = ref(false)
const result = ref<{ correct: boolean; feedback: string; solution: string | null } | null>(null)
const showHint = ref(false)
const pastAttempts = ref([...props.attempts])

async function submit() {
    if (!answer.value.trim() || submitting.value) return

    submitting.value = true
    result.value = null

    try {
        const { data } = await axios.post(route('exercises.attempt', props.exercise.id), {
            answer: answer.value.trim(),
        })
        result.value = data
        pastAttempts.value.unshift({
            id: Date.now(),
            answer: answer.value,
            correct: data.correct,
            feedback: data.feedback,
            created_at: new Date().toISOString(),
        })
    } catch {
        result.value = { correct: false, feedback: 'Error al enviar. Intentá de nuevo.', solution: null }
    } finally {
        submitting.value = false
    }
}

function retry() {
    answer.value = ''
    result.value = null
}
</script>

<template>
    <Head :title="`Ejercicio — ${subject.name}`" />

    <AuthenticatedLayout :element="subject.element">
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('subjects.exercises', subject.id)">
                    <Button variant="ghost" size="icon"><ArrowLeft class="h-4 w-4" /></Button>
                </Link>
                <div>
                    <h2 class="text-xl font-semibold text-foreground">Ejercicio</h2>
                    <p class="text-sm text-muted-foreground">{{ subject.name }}</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Statement -->
                <Card>
                    <CardHeader>
                        <CardTitle class="text-base">Enunciado</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="whitespace-pre-wrap">{{ exercise.statement }}</p>

                        <div v-if="exercise.hint" class="mt-4">
                            <Button v-if="!showHint" variant="outline" size="sm" @click="showHint = true">
                                <Lightbulb class="mr-1.5 h-4 w-4" />
                                Ver pista
                            </Button>
                            <div v-else class="rounded-md bg-accent p-3 text-sm text-accent-foreground">
                                <Lightbulb class="inline h-4 w-4 mr-1" />
                                {{ exercise.hint }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Answer input -->
                <Card v-if="!result">
                    <CardContent class="pt-6">
                        <label class="block text-sm font-medium text-foreground mb-2">Tu respuesta</label>
                        <textarea
                            v-model="answer"
                            rows="4"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                            placeholder="Escribí tu respuesta acá..."
                            @keydown.ctrl.enter="submit"
                        />
                        <div class="mt-3 flex justify-end">
                            <Button @click="submit" :disabled="!answer.trim() || submitting">
                                <Send class="mr-1.5 h-4 w-4" />
                                {{ submitting ? 'Enviando...' : 'Enviar' }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Result -->
                <Card v-if="result" :class="result.correct ? 'border-green-500/50' : 'border-red-400/50'">
                    <CardContent class="pt-6">
                        <div class="flex items-center gap-3 mb-3">
                            <CheckCircle v-if="result.correct" class="h-6 w-6 text-green-500" />
                            <XCircle v-else class="h-6 w-6 text-red-400" />
                            <span class="font-medium text-lg">
                                {{ result.correct ? '¡Correcto!' : 'Incorrecto' }}
                            </span>
                        </div>
                        <p class="text-sm text-muted-foreground">{{ result.feedback }}</p>
                        <div v-if="result.solution" class="mt-3 rounded-md bg-muted p-3 text-sm">
                            <strong>Solución:</strong> {{ result.solution }}
                        </div>
                        <Button class="mt-4" variant="outline" @click="retry">
                            Intentar de nuevo
                        </Button>
                    </CardContent>
                </Card>

                <!-- Past attempts -->
                <div v-if="pastAttempts.length > 0">
                    <h3 class="text-sm font-medium text-muted-foreground mb-3">Intentos anteriores</h3>
                    <div class="space-y-2">
                        <div
                            v-for="att in pastAttempts.slice(0, 5)"
                            :key="att.id"
                            class="flex items-center gap-3 rounded-md border border-border p-3 text-sm"
                        >
                            <CheckCircle v-if="att.correct" class="h-4 w-4 shrink-0 text-green-500" />
                            <XCircle v-else class="h-4 w-4 shrink-0 text-red-400" />
                            <span class="truncate flex-1">{{ att.answer }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
