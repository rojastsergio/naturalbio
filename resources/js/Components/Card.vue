<script setup>
defineProps({
    title: {
        type: String,
        default: ''
    },
    subtitle: {
        type: String,
        default: ''
    },
    flat: {
        type: Boolean,
        default: false
    },
    noPadding: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    elevated: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div 
        :class="[
            'bg-white overflow-visible transition-shadow duration-200 rounded-lg mb-4 sm:mb-6',
            flat ? 'border border-gray-200' : elevated ? 'shadow-md' : 'shadow-sm border border-gray-200'
        ]"
    >
        <!-- Header with title and actions -->
        <div 
            v-if="$slots.header || title || subtitle" 
            :class="[
                'border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:justify-between sm:items-center',
                noPadding ? 'px-3 py-2 sm:px-4 sm:py-3' : 'px-4 py-3 sm:px-6 sm:py-4'
            ]"
        >
            <div class="mb-2 sm:mb-0 w-full sm:w-auto">
                <h3 v-if="title" class="text-base sm:text-lg font-medium text-gray-900 break-words">
                    {{ title }}
                </h3>
                <p v-if="subtitle" class="mt-1 text-xs sm:text-sm text-gray-500 break-words">
                    {{ subtitle }}
                </p>
            </div>
            <div v-if="$slots.header" class="flex-shrink-0 flex flex-wrap gap-2 w-full sm:w-auto">
                <slot name="header"></slot>
            </div>
        </div>

        <!-- Loader -->
        <div v-if="loading" class="flex items-center justify-center py-6 sm:py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-naturalbio-verde"></div>
            <span class="ml-2 text-gray-600">Cargando...</span>
        </div>

        <!-- Content -->
        <div v-else-if="$slots.default" :class="[noPadding ? 'p-0' : 'p-3 sm:p-4 md:p-6']">
            <slot></slot>
        </div>

        <!-- Footer -->
        <div 
            v-if="$slots.footer" 
            :class="[
                'border-t border-gray-200 bg-gray-50',
                noPadding ? 'px-3 py-2 sm:px-4 sm:py-3' : 'px-4 py-3 sm:px-6 sm:py-4'
            ]"
        >
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<style scoped>
/* Estilos adicionales para hover */
.shadow-sm:hover {
    @apply shadow;
}

.shadow-md:hover {
    @apply shadow-lg;
}

/* Asegurarse de que el card no bloquee el scroll */
.overflow-visible {
    overflow: visible !important;
}

/* Asegurarse de que los fondos son correctos */
.bg-white {
    background-color: white !important;
}

.bg-gray-50 {
    background-color: #F9FAFB !important;
}

/* Asegurar que se pueden seleccionar elementos */
* {
    pointer-events: auto !important;
}

/* Mejoras para dispositivos móviles */
@media (max-width: 640px) {
    .mb-6 {
        margin-bottom: 1rem;
    }
    
    /* Botones en el header deben estar en una sola columna */
    :deep(.btn-full-mobile) {
        @apply w-full justify-center mb-2;
    }
    
    :deep(.btn-group-mobile) {
        @apply flex flex-col w-full;
    }
    
    :deep(.btn-group-mobile > *) {
        @apply mb-2 w-full;
    }
}
</style>