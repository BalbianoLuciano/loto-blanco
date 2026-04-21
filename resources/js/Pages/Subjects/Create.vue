<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { trans } from 'laravel-vue-i18n'
import { Droplet, Flame, Mountain, Wind, Flower } from 'lucide-vue-next'

interface Template {
    id: number
    name: string
    element: string
    color: string
}

const props = defineProps<{
    templates: Template[]
}>()

const elements = [
    { value: 'water', label: 'Water', icon: Droplet },
    { value: 'fire', label: 'Fire', icon: Flame },
    { value: 'earth', label: 'Earth', icon: Mountain },
    { value: 'air', label: 'Air', icon: Wind },
    { value: 'lotus', label: 'Lotus', icon: Flower },
]

const form = useForm({
    name: '',
    element: 'lotus',
    color: '#1a1a1a',
    description: '',
})

function applyTemplate(template: Template) {
    form.name = template.name
    form.element = template.element
    form.color = template.color
}

function submit() {
    form.post(route('subjects.store'))
}
</script>

<template>
    <Head :title="`${trans('Create')} ${trans('Subject')}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                {{ trans('Create') }} {{ trans('Subject') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <!-- Templates -->
                <div v-if="templates.length > 0" class="mb-8">
                    <h3 class="text-sm font-medium text-muted-foreground mb-3">Materias sugeridas (TUP)</h3>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="template in templates"
                            :key="template.id"
                            variant="outline"
                            size="sm"
                            @click="applyTemplate(template)"
                        >
                            {{ template.name }}
                        </Button>
                    </div>
                </div>

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
                                    {{ trans('Edit') }} descripción
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                />
                            </div>

                            <div class="flex justify-end gap-3">
                                <Link :href="route('subjects.index')">
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
