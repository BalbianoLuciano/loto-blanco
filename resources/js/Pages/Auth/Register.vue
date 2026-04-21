<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { trans } from 'laravel-vue-i18n'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <GuestLayout>
        <Head :title="trans('Register')" />

        <form @submit.prevent="submit" class="space-y-4">
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

            <div class="space-y-2">
                <Label for="password">{{ trans('Password') }}</Label>
                <Input id="password" v-model="form.password" type="password" required autocomplete="new-password" />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <div class="space-y-2">
                <Label for="password_confirmation">{{ trans('Confirm Password') }}</Label>
                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required autocomplete="new-password" />
                <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                {{ trans('Register') }}
            </Button>

            <p class="text-center text-sm text-muted-foreground">
                {{ trans('Already registered?') }}
                <Link :href="route('login')" class="font-medium text-primary hover:underline">
                    {{ trans('Log in') }}
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
