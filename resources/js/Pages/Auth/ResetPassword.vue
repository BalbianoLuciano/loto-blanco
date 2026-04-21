<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { trans } from 'laravel-vue-i18n'

const props = defineProps<{ email: string; token: string }>()

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post(route('password.store'), { onFinish: () => form.reset('password', 'password_confirmation') })
}
</script>

<template>
    <GuestLayout>
        <Head :title="trans('Password')" />

        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-2">
                <Label for="email">{{ trans('Email') }}</Label>
                <Input id="email" v-model="form.email" type="email" required autocomplete="username" />
                <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-2">
                <Label for="password">{{ trans('Password') }}</Label>
                <Input id="password" v-model="form.password" type="password" required autofocus autocomplete="new-password" />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <div class="space-y-2">
                <Label for="password_confirmation">{{ trans('Confirm Password') }}</Label>
                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required autocomplete="new-password" />
                <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing">
                {{ trans('Save') }}
            </Button>
        </form>
    </GuestLayout>
</template>
