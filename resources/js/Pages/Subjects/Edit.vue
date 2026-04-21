<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { trans } from 'laravel-vue-i18n'
import { Droplet, Flame, Mountain, Wind, Flower } from 'lucide-vue-next'

interface Subject {
    id: number
    name: string
    element: string
    color: string
    description: string | null
}

const props = defineProps<{
    subject: Subject
}>()

const elements = [
    { value: 'water', label: 'Water', icon: Droplet },
    { value: 'fire', label: 'Fire', icon: Flame },
    { value: 'earth', label: 'Earth', icon: Mountain },
    { value: 'air', label: 'Air', icon: Wind },
    { value: 'lotus', label: 'Lotus', icon: Flower },
]

const form = useForm({
    name: props.subject.name,
    element: props.subject.element,
    color: props.subject.color,
    description: props.subject.description || '',
})

function submit() {
    form.put(route('subjects.update', props.subject.id))
}
</script>

<template>
    <Head :title="`${trans('Edit')} — ${subject.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                {{ trans('Edit') }} — {{ subject.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ trans('Subject') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1.5">
                                    {{ trans('Name') }}
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-destructive">
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1.5">
                                    Elemento
                                </label>
                                <div class="grid grid-cols-5 gap-2">
                                    <button
                                        v-for="el in elements"
                                        :key="el.value"
                                        type="button"
                                        :data-element="el.value"
                                        class="flex flex-col items-center gap-1.5 rounded-lg border-2 p-3 transition-all"
                                        :class="form.element === el.value
                                            ? 'border-primary bg-primary/5'
                                            : 'border-transparent hover:border-border'"
                                        @click="form.element = el.value"
                                    >
                                        <component :is="el.icon" class="h-5 w-5" />
                                        <span class="text-xs">{{ trans(el.label) }}</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1.5">
                                    Color
                                </label>
                                <input
                                    v-model="form.color"
                                    type="color"
                                    class="h-10 w-20 cursor-pointer rounded border border-input"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-foreground mb-1.5">
                                    Descripción
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                />
                            </div>

                            <div class="flex justify-end gap-3">
                                <Link :href="route('subjects.show', subject.id)">
                                    <Button type="button" variant="outline">{{ trans('Cancel') }}</Button>
                                </Link>
                                <Button type="submit" :disabled="form.processing">
                                    {{ trans('Save') }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
