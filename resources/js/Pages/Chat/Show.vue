<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { trans } from 'laravel-vue-i18n'
import { Send, Bot, User as UserIcon, FileText, Loader2 } from 'lucide-vue-next'
import { ref, nextTick } from 'vue'
import axios from 'axios'

interface Citation {
    chunk_id: number
    document: string
    page: number
    similarity: number
}

interface Message {
    id: number
    role: 'user' | 'assistant'
    content: string
    citations: Citation[] | null
}

interface SubjectInfo {
    id: number
    name: string
    element: string
}

const props = defineProps<{
    subject: SubjectInfo
    messages: Message[]
}>()

const chatMessages = ref<Message[]>([...props.messages])
const question = ref('')
const loading = ref(false)
const messagesContainer = ref<HTMLElement>()

function scrollToBottom() {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
        }
    })
}

async function send() {
    const q = question.value.trim()
    if (!q || loading.value) return

    chatMessages.value.push({
        id: Date.now(),
        role: 'user',
        content: q,
        citations: null,
    })
    question.value = ''
    loading.value = true
    scrollToBottom()

    try {
        const { data } = await axios.post(route('subjects.chat.ask', props.subject.id), {
            question: q,
        })

        chatMessages.value.push({
            id: data.message_id,
            role: 'assistant',
            content: data.answer,
            citations: data.citations,
        })
    } catch {
        chatMessages.value.push({
            id: Date.now(),
            role: 'assistant',
            content: 'Error al procesar la consulta. Intentá de nuevo.',
            citations: null,
        })
    } finally {
        loading.value = false
        scrollToBottom()
    }
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault()
        send()
    }
}
</script>

<template>
    <Head :title="`${trans('Chat')} — ${subject.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                {{ trans('Chat') }} — {{ subject.name }}
            </h2>
        </template>

        <div class="py-6" :data-element="subject.element">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <Card class="flex flex-col" style="height: calc(100vh - 200px)">
                    <!-- Messages -->
                    <CardContent
                        ref="messagesContainer"
                        class="flex-1 overflow-y-auto space-y-4 p-4"
                    >
                        <div
                            v-if="chatMessages.length === 0"
                            class="flex h-full items-center justify-center text-muted-foreground"
                        >
                            <div class="text-center">
                                <Bot class="mx-auto h-12 w-12 mb-4 opacity-50" />
                                <p>Preguntá algo sobre {{ subject.name }}</p>
                            </div>
                        </div>

                        <div
                            v-for="msg in chatMessages"
                            :key="msg.id"
                            class="flex gap-3"
                            :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
                        >
                            <div
                                v-if="msg.role === 'assistant'"
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10"
                            >
                                <Bot class="h-4 w-4 text-primary" />
                            </div>

                            <div
                                class="max-w-[80%] rounded-lg px-4 py-2.5"
                                :class="msg.role === 'user'
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted'"
                            >
                                <p class="text-sm whitespace-pre-wrap">{{ msg.content }}</p>

                                <div v-if="msg.citations?.length" class="mt-2 flex flex-wrap gap-1.5">
                                    <Badge
                                        v-for="cite in msg.citations"
                                        :key="cite.chunk_id"
                                        variant="outline"
                                        class="text-xs"
                                    >
                                        <FileText class="mr-1 h-3 w-3" />
                                        {{ cite.document }}, p.{{ cite.page }}
                                    </Badge>
                                </div>
                            </div>

                            <div
                                v-if="msg.role === 'user'"
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-secondary"
                            >
                                <UserIcon class="h-4 w-4 text-secondary-foreground" />
                            </div>
                        </div>

                        <div v-if="loading" class="flex gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10">
                                <Loader2 class="h-4 w-4 text-primary animate-spin" />
                            </div>
                            <div class="rounded-lg bg-muted px-4 py-2.5">
                                <p class="text-sm text-muted-foreground">Pensando...</p>
                            </div>
                        </div>
                    </CardContent>

                    <!-- Input -->
                    <div class="border-t p-4">
                        <div class="flex gap-2">
                            <textarea
                                v-model="question"
                                rows="1"
                                :placeholder="`Preguntá sobre ${subject.name}...`"
                                class="flex-1 resize-none rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                @keydown="handleKeydown"
                            />
                            <Button @click="send" :disabled="!question.trim() || loading">
                                <Send class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
