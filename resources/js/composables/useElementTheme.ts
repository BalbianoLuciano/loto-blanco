import { ref, provide, inject, type Ref, type InjectionKey } from 'vue'

export type Element = 'water' | 'fire' | 'earth' | 'air' | 'lotus'

const ELEMENT_KEY: InjectionKey<Ref<Element>> = Symbol('elementTheme')

export function provideElementTheme(defaultElement: Element = 'lotus') {
    const element = ref<Element>(defaultElement)
    provide(ELEMENT_KEY, element)
    return element
}

export function useElementTheme(): Ref<Element> {
    const el = inject(ELEMENT_KEY)
    if (!el) throw new Error('useElementTheme must be used within an ElementThemeProvider')
    return el
}
