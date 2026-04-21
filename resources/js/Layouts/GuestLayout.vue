<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Flower, Sun, Moon } from 'lucide-vue-next'

const { isDark, toggleDark } = useDarkMode()
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-background pt-6 sm:justify-center sm:pt-0">
        <Button
            variant="ghost"
            size="icon"
            class="absolute right-4 top-4 h-9 w-9"
            @click="toggleDark()"
        >
            <Sun v-if="isDark" class="h-4 w-4" />
            <Moon v-else class="h-4 w-4" />
        </Button>

        <div class="mb-6">
            <Link href="/" class="flex items-center gap-3">
                <Flower class="h-10 w-10 text-primary" />
                <span class="text-2xl font-semibold text-foreground">Loto Blanco</span>
            </Link>
        </div>

        <Card class="w-full sm:max-w-md">
            <CardContent class="p-6">
                <slot />
            </CardContent>
        </Card>
    </div>
</template>
