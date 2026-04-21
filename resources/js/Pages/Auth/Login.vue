<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import { trans } from 'laravel-vue-i18n'

defineProps<{
    canResetPassword?: boolean
    status?: string
}>()

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <GuestLayout>
        <Head :title="trans('Log in')" />

        <div v-if="status" class="mb-4 rounded-md bg-primary/10 p-3 text-sm font-medium text-primary">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-2">
                <Label for="email">{{ trans('Email') }}</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-2">
                <Label for="password">{{ trans('Password') }}</Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <Checkbox v-model:checked="form.remember" />
                    <span class="text-sm text-muted-foreground">{{ trans('Remember me') }}</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-muted-foreground hover:text-foreground"
                >
                    {{ trans('Forgot your password?') }}
                </Link>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                {{ trans('Log in') }}
            </Button>

            <p class="text-center text-sm text-muted-foreground">
                ¿No tenés cuenta?
                <Link :href="route('register')" class="font-medium text-primary hover:underline">
                    {{ trans('Register') }}
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
