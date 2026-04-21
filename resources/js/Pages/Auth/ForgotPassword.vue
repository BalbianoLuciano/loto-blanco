<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { trans } from 'laravel-vue-i18n'

defineProps<{ status?: string }>()

const form = useForm({ email: '' })

function submit() {
    form.post(route('password.email'))
}
</script>

<template>
    <GuestLayout>
        <Head :title="trans('Forgot your password?')" />

        <p class="mb-4 text-sm text-muted-foreground">
            Ingresá tu email y te enviamos un link para restablecer tu contraseña.
        </p>

        <div v-if="status" class="mb-4 rounded-md bg-primary/10 p-3 text-sm font-medium text-primary">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-2">
                <Label for="email">{{ trans('Email') }}</Label>
                <Input id="email" v-model="form.email" type="email" required autofocus autocomplete="username" />
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                Enviar link
            </Button>
        </form>
    </GuestLayout>
</template>
