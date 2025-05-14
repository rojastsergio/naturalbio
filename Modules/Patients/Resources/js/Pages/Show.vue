<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    patient: Object,
});

// Manejo seguro de propiedades
const patient = props.patient || {
    id: null,
    name: '',
    last_name: '',
    full_name: '',
    expedient_number: '',
    status: '',
    photo_path: null,
    signature_path: null,
    birthdate: null,
    age: null,
    gender: '',
    email: '',
    phone: '',
    whatsapp: '',
    address: '',
    municipality: null,
    medical_history: '',
    vital_signs: []
};

const tabs = ref([
    { id: 'info', name: 'Información General' },
    { id: 'vitals', name: 'Signos Vitales' },
    { id: 'history', name: 'Historial Médico' },
    { id: 'appointments', name: 'Citas' },
]);

const currentTab = ref('info');

const vitalSignForm = useForm({
    blood_pressure: '',
    oxygen_saturation: '',
    blood_glucose: '',
    heart_rate: '',
    respiratory_rate: '',
    height: '',
    weight: '',
    notes: '',
    recorded_at: new Date().toISOString().split('T')[0],
});

const confirmingDeletion = ref(false);
const selectedVital = ref(null);
const isSubmitting = ref(false);
const isDeleting = ref(false);
const errorMessage = ref('');

function submitVitalSigns() {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    errorMessage.value = '';

    vitalSignForm.post(route('vitals.store', patient.id), {
        preserveScroll: true,
        onSuccess: () => {
            vitalSignForm.reset();
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
}

function confirmDeleteVitalSign(vitalSign) {
    selectedVital.value = vitalSign;
    confirmingDeletion.value = true;
}

function deleteVitalSign() {
    if (!selectedVital.value || isDeleting.value) return;
    
    isDeleting.value = true;
    errorMessage.value = '';
    
    const vitalSignId = selectedVital.value.id;
    window.axios.delete(route('vitals.destroy', vitalSignId))
        .then(() => {
            window.location.reload();
        })
        .catch((error) => {
            console.error('Error deleting vital sign:', error);
            errorMessage.value = 'Error al eliminar el registro. Intente de nuevo.';
        })
        .finally(() => {
            isDeleting.value = false;
            confirmingDeletion.value = false;
        });
}

function formatDate(date) {
    if (!date) return '';
    try {
        return new Date(date).toLocaleDateString('es-GT', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (error) {
        console.error('Error formatting date:', error);
        return date; // Devuelve la fecha original en caso de error
    }
}

// Función para calcular IMC de manera segura
function calculateBMI(height, weight) {
    if (!height || !weight) return null;
    
    const heightMeters = parseFloat(height) / 100; // cm a metros
    const weightKg = parseFloat(weight);
    
    if (isNaN(heightMeters) || isNaN(weightKg) || heightMeters <= 0) return null;
    
    return (weightKg / (heightMeters * heightMeters)).toFixed(1);
}

// Funciones para obtener nombres de género y estado de forma segura
function getGenderName(genderCode) {
    const genderMap = {
        'male': 'Masculino',
        'female': 'Femenino',
        'other': 'Otro'
    };
    
    return genderMap[genderCode] || 'No registrado';
}

function getStatusName(statusCode) {
    return statusCode === 'active' ? 'Activo' : 'Inactivo';
}
</script>

<template>
    <AppLayout title="Detalle de Paciente">
        <Head :title="`Paciente - ${patient.full_name}`" />

        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ patient.full_name }}
                </h2>
                <div class="space-x-2">
                    <Link
                        v-if="patient.id"
                        :href="route('patients.edit', patient.id)"
                        class="inline-flex items-center px-4 py-2 bg-naturalbio-azul text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-azul transition ease-in-out duration-150"
                    >
                        Editar
                    </Link>
                    <Link
                        :href="route('patients.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150"
                    >
                        Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Expediente y estado -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Expediente:</span>
                            <span class="ml-2 text-lg font-semibold">{{ patient.expedient_number || 'No asignado' }}</span>
                        </div>
                        <div>
                            <span
                                :class="{
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': patient.status === 'active',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': patient.status === 'inactive'
                                }"
                                class="px-3 py-1 rounded-full text-sm font-medium"
                            >
                                {{ getStatusName(patient.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex overflow-x-auto">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="currentTab = tab.id"
                                :class="[
                                    currentTab === tab.id
                                        ? 'border-naturalbio-verde text-naturalbio-verde dark:text-naturalbio-verde'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                                    'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8'
                                ]"
                            >
                                {{ tab.name }}
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="mt-6">
                        <!-- Información General -->
                        <div v-if="currentTab === 'info'" class="space-y-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- Foto del paciente -->
                                <div class="md:w-1/4 flex flex-col items-center">
                                    <div v-if="patient.photo_path" class="mb-4">
                                        <img
                                            :src="`/storage/${patient.photo_path}`"
                                            :alt="patient.full_name"
                                            class="w-48 h-48 object-cover rounded-lg"
                                        >
                                    </div>
                                    <div v-else class="w-48 h-48 rounded-lg bg-naturalbio-verde dark:bg-naturalbio-verde text-white flex items-center justify-center text-4xl font-bold">
                                        {{ patient.name ? patient.name.charAt(0) : '' }}{{ patient.last_name ? patient.last_name.charAt(0) : '' }}
                                    </div>
                                    
                                    <div v-if="patient.signature_path" class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Firma:</h4>
                                        <img
                                            :src="`/storage/${patient.signature_path}`"
                                            alt="Firma"
                                            class="h-16"
                                        >
                                    </div>
                                </div>
                                
                                <!-- Información básica -->
                                <div class="md:w-3/4 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Nombre completo:</h4>
                                            <p class="mt-1 text-lg">{{ patient.full_name }}</p>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Fecha de nacimiento:</h4>
                                            <p class="mt-1">
                                                {{ patient.birthdate ? formatDate(patient.birthdate) : 'No registrada' }}
                                                <span v-if="patient.age" class="ml-2">({{ patient.age }} años)</span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Género:</h4>
                                            <p class="mt-1">{{ getGenderName(patient.gender) }}</p>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Email:</h4>
                                            <p class="mt-1">{{ patient.email || 'No registrado' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Teléfono:</h4>
                                            <p class="mt-1">{{ patient.phone || 'No registrado' }}</p>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">WhatsApp:</h4>
                                            <p class="mt-1">{{ patient.whatsapp || 'No registrado' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Dirección:</h4>
                                        <p class="mt-1">{{ patient.address || 'No registrada' }}</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Municipio:</h4>
                                            <p class="mt-1">{{ patient.municipality ? patient.municipality.name : 'No registrado' }}</p>
                                        </div>
                                        
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Departamento:</h4>
                                            <p class="mt-1">{{ patient.municipality && patient.municipality.department ? patient.municipality.department.name : 'No disponible' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Signos Vitales -->
                        <div v-if="currentTab === 'vitals'" class="space-y-6">
                            <!-- Formulario para agregar nuevos signos vitales -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Registrar Nuevos Signos Vitales</h3>
                                
                                <div v-if="errorMessage" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                    {{ errorMessage }}
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <div>
                                        <InputLabel for="blood_pressure" value="Presión Arterial (PA)" />
                                        <TextInput
                                            id="blood_pressure"
                                            v-model="vitalSignForm.blood_pressure"
                                            type="text"
                                            placeholder="Ej: 120/80"
                                            class="w-full mt-1"
                                        />
                                        <InputError :message="vitalSignForm.errors.blood_pressure" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <InputLabel for="oxygen_saturation" value="Saturación de Oxígeno (SpO₂)" />
                                        <TextInput
                                            id="oxygen_saturation"
                                            v-model="vitalSignForm.oxygen_saturation"
                                            type="number"
                                            placeholder="Ej: 98"
                                            class="w-full mt-1"
                                        />
                                        <InputError :message="vitalSignForm.errors.oxygen_saturation" class="mt-2" />
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                                    <div>
                                        <InputLabel for="blood_glucose" value="Glucosa en Sangre" />
                                        <TextInput
                                            id="blood_glucose"
                                            v-model="vitalSignForm.blood_glucose"
                                            type="number"
                                            class="w-full mt-1"
                                        />
                                        <InputError :message="vitalSignForm.errors.blood_glucose" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <InputLabel for="heart_rate" value="Frecuencia Cardíaca (FC)" />
                                        <TextInput
                                            id="heart_rate"
                                            v-model="vitalSignForm.heart_rate"
                                            type="number"
                                            placeholder="lpm"
                                            class="w-full mt-1"
                                        />
                                        <InputError :message="vitalSignForm.errors.heart_rate" class="mt-2" />
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                    <div>
                                        <InputLabel for="respiratory_rate" value="Frecuencia Respiratoria (FR)" />
                                        <TextInput
                                            id="respiratory_rate"
                                            v-model="vitalSignForm.respiratory_rate"
                                            type="number"
                                            placeholder="rpm"
                                            class="w-full mt-1"
                                        />
                                        <InputError :message="vitalSignForm.errors.respiratory_rate" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <InputLabel for="height" value="Estatura (cm)" />
                                        <TextInput
                                            id="height"
                                            v-model="vitalSignForm.height"
                                            type="number"
                                            class="w-full mt-1"
                                        />
                                        <InputError :message="vitalSignForm.errors.height" class="mt-2" />
                                    </div>
                                    
                                    <div>
                                        <InputLabel for="weight" value="Peso (kg)" />
                                        <TextInput
                                            id="weight"
                                            v-model="vitalSignForm.weight"
                                            type="number"
                                            class="w-full mt-1"
                                        />
                                        <InputError :message="vitalSignForm.errors.weight" class="mt-2" />
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <InputLabel for="recorded_at" value="Fecha de Registro" />
                                    <TextInput
                                        id="recorded_at"
                                        v-model="vitalSignForm.recorded_at"
                                        type="date"
                                        class="w-full mt-1"
                                    />
                                    <InputError :message="vitalSignForm.errors.recorded_at" class="mt-2" />
                                </div>
                                
                                <div class="mb-4">
                                    <InputLabel for="vital_notes" value="Notas" />
                                    <TextareaInput
                                        id="vital_notes"
                                        v-model="vitalSignForm.notes"
                                        class="w-full mt-1"
                                        rows="3"
                                    />
                                    <InputError :message="vitalSignForm.errors.notes" class="mt-2" />
                                </div>
                                
                                <div class="flex justify-end">
                                    <PrimaryButton
                                        @click="submitVitalSigns"
                                        :disabled="isSubmitting"
                                        :class="{ 'opacity-50': isSubmitting }"
                                    >
                                        <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ isSubmitting ? 'Procesando...' : 'Guardar Signos Vitales' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                            
                            <!-- Historial de signos vitales -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Historial de Signos Vitales</h3>
                                
                                <div v-if="!patient.vital_signs || patient.vital_signs.length === 0" class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No hay registros de signos vitales.</p>
                                </div>
                                
                                <div v-else class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Fecha
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    PA
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    SpO₂
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    FC
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    FR
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Altura/Peso
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    IMC
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Acción
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="vital in patient.vital_signs" :key="vital.id">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    {{ formatDate(vital.recorded_at) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    {{ vital.blood_pressure || '—' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    {{ vital.oxygen_saturation ? `${vital.oxygen_saturation}%` : '—' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    {{ vital.heart_rate ? `${vital.heart_rate} lpm` : '—' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    {{ vital.respiratory_rate ? `${vital.respiratory_rate} rpm` : '—' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    {{ vital.height && vital.weight ? `${vital.height} cm / ${vital.weight} kg` : '—' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                    {{ vital.bmi ? vital.bmi.toFixed(1) : calculateBMI(vital.height, vital.weight) || '—' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                    <button
                                                        @click="confirmDeleteVitalSign(vital)"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200"
                                                        :disabled="isDeleting"
                                                    >
                                                        Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Historial Médico -->
                        <div v-if="currentTab === 'history'" class="space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Historial Médico</h3>
                                
                                <div v-if="!patient.medical_history" class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No hay historial médico registrado.</p>
                                </div>
                                
                                <div v-else class="p-4 border rounded-lg border-gray-200 dark:border-gray-700">
                                    <p class="whitespace-pre-line">{{ patient.medical_history }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Citas -->
                        <div v-if="currentTab === 'appointments'" class="space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Historial de Citas</h3>
                                    
                                    <Link
                                        v-if="patient.id"
                                        :href="route('appointments.create', { patient_id: patient.id })"
                                        class="inline-flex items-center px-4 py-2 bg-naturalbio-verde text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde transition ease-in-out duration-150"
                                    >
                                        Nueva Cita
                                    </Link>
                                </div>
                                
                                <div class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No hay citas registradas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para eliminar signo vital -->
        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Confirmar Eliminación
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    ¿Está seguro que desea eliminar este registro de signos vitales?
                    Esta acción no se puede deshacer.
                </p>
                
                <div v-if="errorMessage" class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ errorMessage }}
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingDeletion = false" :disabled="isDeleting">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        @click="deleteVitalSign"
                        :disabled="isDeleting"
                    >
                        <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isDeleting ? 'Eliminando...' : 'Eliminar Registro' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>