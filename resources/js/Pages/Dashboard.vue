<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { trans } from 'laravel-vue-i18n'
import { computed } from 'vue'
import {
    Droplet, Flame, Mountain, Wind, Flower,
    Plus, FileText, BookOpen, Zap, Calendar,
} from 'lucide-vue-next'

interface Subject {
    id: number
    name: string
    element: string
    color: string
}

const page = usePage()

const subjects = computed(() => {
    const s = (page.props as any).subjects_nav
    return Array.isArray(s) ? s as Subject[] : []
})

const user = computed(() => page.props.auth.user as { name: string })

const elementIcons: Record<string, typeof Droplet> = {
    water: Droplet, fire: Flame, earth: Mountain, air: Wind, lotus: Flower,
}

const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 12) return 'Buenos días'
    if (hour < 19) return 'Buenas tardes'
    return 'Buenas noches'
})
</script>

<template>
    <Head :title="trans('Dashboard')" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Greeting -->
                <div class="mb-8">
                    <h1 class="text-2xl font-bold">{{ greeting }}, {{ user.name }}</h1>
                    <p class="mt-1 text-muted-foreground">¿Qué vas a estudiar hoy?</p>
                </div>

                <!-- Quick stats -->
                <div class="mb-8 grid gap-4 sm:grid-cols-3">
                    <Card>
                        <CardContent class="flex items-center gap-4 pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <BookOpen class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold">{{ subjects.length }}</p>
                                <p class="text-sm text-muted-foreground">{{ trans('Subjects') }}</p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex items-center gap-4 pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <Zap class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold">0</p>
                                <p class="text-sm text-muted-foreground">{{ trans('Streak') }} ({{ trans('days') }})</p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="flex items-center gap-4 pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <Calendar class="h-5 w-5 text-primary" />
                            </div>
                            <div>
                                <p class="text-2xl font-bold">0</p>
                                <p class="text-sm text-muted-foreground">{{ trans('Due today') }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Subjects grid -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">{{ trans('Subjects') }}</h2>
                    <Link :href="route('subjects.create')">
                        <Button variant="outline" size="sm">
                            <Plus class="mr-1.5 h-4 w-4" />
                            {{ trans('Create') }}
                        </Button>
                    </Link>
                </div>

                <div v-if="subjects.length === 0" class="text-center py-16">
                    <Flower class="mx-auto h-12 w-12 mb-4 text-muted-foreground/50" />
                    <p class="text-muted-foreground mb-4">Todavía no tenés materias</p>
                    <Link :href="route('subjects.create')">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Crear materia
                        </Button>
                    </Link>
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="subject in subjects"
                        :key="subject.id"
                        :href="route('subjects.show', subject.id)"
                        class="block"
                    >
                        <Card
                            :data-element="subject.element"
                            class="transition-all hover:shadow-md hover:-translate-y-0.5 h-full"
                        >
                            <CardContent class="flex items-center gap-3 pt-6">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                                    <component
                                        :is="elementIcons[subject.element] || Flower"
                                        class="h-5 w-5 text-primary"
                                    />
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium truncate">{{ subject.name }}</p>
                                    <div class="flex gap-1.5 mt-1">
                                        <Badge variant="outline" class="text-xs">
                                            <FileText class="mr-1 h-3 w-3" />
                                            0 docs
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
