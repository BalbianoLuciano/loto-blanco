<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { trans } from 'laravel-vue-i18n'

defineProps<{
    mustVerifyEmail?: boolean
    status?: string
}>()

const user = usePage().props.auth.user as { name: string; email: string; email_verified_at: string | null }

const form = useForm({
    name: user.name,
    email: user.email,
})
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-foreground">{{ trans('Profile') }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">Actualizá tu nombre y email.</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-4">
            <div class="space-y-2">
                <Label for="name">{{ trans('Name') }}</Label>
                <Input id="name" v-model="form.name" type="text" required autofocus autocomplete="name" />
                <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
            </div>

            <div class="space-y-2">
                <Label for="email">{{ trans('Email') }}</Label>
                <Input id="email" v-model="form.email" type="email" required autocomplete="username" />
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm text-muted-foreground">
                    Tu email no está verificado.
                    <Link :href="route('verification.send')" method="post" as="button" class="text-primary underline">
                        Reenviar email de verificación.
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-primary">
                    Se envió un nuevo link de verificación.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">{{ trans('Save') }}</Button>
                <p v-if="form.recentlySuccessful" class="text-sm text-muted-foreground">Guardado.</p>
            </div>
        </form>
    </section>
</template>
