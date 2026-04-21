<script setup lang="ts">
import { computed } from 'vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { trans } from 'laravel-vue-i18n'

const props = defineProps<{ status?: string }>()
const form = useForm({})

const verificationLinkSent = computed(() => props.status === 'verification-link-sent')

function submit() {
    form.post(route('verification.send'))
}
</script>

<template>
    <GuestLayout>
        <Head title="Verificar email" />

        <p class="mb-4 text-sm text-muted-foreground">
            Gracias por registrarte. Verificá tu email haciendo click en el link que te enviamos.
        </p>

        <div v-if="verificationLinkSent" class="mb-4 rounded-md bg-primary/10 p-3 text-sm font-medium text-primary">
            Se envió un nuevo link de verificación a tu email.
        </div>

        <form @submit.prevent="submit" class="flex items-center justify-between">
            <Button type="submit" :disabled="form.processing">
                Reenviar email
            </Button>

            <Link :href="route('logout')" method="post" as="button" class="text-sm text-muted-foreground hover:text-foreground">
                {{ trans('Log Out') }}
            </Link>
        </form>
    </GuestLayout>
</template>
