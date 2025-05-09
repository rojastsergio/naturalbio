<script setup>
import { ref, inject, computed, watch } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    collapsed: {
        type: Boolean,
        default: false
    },
    sectionKey: {
        type: String,
        required: true
    }
});

// Obtener la clave de sección activa del padre
const activeSectionKey = inject('activeSectionKey', ref('general'));

// Estado de expansión de la sección
const expanded = ref(false);

// Verificar si esta sección está activa
const isActive = computed(() => {
    return activeSectionKey.value === props.sectionKey;
});

// Expandir automáticamente la sección si está activa
watch(isActive, (newValue) => {
    if (newValue) {
        expanded.value = true;
    }
}, { immediate: true });

// Cerrar otras secciones cuando se active una nueva
watch(() => activeSectionKey.value, (newValue) => {
    if (newValue !== props.sectionKey) {
        expanded.value = false;
    } else {
        expanded.value = true;
    }
});

function toggleExpanded() {
    if (!props.collapsed) {
        expanded.value = !expanded.value;
    }
}
</script>

<template>
    <div class="mb-4 sidebar-section">
        <!-- Encabezado de la sección -->
        <div 
            class="flex items-center justify-between px-2 py-2 text-gray-500 cursor-pointer hover:text-gray-700 sidebar-section-header"
            @click="toggleExpanded"
        >
            <span 
                :class="[
                    'text-xs font-semibold uppercase tracking-wider transition-opacity duration-200',
                    collapsed ? 'opacity-0 w-0' : 'opacity-100'
                ]"
            >
                {{ title }}
            </span>
            <svg 
                v-if="!collapsed"
                xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4 transition-transform"
                :class="{ 'transform rotate-180': expanded }"
                viewBox="0 0 20 20" 
                fill="currentColor"
            >
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
                
        <!-- Contenido de la sección -->
        <div v-if="collapsed || expanded">
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>
/* Ajustes responsivos */
@media (max-width: 768px) {
    .sidebar-section {
        margin-bottom: 0.5rem;
    }
    
    .sidebar-section-header {
        padding: 0.5rem;
    }
}

/* Asegurar que los colores son correctos */
.text-gray-500 {
    color: #6B7280 !important;
}

.hover\:text-gray-700:hover {
    color: #374151 !important;
}
</style>