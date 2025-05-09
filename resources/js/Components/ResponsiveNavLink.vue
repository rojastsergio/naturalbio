<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
        ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-white text-left text-base font-medium text-white bg-naturalbio-verde focus:outline-none focus:text-white focus:bg-naturalbio-verde focus:border-white transition duration-150 ease-in-out'
        : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-white hover:text-white hover:bg-naturalbio-verde/50 hover:border-white focus:outline-none focus:text-white focus:bg-naturalbio-verde/50 focus:border-white transition duration-150 ease-in-out';
});
</script>

<template>
    <div>
        <button
            v-if="as == 'button'"
            :class="classes"
            class="w-full text-left"
        >
            <slot />
        </button>

        <Link
            v-else
            :href="href"
            :class="classes"
        >
            <slot />
        </Link>
    </div>
</template>