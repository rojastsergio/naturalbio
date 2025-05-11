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
            'bg-white overflow-hidden transition-shadow duration-200 rounded-lg mb-6 w-full',
            flat ? 'border border-gray-200' : elevated ? 'shadow-md' : 'shadow-sm border border-gray-200'
        ]"
    >
        <!-- Header with title and actions -->
        <div 
            v-if="$slots.header || title || subtitle" 
            :class="[
                'border-b border-gray-200 bg-gray-50 flex flex-wrap justify-between items-center',
                noPadding ? 'px-3 py-2 sm:px-4 sm:py-3' : 'px-4 py-3 sm:px-6 sm:py-4'
            ]"
        >
            <div class="mb-2 sm:mb-0">
                <h3 v-if="title" class="text-base sm:text-lg font-medium text-gray-900">
                    {{ title }}
                </h3>
                <p v-if="subtitle" class="mt-1 text-xs sm:text-sm text-gray-500">
                    {{ subtitle }}
                </p>
            </div>
            <div v-if="$slots.header" class="flex-shrink-0 flex gap-2">
                <slot name="header"></slot>
            </div>
        </div>

        <!-- Loader -->
        <div v-if="loading" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-naturalbio-verde"></div>
            <span class="ml-2 text-gray-600">Cargando...</span>
        </div>

        <!-- Content -->
        <div v-else-if="$slots.default" :class="[noPadding ? 'p-0' : 'p-4 sm:p-6 xl:p-5']">
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

/* Mejoras para dispositivos móviles */
@media (max-width: 640px) {
    .mb-6 {
        margin-bottom: 1rem;
    }
}
</style>