<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { trans } from 'laravel-vue-i18n'
import { useDarkMode } from '@/composables/useDarkMode'
import { provideElementTheme, type Element } from '@/composables/useElementTheme'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Separator } from '@/components/ui/separator'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
    Sheet,
    SheetContent,
    SheetTrigger,
} from '@/components/ui/sheet'
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip'
import {
    Flower, Sun, Moon, Menu, Languages,
    LayoutDashboard, BookOpen, User, LogOut, Settings,
    Droplet, Flame, Mountain, Wind,
} from 'lucide-vue-next'

const props = withDefaults(defineProps<{
    element?: Element
}>(), {
    element: 'lotus',
})

const page = usePage()
const { isDark, toggleDark } = useDarkMode()
const currentElement = provideElementTheme(props.element)
const mobileOpen = ref(false)

const user = computed(() => page.props.auth.user as { name: string; email: string })
const initials = computed(() => {
    const parts = user.value.name.split(' ')
    return parts.map(p => p[0]).join('').toUpperCase().slice(0, 2)
})

const elementIcons: Record<string, typeof Droplet> = {
    water: Droplet, fire: Flame, earth: Mountain, air: Wind, lotus: Flower,
}

const subjects = computed(() => {
    const s = (page.props as any).subjects_nav
    return Array.isArray(s) ? s : []
})

function logout() {
    router.post(route('logout'))
}

function toggleLocale() {
    // TODO: call backend to switch locale, for now just toggle
    const current = document.documentElement.lang
    const next = current === 'es' ? 'en' : 'es'
    document.documentElement.lang = next
}
</script>

<template>
    <div :data-element="currentElement" class="min-h-screen bg-background text-foreground transition-colors duration-300">
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 border-b border-border bg-background/80 backdrop-blur-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-14 items-center justify-between">
                    <!-- Left: Logo + Nav -->
                    <div class="flex items-center gap-6">
                        <Link :href="route('dashboard')" class="flex items-center gap-2">
                            <Flower class="h-6 w-6 text-primary" />
                            <span class="hidden font-semibold sm:inline">Loto Blanco</span>
                        </Link>

                        <Separator orientation="vertical" class="hidden h-6 sm:block" />

                        <!-- Desktop Nav -->
                        <div class="hidden items-center gap-1 sm:flex">
                            <Link :href="route('dashboard')">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    :class="route().current('dashboard') ? 'bg-accent' : ''"
                                >
                                    <LayoutDashboard class="mr-1.5 h-4 w-4" />
                                    {{ trans('Dashboard') }}
                                </Button>
                            </Link>
                            <Link :href="route('subjects.index')">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    :class="route().current('subjects.*') ? 'bg-accent' : ''"
                                >
                                    <BookOpen class="mr-1.5 h-4 w-4" />
                                    {{ trans('Subjects') }}
                                </Button>
                            </Link>
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-1">
                        <TooltipProvider :delay-duration="300">
                            <!-- Language toggle -->
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="ghost" size="icon" class="h-9 w-9" @click="toggleLocale">
                                        <Languages class="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>{{ trans('Language') }}</TooltipContent>
                            </Tooltip>

                            <!-- Dark mode toggle -->
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <Button variant="ghost" size="icon" class="h-9 w-9" @click="toggleDark()">
                                        <Sun v-if="isDark" class="h-4 w-4" />
                                        <Moon v-else class="h-4 w-4" />
                                    </Button>
                                </TooltipTrigger>
                                <TooltipContent>{{ isDark ? trans('Light') : trans('Dark') }}</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>

                        <!-- User dropdown (desktop) -->
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" class="hidden gap-2 sm:flex">
                                    <Avatar class="h-7 w-7">
                                        <AvatarFallback class="text-xs">{{ initials }}</AvatarFallback>
                                    </Avatar>
                                    <span class="text-sm">{{ user.name }}</span>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-48">
                                <DropdownMenuItem as-child>
                                    <Link :href="route('profile.edit')" class="flex items-center gap-2">
                                        <Settings class="h-4 w-4" />
                                        {{ trans('Profile') }}
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem @click="logout" class="flex items-center gap-2 text-destructive">
                                    <LogOut class="h-4 w-4" />
                                    {{ trans('Log Out') }}
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <!-- Mobile hamburger -->
                        <Sheet v-model:open="mobileOpen">
                            <SheetTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-9 w-9 sm:hidden">
                                    <Menu class="h-5 w-5" />
                                </Button>
                            </SheetTrigger>
                            <SheetContent side="right" class="w-72">
                                <div class="flex flex-col gap-4 pt-6">
                                    <div class="flex items-center gap-3 px-2">
                                        <Avatar class="h-10 w-10">
                                            <AvatarFallback>{{ initials }}</AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p class="text-sm font-medium">{{ user.name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                        </div>
                                    </div>

                                    <Separator />

                                    <div class="flex flex-col gap-1">
                                        <Link :href="route('dashboard')" @click="mobileOpen = false">
                                            <Button variant="ghost" class="w-full justify-start">
                                                <LayoutDashboard class="mr-2 h-4 w-4" />
                                                {{ trans('Dashboard') }}
                                            </Button>
                                        </Link>
                                        <Link :href="route('subjects.index')" @click="mobileOpen = false">
                                            <Button variant="ghost" class="w-full justify-start">
                                                <BookOpen class="mr-2 h-4 w-4" />
                                                {{ trans('Subjects') }}
                                            </Button>
                                        </Link>
                                    </div>

                                    <Separator />

                                    <div class="flex flex-col gap-1">
                                        <Link :href="route('profile.edit')" @click="mobileOpen = false">
                                            <Button variant="ghost" class="w-full justify-start">
                                                <Settings class="mr-2 h-4 w-4" />
                                                {{ trans('Profile') }}
                                            </Button>
                                        </Link>
                                        <Button variant="ghost" class="w-full justify-start text-destructive" @click="logout">
                                            <LogOut class="mr-2 h-4 w-4" />
                                            {{ trans('Log Out') }}
                                        </Button>
                                    </div>
                                </div>
                            </SheetContent>
                        </Sheet>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="border-b border-border bg-card">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
