<template>
    <textarea
        class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm w-full"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        :rows="rows"
        :disabled="disabled"
    ></textarea>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 4
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);
</script>