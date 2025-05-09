<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    patient: Object,
});
</script>

<template>
    <Link
        :href="route('patients.show', patient.id)"
        class="block bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden"
    >
        <div class="p-4">
            <div class="flex items-center space-x-4">
                <div v-if="patient.photo_path" class="flex-shrink-0">
                    <img
                        :src="`/storage/${patient.photo_path}`"
                        :alt="patient.full_name"
                        class="w-16 h-16 rounded-full object-cover"
                    >
                </div>
                <div v-else class="flex-shrink-0 w-16 h-16 rounded-full bg-naturalbio-verde dark:bg-naturalbio-verde text-white flex items-center justify-center text-xl font-bold">
                    {{ patient.name.charAt(0) }}{{ patient.last_name.charAt(0) }}
                </div>
                
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white truncate">
                        {{ patient.full_name }}
                    </h3>
                    <p v-if="patient.expedient_number" class="text-sm text-gray-500 dark:text-gray-300">
                        #{{ patient.expedient_number }}
                    </p>
                </div>
                
                <div v-if="patient.status === 'inactive'" class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        Inactivo
                    </span>
                </div>
            </div>
            
            <div class="mt-4 space-y-2">
                <p v-if="patient.email" class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Email:</span> {{ patient.email }}
                </p>
                <p v-if="patient.whatsapp" class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">WA:</span> {{ patient.whatsapp }}
                </p>
                <p v-if="patient.age" class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Edad:</span> {{ patient.age }} años
                </p>
                
                <div v-if="patient.latest_vital_signs" class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Signos:</span>
                        <span v-if="patient.latest_vital_signs.blood_pressure" class="ml-1">
                            PA {{ patient.latest_vital_signs.blood_pressure }}
                        </span>
                        <span v-if="patient.latest_vital_signs.oxygen_saturation" class="ml-1">
                            SpO₂ {{ patient.latest_vital_signs.oxygen_saturation }}%
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </Link>
</template>