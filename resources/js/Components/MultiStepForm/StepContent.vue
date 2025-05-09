<script setup>
defineProps({
    active: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <div 
        v-if="active"
        class="step-content overflow-visible transition-opacity duration-300"
    >
        <slot></slot>
    </div>
</template>

<style scoped>
.step-content {
    @apply animate-fade-in;
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-in-out;
}

/* Asegurar que no se bloquee el scroll */
.overflow-visible {
    overflow: visible !important;
}

@media (max-width: 640px) {
    .step-content {
        padding: 0.5rem;
    }
}
</style>