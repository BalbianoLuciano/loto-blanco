<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { trans } from 'laravel-vue-i18n'

const passwordInput = ref<HTMLInputElement | null>(null)
const currentPasswordInput = ref<HTMLInputElement | null>(null)

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation')
                passwordInput.value?.focus()
            }
            if (form.errors.current_password) {
                form.reset('current_password')
                currentPasswordInput.value?.focus()
            }
        },
    })
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-foreground">{{ trans('Password') }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">Usá una contraseña larga y segura.</p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-4">
            <div class="space-y-2">
                <Label for="current_password">Contraseña actual</Label>
                <Input id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" autocomplete="current-password" />
                <p v-if="form.errors.current_password" class="text-sm text-destructive">{{ form.errors.current_password }}</p>
            </div>

            <div class="space-y-2">
                <Label for="password">Nueva contraseña</Label>
                <Input id="password" ref="passwordInput" v-model="form.password" type="password" autocomplete="new-password" />
                <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>

            <div class="space-y-2">
                <Label for="password_confirmation">{{ trans('Confirm Password') }}</Label>
                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" />
                <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">{{ trans('Save') }}</Button>
                <p v-if="form.recentlySuccessful" class="text-sm text-muted-foreground">Guardado.</p>
            </div>
        </form>
    </section>
</template>
