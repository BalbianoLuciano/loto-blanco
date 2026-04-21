<script setup lang="ts">
import { provideElementTheme, type Element } from '@/composables/useElementTheme'

const props = withDefaults(defineProps<{
    element?: Element
}>(), {
    element: 'lotus',
})

const currentElement = provideElementTheme(props.element)

defineExpose({ currentElement })
</script>

<template>
    <div :data-element="currentElement" class="min-h-screen transition-colors duration-300">
        <slot />
    </div>
</template>
