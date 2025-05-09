<template>
    <button 
        :class="[
            variant === 'primary' ? 'btn-primary' : 
            variant === 'secondary' ? 'btn-secondary' : 
            variant === 'outline' ? 'btn-outline' : 
            variant === 'premium' ? 'btn-premium' : '',
            size === 'lg' ? 'text-lg py-3 px-6' : 
            size === 'sm' ? 'text-sm py-1 px-3' : '',
            block ? 'w-full' : '',
            className
        ]"
        v-bind="$attrs"
    >
        <slot></slot>
    </button>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'outline', 'premium'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    block: {
        type: Boolean,
        default: false
    },
    className: {
        type: String,
        default: ''
    }
});
</script>