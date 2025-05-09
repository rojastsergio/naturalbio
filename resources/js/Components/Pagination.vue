<template>
    <div v-if="links.length > 3" class="flex flex-wrap mt-6 -mb-1">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" 
                class="mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-500 border rounded"
                v-html="link.label"></div>
            <Link v-else
                class="mr-1 mb-1 px-4 py-2 text-sm leading-4 border rounded hover:bg-green-500 hover:text-white focus:border-green-500 focus:text-green-500"
                :class="{ 'bg-green-500 text-white': link.active }"
                :href="link.url"
                v-html="link.label"></Link>
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        default: () => []
    }
});
</script>