<template>
    <select
        class="border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm w-full"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option v-if="placeholder" value="">{{ placeholder }}</option>
        <option 
            v-for="option in processedOptions"
            :key="getOptionValue(option)"
            :value="getOptionValue(option)"
        >
            {{ getOptionLabel(option) }}
        </option>
    </select>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    options: {
        type: Array,
        default: () => []
    },
    optionLabel: {
        type: String,
        default: 'label'
    },
    optionValue: {
        type: String,
        default: 'value'
    },
    placeholder: {
        type: String,
        default: 'Seleccionar opción'
    }
});

// Procesar las opciones y eliminar duplicados basados en el valor
const processedOptions = computed(() => {
    if (!props.options || !props.options.length) return [];
    
    // Crear un mapa para detectar duplicados por el valor
    const uniqueMap = new Map();
    
    return props.options.filter(option => {
        const value = getOptionValue(option);
        if (uniqueMap.has(value)) {
            return false; // Descartar duplicados
        } else {
            uniqueMap.set(value, true);
            return true;
        }
    });
});

// Obtener el valor de la opción
function getOptionValue(option) {
    if (option === null || option === undefined) return '';
    if (typeof option !== 'object') return option;
    if (option.value !== undefined) return option.value;
    if (props.optionValue && option[props.optionValue] !== undefined) return option[props.optionValue];
    return option;
}

// Obtener la etiqueta de la opción
function getOptionLabel(option) {
    if (option === null || option === undefined) return '';
    if (typeof option !== 'object') return option;
    if (option.label !== undefined) return option.label;
    if (props.optionLabel && option[props.optionLabel] !== undefined) return option[props.optionLabel];
    return option;
}

defineEmits(['update:modelValue']);
</script>