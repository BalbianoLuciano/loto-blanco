<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { trans } from 'laravel-vue-i18n'
import { Droplet, Flame, Mountain, Wind, Flower, Plus } from 'lucide-vue-next'
import { computed } from 'vue'

interface Subject {
    id: number
    name: string
    element: string
    color: string
    description: string | null
    documents_count: number
    topics_count: number
}

const props = defineProps<{
    subjects: Subject[]
}>()

const elementIcons: Record<string, typeof Droplet> = {
    water: Droplet,
    fire: Flame,
    earth: Mountain,
    air: Wind,
    lotus: Flower,
}

const elementLabels: Record<string, string> = {
    water: 'Water',
    fire: 'Fire',
    earth: 'Earth',
    air: 'Air',
    lotus: 'Lotus',
}
</script>

<template>
    <Head :title="trans('Subjects')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-foreground">
                    {{ trans('Subjects') }}
                </h2>
                <Link :href="route('subjects.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        {{ trans('Create') }}
                    </Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="subjects.length === 0" class="text-center py-16 text-muted-foreground">
                    <Flower class="mx-auto h-12 w-12 mb-4 opacity-50" />
                    <p class="text-lg">{{ trans('No results found') }}</p>
                    <Link :href="route('subjects.create')" class="mt-4 inline-block">
                        <Button variant="outline">
                            {{ trans('Create') }} {{ trans('Subject') }}
                        </Button>
                    </Link>
                </div>

                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="subject in subjects"
                        :key="subject.id"
                        :href="route('subjects.show', subject.id)"
                        class="block"
                    >
                        <Card
                            :data-element="subject.element"
                            class="transition-all hover:shadow-lg hover:-translate-y-0.5"
                        >
                            <CardHeader>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10"
                                    >
                                        <component
                                            :is="elementIcons[subject.element] || Flower"
                                            class="h-5 w-5 text-primary"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <CardTitle class="truncate">{{ subject.name }}</CardTitle>
                                        <CardDescription v-if="subject.description" class="line-clamp-1 mt-1">
                                            {{ subject.description }}
                                        </CardDescription>
                                    </div>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <Badge variant="secondary">
                                        {{ subject.topics_count }} {{ trans('Topics') }}
                                    </Badge>
                                    <Badge variant="secondary">
                                        {{ subject.documents_count }} {{ trans('Documents') }}
                                    </Badge>
                                    <Badge variant="outline">
                                        {{ trans(elementLabels[subject.element] || 'Lotus') }}
                                    </Badge>
                                </div>
                            </CardHeader>
                        </Card>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
