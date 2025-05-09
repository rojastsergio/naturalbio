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
    <div class="step-indicator-container overflow-x-auto overflow-y-visible">
        <div class="step-indicator flex items-center min-w-max">
            <div 
                v-for="(step, index) in steps" 
                :key="index"
                class="flex items-center"
            >
                <!-- Step bubble -->
                <div 
                    :class="[
                        'step-circle flex items-center justify-center',
                        index < currentStep ? 'step-complete' : 
                        index === currentStep ? 'step-active' : 'step-inactive'
                    ]"
                >
                    <span v-if="index < currentStep">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span v-else>{{ index + 1 }}</span>
                </div>
                
                <!-- Step line -->
                <div 
                    v-if="index < steps.length - 1"
                    :class="[
                        'step-line',
                        index < currentStep ? 'step-line-complete' : 'step-line-inactive'
                    ]"
                ></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.step-indicator-container {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    padding: 0.5rem 0;
    margin-bottom: 1rem;
}

.step-indicator {
    display: flex;
    align-items: center;
    padding: 0 0.5rem;
}

.step-circle {
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    flex-shrink: 0;
}

.step-active {
    background-color: #4CAF50;
    color: white;
    border: 2px solid #4CAF50;
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
    width: 2rem;
    height: 2px;
    margin: 0 0.25rem;
}

.step-line-complete {
    background-color: #4CAF50;
}

.step-line-inactive {
    background-color: #E5E7EB;
}

/* Responsive mejoras */
@media (max-width: 640px) {
    .step-circle {
        width: 1.75rem;
        height: 1.75rem;
        font-size: 0.75rem;
    }
    
    .step-line {
        width: 1.5rem;
    }
}
</style>