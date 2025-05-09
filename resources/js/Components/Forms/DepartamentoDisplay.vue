<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    departamento: {
        type: Object,
        default: null
    },
    error: {
        type: String,
        default: ''
    }
});

const departamentoNombre = ref(props.departamento?.nombre || '');

watch(() => props.departamento, (newDepartamento) => {
    departamentoNombre.value = newDepartamento?.nombre || '';
}, { deep: true });
</script>

<template>
    <div>
        <label for="departamento" class="block text-sm font-medium text-gray-700 mb-1">
            Departamento
        </label>
        <input
            id="departamento"
            type="text"
            v-model="departamentoNombre"
            disabled
            class="w-full px-4 py-2 border rounded-md shadow-sm bg-gray-50 cursor-not-allowed"
            :class="{ 'border-red-500': error }"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>