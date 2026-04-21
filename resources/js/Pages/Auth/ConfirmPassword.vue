<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { trans } from 'laravel-vue-i18n'

const form = useForm({ password: '' })

function submit() {
    form.post(route('password.confirm'), { onFinish: () => form.reset() })
}
</script>

<template>
    <GuestLayout>
        <Head :title="trans('Confirm Password')" />

        <p class="mb-4 text-sm text-muted-foreground">
            Esta es un área segura. Confirmá tu contraseña antes de continuar.
        </p>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-2">
                <Label for="password">{{ trans('Password') }}</Label>
                <Input id="password" v-model="form.password" type="password" required autofocus autocomplete="current-password" />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                Confirmar
            </Button>
        </form>
    </GuestLayout>
</template>
