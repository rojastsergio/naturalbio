<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Detalle de Cita
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex justify-between items-start mb-6">
                <div>
                  <h3 class="text-lg font-bold text-gray-900">
                    Cita #{{ appointment.id }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    Creada el {{ formatDateTime(appointment.created_at) }}
                    <span v-if="appointment.creator">
                      por {{ appointment.creator.name }}
                    </span>
                  </p>
                </div>
                
                <div class="flex space-x-3">
                  <Link
                    v-if="canEdit"
                    :href="route('appointments.edit', appointment.id)"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Editar
                  </Link>
                  
                  <button
                    v-if="canSendReminder"
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
                    @click="sendReminder"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Enviar Recordatorio
                  </button>
                  
                  <button
                    v-if="canDelete"
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    @click="confirmDelete"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                  </button>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <div class="bg-gray-50 rounded-lg p-4 shadow-sm">
                    <h4 class="font-medium text-gray-800 mb-3">Información del Paciente</h4>
                    
                    <div class="flex items-center mb-2">
                      <div v-if="appointment.patient.photo_path" class="mr-3">
                        <img :src="`/storage/${appointment.patient.photo_path}`" alt="Foto del paciente" class="h-12 w-12 rounded-full object-cover" />
                      </div>
                      <div>
                        <div class="font-semibold">{{ appointment.patient.name }} {{ appointment.patient.last_name }}</div>
                        <div class="text-sm text-gray-600">
                          {{ appointment.patient.expedient_number || 'Sin expediente' }}
                        </div>
                      </div>
                    </div>
                    
                    <div class="mt-2 text-sm">
                      <div class="flex items-center mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ appointment.patient.email || 'Sin correo' }}</span>
                      </div>
                      
                      <div class="flex items-center mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span>{{ appointment.patient.phone || 'Sin teléfono' }}</span>
                      </div>
                      
                      <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                        <span>{{ appointment.patient.whatsapp || 'Sin WhatsApp' }}</span>
                      </div>
                    </div>
                    
                    <div class="mt-3">
                      <Link
                        :href="route('patients.show', appointment.patient.id)"
                        class="text-sm text-naturalbio-verde hover:underline"
                      >
                        Ver perfil completo
                      </Link>
                    </div>
                  </div>
                </div>
                
                <div class="md:col-span-2">
                  <div class="bg-gray-50 rounded-lg p-4 shadow-sm mb-6">
                    <h4 class="font-medium text-gray-800 mb-3">Detalles de la Cita</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <div class="text-sm text-gray-500">Fecha y Hora</div>
                        <div class="font-medium">{{ formatDateTime(appointment.start_time) }}</div>
                      </div>
                      
                      <div>
                        <div class="text-sm text-gray-500">Duración</div>
                        <div class="font-medium">{{ getAppointmentDuration() }} minutos</div>
                      </div>
                      
                      <div>
                        <div class="text-sm text-gray-500">Estado</div>
                        <div class="flex items-center">
                          <span 
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="statusClasses"
                          >
                            {{ statusLabel }}
                          </span>
                          
                          <div class="ml-2" v-if="canChangeStatus">
                            <select
                              v-model="appointmentStatus"
                              class="mt-1 block w-full pl-3 pr-10 py-1 text-sm border-gray-300 focus:outline-none focus:ring-naturalbio-verde focus:border-naturalbio-verde rounded-md"
                              @change="updateStatus"
                            >
                              <option value="scheduled">Programada</option>
                              <option value="completed">Completada</option>
                              <option value="cancelled">Cancelada</option>
                              <option value="no-show">No asistió</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      
                      <div>
                        <div class="text-sm text-gray-500">Doctor</div>
                        <div class="font-medium">{{ appointment.doctor ? appointment.doctor.name : 'Sin asignar' }}</div>
                      </div>
                      
                      <div>
                        <div class="text-sm text-gray-500">Tipo de Cita</div>
                        <div class="font-medium">{{ appointment.type ? appointment.type.name : 'Sin tipo específico' }}</div>
                      </div>
                      
                      <div>
                        <div class="text-sm text-gray-500">Precio Total</div>
                        <div class="font-medium text-naturalbio-verde">Q {{ typeof appointment.total_price === 'number' ? appointment.total_price.toFixed(2) : (parseFloat(appointment.total_price) || 0).toFixed(2) }}</div>
                      </div>
                    </div>
                    
                    <div class="mt-4">
                      <div class="text-sm text-gray-500 mb-1">Notas</div>
                      <div class="bg-white rounded-md p-3 border border-gray-200">
                        <p class="text-gray-800">{{ appointment.notes || 'Sin notas' }}</p>
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="appointment.therapies && appointment.therapies.length > 0" class="bg-gray-50 rounded-lg p-4 shadow-sm">
                    <h4 class="font-medium text-gray-800 mb-3">Terapias</h4>
                    
                    <div class="divide-y divide-gray-200">
                      <div
                        v-for="therapy in appointment.therapies"
                        :key="therapy.id"
                        class="py-3"
                      >
                        <div class="flex justify-between">
                          <div>
                            <div class="font-medium">{{ therapy.name }}</div>
                            <div class="text-sm text-gray-500">
                              {{ therapy.pivot.duration }} minutos
                              <span v-if="therapy.pivot.therapist_id">
                                • Terapista: {{ getTherapistName(therapy.pivot.therapist_id) }}
                              </span>
                            </div>
                          </div>
                          <div class="font-medium text-naturalbio-verde">
                            Q {{ therapy.pivot && therapy.pivot.price ? parseFloat(therapy.pivot.price).toFixed(2) : '0.00' }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </app-layout>
  </template>
  
  <script setup>
  import { ref, computed } from 'vue';
  import { router, Link } from '@inertiajs/vue3';
  import { format, parseISO, differenceInMinutes } from 'date-fns';
  import { es } from 'date-fns/locale';
  import axios from 'axios';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  const props = defineProps({
    appointment: {
      type: Object,
      required: true
    },
    canEdit: {
      type: Boolean,
      default: false
    },
    canDelete: {
      type: Boolean,
      default: false
    },
    canSendReminder: {
      type: Boolean,
      default: false
    },
    canChangeStatus: {
      type: Boolean,
      default: false
    }
  });
  
  // Estado
  const appointmentStatus = ref(props.appointment.status);
  
  // Computed
  const statusLabel = computed(() => {
    switch (appointmentStatus.value) {
      case 'scheduled':
        return 'Programada';
      case 'completed':
        return 'Completada';
      case 'cancelled':
        return 'Cancelada';
      case 'no-show':
        return 'No asistió';
      default:
        return 'Programada';
    }
  });
  
  const statusClasses = computed(() => {
    switch (appointmentStatus.value) {
      case 'scheduled':
        return 'bg-green-100 text-green-800';
      case 'completed':
        return 'bg-blue-100 text-blue-800';
      case 'cancelled':
        return 'bg-gray-100 text-gray-800';
      case 'no-show':
        return 'bg-red-100 text-red-800';
      default:
        return 'bg-green-100 text-green-800';
    }
  });
  
  // Métodos
  function formatDateTime(dateTimeStr) {
    const date = parseISO(dateTimeStr);
    return format(date, 'EEEE, d MMMM yyyy HH:mm', { locale: es });
  }
  
  function getAppointmentDuration() {
    const startTime = parseISO(props.appointment.start_time);
    const endTime = parseISO(props.appointment.end_time);
    return differenceInMinutes(endTime, startTime);
  }
  
  function getTherapistName(therapistId) {
    // Esta función requeriría una lista de terapistas que no tenemos en props
    // En una implementación real, se podría obtener de la API o pasar como prop
    return `ID: ${therapistId}`;
  }
  
  function updateStatus() {
    axios.patch(route('appointments.update', props.appointment.id), {
      status: appointmentStatus.value
    })
      .then(response => {
        // Notificar actualización exitosa
      })
      .catch(error => {
        console.error('Error al actualizar estado:', error);
        // Restaurar estado anterior
        appointmentStatus.value = props.appointment.status;
      });
  }
  
  function sendReminder() {
    axios.post(route('api.appointments.reminder', props.appointment.id))
      .then(response => {
        if (response.data.success) {
          alert('Recordatorio enviado exitosamente');
        }
      })
      .catch(error => {
        console.error('Error al enviar recordatorio:', error);
        alert('Error al enviar recordatorio');
      });
  }
  
  function confirmDelete() {
    if (confirm('¿Está seguro que desea eliminar esta cita? Esta acción no se puede deshacer.')) {
      router.delete(route('appointments.destroy', props.appointment.id));
    }
  }
  </script>