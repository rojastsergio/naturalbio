<template>
    <div 
      class="appointment-card"
      :class="{ 
        'border-red-500': isEmergency,
        'bg-red-50': isEmergency
      }"
      @click="$emit('click', appointment)"
    >
      <div class="flex items-start">
        <div 
          class="w-2 h-full rounded-full mr-3 flex-shrink-0" 
          :style="{ backgroundColor: statusColor }"
        ></div>
        
        <div class="flex-grow">
          <div class="flex items-start justify-between">
            <div>
              <div class="flex items-center">
                <span class="font-semibold">{{ formatTime(appointment.start_time) }}</span>
                <span class="mx-1">-</span>
                <span>{{ formatTime(appointment.end_time) }}</span>
                
                <span 
                  v-if="isEmergency" 
                  class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"
                >
                  Emergencia
                </span>
              </div>
              
              <div class="text-sm font-medium mt-1">
                {{ appointment.patient.name }} {{ appointment.patient.last_name }}
              </div>
              
              <div v-if="appointment.appointment_type" class="text-xs text-gray-500 mt-0.5">
                {{ appointment.appointment_type.name }}
              </div>
              
              <div v-if="appointment.therapies && appointment.therapies.length > 0" class="text-xs text-gray-500 mt-1">
                <span>Terapias:</span>
                <span v-for="(therapy, index) in appointment.therapies" :key="therapy.id">
                  {{ index > 0 ? ', ' : '' }}{{ therapy.name }}
                </span>
              </div>
            </div>
            
            <div class="text-sm font-medium text-naturalbio-verde">
              {{ formatPrice(appointment.total_price) }}
            </div>
          </div>
          
          <div v-if="appointment.notes" class="mt-2 text-sm text-gray-600">
            {{ truncateNotes(appointment.notes) }}
          </div>
          
          <div class="mt-2 flex items-center justify-between">
            <div class="flex space-x-2">
              <span 
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                :class="statusClasses"
              >
                {{ statusLabel }}
              </span>
              
              <span 
                v-if="appointment.doctor" 
                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
              >
                Dr. {{ appointment.doctor.name }}
              </span>
            </div>
            
            <div class="flex space-x-2">
              <button 
                v-if="canShowActions && canEdit" 
                class="action-button"
                @click.stop="$emit('edit')"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </button>
              
              <button 
                v-if="canShowActions && canSendReminder" 
                class="action-button"
                @click.stop="$emit('send-reminder')"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import { format } from 'date-fns';
  
  const props = defineProps({
    appointment: {
      type: Object,
      required: true
    },
    canEdit: {
      type: Boolean,
      default: false
    },
    canSendReminder: {
      type: Boolean,
      default: false
    },
    showActions: {
      type: Boolean,
      default: true
    }
  });
  
  const emit = defineEmits(['click', 'edit', 'send-reminder']);
  
  // Computed properties
  const isEmergency = computed(() => {
    return props.appointment.emergency;
  });
  
  const statusColor = computed(() => {
    switch (props.appointment.status) {
      case 'scheduled':
        return '#4CAF50'; // Verde NaturalBIO
      case 'completed':
        return '#00ACC1'; // Azul Serenidad
      case 'cancelled':
        return '#546E7A'; // Gris Piedra
      case 'no-show':
        return '#F44336'; // Rojo (no está en la paleta pero es apropiado)
      default:
        return '#4CAF50';
    }
  });
  
  const statusLabel = computed(() => {
    switch (props.appointment.status) {
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
    switch (props.appointment.status) {
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
  
  const canShowActions = computed(() => {
    return props.showActions && (props.canEdit || props.canSendReminder);
  });
  
  // Methods
  function formatTime(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return format(date, 'HH:mm');
  }
  
  function formatPrice(price) {
    return `Q ${parseFloat(price || 0).toFixed(2)}`;
  }
  
  function truncateNotes(notes) {
    if (notes.length <= 100) {
      return notes;
    }
    return notes.substring(0, 100) + '...';
  }
  </script>
  
  <style scoped>
  .appointment-card {
    @apply bg-white rounded-lg p-4 shadow hover:shadow-md transition-shadow duration-200 border-l-4 border-transparent cursor-pointer;
  }
  
  .action-button {
    @apply text-gray-400 hover:text-naturalbio-verde p-1 rounded-full hover:bg-gray-100 focus:outline-none;
  }
  </style>