<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { nextTick, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import { trans } from 'laravel-vue-i18n'

const open = ref(false)
const passwordInput = ref<HTMLInputElement | null>(null)

const form = useForm({ password: '' })

function confirmDeletion() {
    open.value = true
    nextTick(() => passwordInput.value?.focus())
}

function deleteUser() {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => { open.value = false },
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    })
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-foreground">Eliminar cuenta</h2>
            <p class="mt-1 text-sm text-muted-foreground">
                Una vez eliminada, todos tus datos se borran permanentemente.
                Descargá cualquier información que quieras conservar antes de continuar.
            </p>
        </header>

        <Button variant="destructive" class="mt-4" @click="confirmDeletion">
            {{ trans('Delete') }} cuenta
        </Button>

        <Dialog v-model:open="open">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ trans('Are you sure?') }}</DialogTitle>
                    <DialogDescription>
                        {{ trans('This action cannot be undone.') }}
                        Ingresá tu contraseña para confirmar.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-2">
                    <Label for="delete-password" class="sr-only">{{ trans('Password') }}</Label>
                    <Input
                        id="delete-password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        :placeholder="trans('Password')"
                        @keyup.enter="deleteUser"
                    />
                    <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="open = false">{{ trans('Cancel') }}</Button>
                    <Button variant="destructive" :disabled="form.processing" @click="deleteUser">
                        {{ trans('Delete') }} cuenta
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </section>
</template>
