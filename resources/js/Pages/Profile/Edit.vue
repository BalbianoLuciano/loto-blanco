<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { trans } from 'laravel-vue-i18n'

defineProps<{
    mustVerifyEmail?: boolean
    status?: string
}>()
</script>

<template>
    <Head :title="trans('Profile')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-foreground">
                {{ trans('Profile') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <Card>
                    <CardContent class="p-6 sm:p-8">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-6 sm:p-8">
                        <UpdatePasswordForm class="max-w-xl" />
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-6 sm:p-8">
                        <DeleteUserForm class="max-w-xl" />
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
