<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Editar Cita
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <appointment-form
                :appointment-types="appointmentTypes"
                :doctors="doctors"
                :therapists="therapists"
                :therapies="therapies"
                :initial-data="appointment"
                @submit="updateAppointment"
              />
            </div>
          </div>
        </div>
      </div>
    </app-layout>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  import { router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import AppointmentForm from '../../../resources/js/Components/AppointmentForm.vue';
  
  const props = defineProps({
    appointment: {
      type: Object,
      required: true
    },
    appointmentTypes: {
      type: Array,
      required: true
    },
    doctors: {
      type: Array,
      required: true
    },
    therapists: {
      type: Array,
      default: () => []
    },
    therapies: {
      type: Array,
      default: () => []
    }
  });
  
  // Método para actualizar la cita
  function updateAppointment(formData) {
    router.patch(route('appointments.update', props.appointment.id), formData, {
      onSuccess: () => {
        // Redirección se maneja automáticamente por el controlador
      },
      onError: (errors) => {
        console.error('Errores al actualizar cita:', errors);
      }
    });
  }
  </script>