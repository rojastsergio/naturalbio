<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Crear Nueva Cita
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
                :preselected-patient="patient"
                :default-date="defaultDate"
                @submit="createAppointment"
              />
            </div>
          </div>
        </div>
      </div>
    </app-layout>
  </template>
  
  <script setup>
  import { router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import AppointmentForm from 'Modules/Appointments/Resources/js/Components/AppointmentForm.vue';
  
  const props = defineProps({
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
    },
    patient: {
      type: Object,
      default: null
    },
    defaultDate: {
      type: String,
      required: true
    }
  });
  
  // Método para crear la cita
  function createAppointment(formData) {
    router.post(route('appointments.store'), formData, {
      onSuccess: () => {
        // Redirección se maneja automáticamente por el controlador
      },
      onError: (errors) => {
        console.error('Errores al crear cita:', errors);
      }
    });
  }
  </script>