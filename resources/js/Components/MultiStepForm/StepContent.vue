<script setup>
defineProps({
    active: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <transition
        name="step-transition"
        mode="out-in"
        appear
    >
        <div
            v-if="active"
            class="step-content overflow-visible"
            role="tabpanel"
        >
            <slot></slot>

            <!-- Botones mejorados - se mostrarán si el slot 'buttons' existe -->
            <div class="step-buttons mt-8">
                <slot name="buttons"></slot>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.step-content {
    width: 100%;
    position: relative;
}

/* Transiciones mejoradas */
.step-transition-enter-active,
.step-transition-leave-active {
    transition: all 0.3s ease-out;
}

.step-transition-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

.step-transition-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}

/* Botones de navegación */
.step-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

/* Asegurar que no se bloquee el scroll */
.overflow-visible {
    overflow: visible !important;
}

/* Mejoras responsive */
@media (max-width: 768px) {
    .step-content {
        padding: 0.75rem 0.5rem;
    }

    .step-buttons {
        flex-direction: row;
        margin-top: 1.5rem;
    }
}

@media (max-width: 640px) {
    .step-content {
        padding: 0.5rem 0.25rem;
    }

    .step-transition-enter-from,
    .step-transition-leave-to {
        transform: translateY(10px);
    }
}
</style>