<template>
    <div :class="[
        'card-service p-6',
        type === 'product' ? 'card-product' : 
        type === 'diagnostic' ? 'card-diagnostic' : 'card-service',
    ]">
        <div class="mb-4">
            <slot name="icon"></slot>
        </div>
        <h3 class="heading-primary text-xl mb-2">{{ title }}</h3>
        <p class="text-naturalbio-gris mb-4">{{ description }}</p>
        <div class="mt-auto">
            <slot name="action"></slot>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'service',
        validator: (value) => ['service', 'product', 'diagnostic'].includes(value)
    }
});
</script>