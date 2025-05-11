<script setup>
defineProps({
    steps: {
        type: Array,
        required: true
    },
    currentStep: {
        type: Number,
        required: true
    }
});
</script>

<template>
    <div class="step-indicator-container overflow-x-auto overflow-y-visible" role="navigation" aria-label="Pasos de formulario">
        <div class="step-indicator flex items-center min-w-max">
            <div
                v-for="(step, index) in steps"
                :key="index"
                class="flex flex-col items-center"
            >
                <div class="flex items-center mb-2">
                    <!-- Step bubble with improved visuals -->
                    <div
                        :class="[
                            'step-circle flex items-center justify-center',
                            index < currentStep ? 'step-complete' :
                            index === currentStep ? 'step-active' : 'step-inactive'
                        ]"
                        :aria-current="index === currentStep ? 'step' : undefined"
                        :aria-label="`Paso ${index + 1}${index < currentStep ? ': Completado' : index === currentStep ? ': Actual' : ''}`"
                    >
                        <span v-if="index < currentStep">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span v-else>{{ index + 1 }}</span>
                    </div>

                    <!-- Step line with animation -->
                    <div
                        v-if="index < steps.length - 1"
                        :class="[
                            'step-line transition-all duration-500',
                            index < currentStep ? 'step-line-complete' : 'step-line-inactive'
                        ]"
                    ></div>
                </div>

                <!-- Step label - Added for better usability -->
                <div class="step-label text-center text-xs font-medium" :class="index === currentStep ? 'text-naturalbio-verde' : 'text-gray-500'">
                    {{ step.label || `Paso ${index + 1}` }}
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.step-indicator-container {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    padding: 1rem 0;
    margin-bottom: 1.5rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(76, 175, 80, 0.3) transparent;
}

.step-indicator-container::-webkit-scrollbar {
    height: 4px;
}

.step-indicator-container::-webkit-scrollbar-track {
    background: transparent;
}

.step-indicator-container::-webkit-scrollbar-thumb {
    background-color: rgba(76, 175, 80, 0.3);
    border-radius: 9999px;
}

.step-indicator {
    display: flex;
    align-items: center;
    padding: 0 1rem;
    min-width: max-content;
}

.step-circle {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    font-size: 1rem;
    font-weight: 500;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.step-active {
    background-color: #4CAF50;
    color: white;
    border: 2px solid #4CAF50;
    transform: scale(1.1);
    box-shadow: 0 4px 6px rgba(76, 175, 80, 0.2);
}

.step-complete {
    background-color: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
    border: 2px solid #4CAF50;
}

.step-inactive {
    background-color: white;
    color: #6B7280;
    border: 2px solid #E5E7EB;
}

.step-line {
    width: 3rem;
    height: 3px;
    margin: 0 0.5rem;
    position: relative;
    overflow: hidden;
}

.step-line-complete {
    background-color: #4CAF50;
}

.step-line-inactive {
    background-color: #E5E7EB;
}

.step-label {
    width: 100%;
    margin-top: 0.25rem;
    white-space: nowrap;
    font-size: 0.75rem;
    font-weight: 500;
    transition: color 0.3s ease;
}

/* Animations */
.step-active .step-circle-inner {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(76, 175, 80, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
    }
}

/* Responsive mejoras */
@media (max-width: 640px) {
    .step-circle {
        width: 2.25rem;
        height: 2.25rem;
        font-size: 0.9rem;
    }

    .step-line {
        width: 2.5rem;
    }

    .step-label {
        font-size: 0.7rem;
    }
}

@media (max-width: 480px) {
    .step-indicator > div {
        margin: 0 0.25rem;
    }

    .step-circle {
        width: 2rem;
        height: 2rem;
        font-size: 0.8rem;
    }

    .step-line {
        width: 1.5rem;
    }
}
</style>