<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: '#'
    },
    as: {
        type: String,
        default: 'inertia'
    }
});

const baseClass = "block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out";

// Determinamos qué componente renderizar basándonos en 'as'
const currentComponent = computed(() => {
    if (props.as === 'button') return 'button';
    if (props.as === 'a') return 'a';
    return Link;
});

// Atributos adicionales según el componente
const additionalAttrs = computed(() => {
    if (props.as === 'button') return { type: 'submit', class: baseClass + " w-full text-left" };
    if (props.as === 'a') return { href: props.href, class: baseClass };
    return { href: props.href, class: baseClass };
});
</script>

<template>
    <component :is="currentComponent" v-bind="additionalAttrs">
        <slot />
    </component>
</template>