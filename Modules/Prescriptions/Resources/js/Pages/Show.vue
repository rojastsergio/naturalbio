<template>
    <AppLayout :title="`Receta: ${prescription.prescription_number || `RX-${prescription.id}`}`">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Receta: {{ prescription.prescription_number || `RX-${prescription.id}` }}
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Acciones -->
            <div class="flex justify-between items-center mb-6">
              <div class="flex items-center space-x-4">
                <Link
                  :href="route('prescriptions.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                  Volver
                </Link>
                
                <span
                  class="px-3 py-1 text-xs rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': prescription.status === 'active',
                    'bg-gray-100 text-gray-800': prescription.status === 'inactive',
                  }"
                >
                  {{ prescription.status === 'active' ? 'Activa' : 'Inactiva' }}
                </span>
              </div>
              
              <div class="flex items-center space-x-2">
                <button
                  @click="generatePdf"
                  class="inline-flex items-center px-3 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                  </svg>
                  Generar PDF
                </button>
                
                <button
                  @click="sendNotification"
                  :disabled="prescription.notification_sent"
                  class="inline-flex items-center px-3 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150"
                  :class="{
                    'bg-purple-500 text-white hover:bg-purple-600 focus:bg-purple-600 active:bg-purple-600 focus:ring-purple-500': !prescription.notification_sent,
                    'bg-gray-300 text-gray-500 cursor-not-allowed': prescription.notification_sent,
                  }"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                    />
                  </svg>
                  {{ prescription.notification_sent ? 'Notificación enviada' : 'Enviar notificación' }}
                </button>
                
                <Link
                  :href="route('prescriptions.edit', prescription.id)"
                  class="inline-flex items-center px-3 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    />
                  </svg>
                  Editar
                </Link>
              </div>
            </div>
  
            <!-- Información general -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Información del paciente</h3>
                <div class="space-y-2">
                  <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ prescription.patient.name }} {{ prescription.patient.last_name }}</span>
                  </div>
                  <div v-if="prescription.patient.expedient_number">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nº Expediente:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ prescription.patient.expedient_number }}</span>
                  </div>
                  <div v-if="prescription.patient.email">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ prescription.patient.email }}</span>
                  </div>
                  <div v-if="prescription.patient.phone">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ prescription.patient.phone }}</span>
                  </div>
                </div>
              </div>
  
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Información de la receta</h3>
                <div class="space-y-2">
                  <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Doctor:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ prescription.doctor?.user?.name || 'No disponible' }}</span>
                  </div>
                  <div v-if="prescription.doctor?.specialty">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Especialidad:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ prescription.doctor.specialty }}</span>
                  </div>
                  <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de emisión:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ formatDate(prescription.issue_date) }}</span>
                  </div>
                  <div v-if="prescription.expiry_date">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de expiración:</span>
                    <span class="ml-2 text-gray-900 dark:text-gray-100">{{ formatDate(prescription.expiry_date) }}</span>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Diagnóstico e instrucciones -->
            <div class="mb-8">
              <div v-if="prescription.diagnosis" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Diagnóstico</h3>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ prescription.diagnosis }}</p>
            </div>
            <div v-if="prescription.instructions" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Instrucciones generales</h3>
              <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ prescription.instructions }}</p>
            </div>
          </div>

          <!-- Items de la receta -->
          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Medicamentos / Suplementos</h3>
            
            <div v-if="prescription.items.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
              No hay medicamentos o suplementos en esta receta.
            </div>

            <div v-else class="space-y-4">
              <div 
                v-for="(item, index) in prescription.items" 
                :key="index"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
              >
                <div class="flex justify-between items-start">
                  <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100">
                    {{ item.name }}
                  </h4>
                  <span 
                    v-if="item.is_supplement" 
                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800"
                  >
                    Suplemento
                  </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                  <div v-if="item.dosage">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Dosificación:</span>
                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ item.dosage }}</span>
                  </div>
                  
                  <div v-if="item.frequency">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Frecuencia:</span>
                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ item.frequency }}</span>
                  </div>
                  
                  <div v-if="item.duration">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Duración:</span>
                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ item.duration }}</span>
                  </div>
                </div>
                
                <div v-if="item.posology" class="mt-3">
                  <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Posología completa:</span>
                  <p class="mt-1 text-gray-700 dark:text-gray-300">{{ item.posology }}</p>
                </div>
                
                <div v-if="item.instructions" class="mt-3">
                  <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Instrucciones adicionales:</span>
                  <p class="mt-1 text-gray-700 dark:text-gray-300">{{ item.instructions }}</p>
                </div>
                
                <div v-if="item.notes" class="mt-3">
                  <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Notas:</span>
                  <p class="mt-1 text-gray-700 dark:text-gray-300">{{ item.notes }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  prescription: Object
});

// Format date using the browser's locale
function formatDate(dateString) {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-GT', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function generatePdf() {
  router.post(route('prescriptions.pdf', props.prescription.id), {}, {
    onSuccess: (response) => {
      // Aquí podrías abrir la ventana con el PDF o redirigir a su URL
      if (response?.data?.pdf_url) {
        window.open(response.data.pdf_url, '_blank');
      }
    }
  });
}

function sendNotification() {
  if (props.prescription.notification_sent) return;
  
  router.post(route('prescriptions.notify', props.prescription.id), {}, {
    preserveScroll: true
  });
}
</script> 