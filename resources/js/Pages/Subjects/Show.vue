<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { trans } from 'laravel-vue-i18n'
import {
    Droplet, Flame, Mountain, Wind, Flower,
    FileText, MessageSquare, BookOpen, Upload, Pencil, Trash2,
    Dumbbell, Layers, BarChart3,
} from 'lucide-vue-next'
import { ref } from 'vue'

interface Topic { id: number; name: string; sort_order: number }
interface Document { id: number; title: string; original_filename: string; status: string; size_bytes: number }
interface MasteryItem {
    topic_id: number; topic_name: string; stability: number;
    difficulty: number; state: string; due_at: string | null; reps: number
}

const props = defineProps<{
    subject: { id: number; name: string; element: string; color: string; description: string | null; topics: Topic[]; documents: Document[] }
    mastery: MasteryItem[]
    exercisesCount: number
}>()

const elementIcons: Record<string, typeof Droplet> = {
    water: Droplet, fire: Flame, earth: Mountain, air: Wind, lotus: Flower,
}

function formatBytes(bytes: number): string {
    if (bytes < 1024) return bytes + ' B'
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / 1048576).toFixed(1) + ' MB'
}

function deleteSubject() {
    if (confirm(trans('Are you sure?'))) {
        router.delete(route('subjects.destroy', props.subject.id))
    }
}

const fileInput = ref<HTMLInputElement>()
const uploading = ref(false)

function triggerUpload() { fileInput.value?.click() }

function handleFiles(event: Event) {
    const target = event.target as HTMLInputElement
    if (!target.files?.length) return
    uploading.value = true
    const formData = new FormData()
    for (const file of target.files) formData.append('documents[]', file)
    router.post(route('subjects.documents.store', props.subject.id), formData, {
        forceFormData: true,
        onFinish: () => { uploading.value = false; target.value = '' },
    })
}

function masteryPercent(stability: number): number {
    return Math.min(100, Math.round((stability / 50) * 100))
}
</script>

<template>
    <Head :title="subject.name" />

    <AuthenticatedLayout :element="subject.element">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                        <component :is="elementIcons[subject.element] || Flower" class="h-5 w-5 text-primary" />
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold leading-tight text-foreground">{{ subject.name }}</h2>
                        <p v-if="subject.description" class="text-sm text-muted-foreground">{{ subject.description }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('subjects.edit', subject.id)">
                        <Button variant="outline" size="sm"><Pencil class="mr-1.5 h-4 w-4" />{{ trans('Edit') }}</Button>
                    </Link>
                    <Button variant="outline" size="sm" @click="deleteSubject">
                        <Trash2 class="mr-1.5 h-4 w-4" />{{ trans('Delete') }}
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <Tabs default-value="documents">
                    <TabsList>
                        <TabsTrigger value="documents">
                            <FileText class="mr-1.5 h-4 w-4" />{{ trans('Documents') }}
                        </TabsTrigger>
                        <TabsTrigger value="exercises">
                            <Dumbbell class="mr-1.5 h-4 w-4" />{{ trans('Exercises') }}
                            <Badge v-if="exercisesCount > 0" variant="secondary" class="ml-1.5">{{ exercisesCount }}</Badge>
                        </TabsTrigger>
                        <TabsTrigger value="mastery">
                            <BarChart3 class="mr-1.5 h-4 w-4" />{{ trans('Mastery') }}
                        </TabsTrigger>
                        <TabsTrigger value="chat">
                            <MessageSquare class="mr-1.5 h-4 w-4" />{{ trans('Chat') }}
                        </TabsTrigger>
                    </TabsList>

                    <!-- Documents -->
                    <TabsContent value="documents" class="mt-6">
                        <div class="flex justify-end mb-4">
                            <input ref="fileInput" type="file" accept=".pdf" multiple class="hidden" @change="handleFiles" />
                            <Button @click="triggerUpload" :disabled="uploading">
                                <Upload class="mr-2 h-4 w-4" />
                                {{ uploading ? trans('Loading...') : trans('Upload') }}
                            </Button>
                        </div>
                        <div v-if="subject.documents.length === 0" class="text-center py-16 text-muted-foreground">
                            <FileText class="mx-auto h-12 w-12 mb-4 opacity-50" />
                            <p>Subí PDFs para empezar a estudiar</p>
                        </div>
                        <div v-else class="space-y-2">
                            <Card v-for="doc in subject.documents" :key="doc.id">
                                <CardContent class="flex items-center justify-between py-3 px-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <FileText class="h-5 w-5 shrink-0 text-muted-foreground" />
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium truncate">{{ doc.title }}</p>
                                            <p class="text-xs text-muted-foreground">{{ formatBytes(doc.size_bytes) }}</p>
                                        </div>
                                    </div>
                                    <Badge :variant="doc.status === 'ready' ? 'default' : 'secondary'">{{ doc.status }}</Badge>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <!-- Exercises -->
                    <TabsContent value="exercises" class="mt-6">
                        <div v-if="exercisesCount === 0" class="text-center py-16 text-muted-foreground">
                            <Dumbbell class="mx-auto h-12 w-12 mb-4 opacity-50" />
                            <p>Los ejercicios se extraen automáticamente de los PDFs</p>
                        </div>
                        <div v-else class="text-center py-8">
                            <Dumbbell class="mx-auto h-12 w-12 mb-4 text-primary/50" />
                            <p class="text-lg font-medium mb-2">{{ exercisesCount }} ejercicios disponibles</p>
                            <Link :href="route('subjects.exercises', subject.id)">
                                <Button>Ir a ejercicios</Button>
                            </Link>
                        </div>
                    </TabsContent>

                    <!-- Mastery -->
                    <TabsContent value="mastery" class="mt-6">
                        <div v-if="mastery.length === 0" class="text-center py-16 text-muted-foreground">
                            <BarChart3 class="mx-auto h-12 w-12 mb-4 opacity-50" />
                            <p>El progreso aparece al completar ejercicios y flashcards</p>
                        </div>
                        <div v-else class="space-y-3">
                            <Card v-for="m in mastery" :key="m.topic_id">
                                <CardContent class="py-4 px-5">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium">{{ m.topic_name }}</span>
                                        <div class="flex items-center gap-2">
                                            <Badge variant="outline">{{ m.reps }} reps</Badge>
                                            <Badge :variant="m.state === 'new' ? 'secondary' : 'default'">
                                                {{ m.state === 'new' ? 'Nuevo' : 'En revisión' }}
                                            </Badge>
                                        </div>
                                    </div>
                                    <div class="h-2 rounded-full bg-muted overflow-hidden">
                                        <div
                                            class="h-full rounded-full bg-primary transition-all"
                                            :style="{ width: masteryPercent(m.stability) + '%' }"
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        Estabilidad: {{ m.stability.toFixed(1) }} · Dificultad: {{ (m.difficulty * 100).toFixed(0) }}%
                                    </p>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <!-- Chat -->
                    <TabsContent value="chat" class="mt-6">
                        <div v-if="subject.documents.length === 0">
                            <Card>
                                <CardContent class="py-16 text-center text-muted-foreground">
                                    <MessageSquare class="mx-auto h-12 w-12 mb-4 opacity-50" />
                                    <p>Subí documentos primero para usar el chat RAG</p>
                                </CardContent>
                            </Card>
                        </div>
                        <div v-else class="text-center py-8">
                            <MessageSquare class="mx-auto h-12 w-12 mb-4 text-primary/50" />
                            <Link :href="route('subjects.chat', subject.id)">
                                <Button>Abrir chat</Button>
                            </Link>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
