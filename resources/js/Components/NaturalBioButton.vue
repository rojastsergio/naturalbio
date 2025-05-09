<template>
    <component 
        :is="as" 
        v-bind="as === 'a' ? { href: href } : {}"
        :type="as === 'button' ? type : undefined" 
        :disabled="disabled" 
        :class="classes"
    >
        <slot />
    </component>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'button',
    },
    as: {
        type: String,
        default: 'button',
        validator: (value) => ['button', 'a'].includes(value)
    },
    href: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'outline', 'premium'].includes(value)
    }
});

const classes = computed(() => {
    const baseClasses = 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';
    
    const variantClasses = {
        primary: 'bg-naturalbio-verde border-transparent text-white hover:bg-green-700 active:bg-green-900 focus:ring-green-500',
        secondary: 'bg-naturalbio-azul border-transparent text-white hover:bg-blue-700 active:bg-blue-900 focus:ring-blue-500',
        outline: 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:ring-indigo-500',
        premium: 'bg-naturalbio-dorado border-transparent text-white hover:bg-yellow-600 active:bg-yellow-700 focus:ring-yellow-500',
    };
    
    const disabledClasses = props.disabled ? 'opacity-50 cursor-not-allowed' : '';
    
    return `${baseClasses} ${variantClasses[props.variant]} ${disabledClasses}`;
});
</script>