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
    // Mostrar un mensaje de carga
    const message = document.createElement('div');
    message.classList.add('fixed', 'inset-0', 'flex', 'items-center', 'justify-center', 'z-50', 'bg-black', 'bg-opacity-50');
    message.innerHTML = `
      <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
        <div class="flex items-center justify-center mb-4">
          <svg class="animate-spin h-8 w-8 text-emerald-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-lg font-semibold">Creando la cita...</p>
        </div>
      </div>
    `;
    document.body.appendChild(message);

    router.post(route('appointments.store'), formData, {
      onSuccess: () => {
        // Redirección se maneja automáticamente por el controlador
        document.body.removeChild(message);
      },
      onError: (errors) => {
        document.body.removeChild(message);

        // Mostrar un mensaje de error
        const errorMessageEl = document.createElement('div');
        errorMessageEl.classList.add('fixed', 'inset-0', 'flex', 'items-center', 'justify-center', 'z-50', 'bg-black', 'bg-opacity-50');

        let errorMessage = 'Hubo un error al crear la cita.';

        // Si hay un mensaje específico del servidor, usar ese
        if (errors.message) {
          errorMessage = errors.message;
        } else if (errors.start_time) {
          errorMessage = 'La fecha y hora de inicio son requeridas.';
        }

        errorMessageEl.innerHTML = `
          <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="ml-3 flex-1">
                <h3 class="text-lg font-medium text-gray-900">Error</h3>
                <div class="mt-2 text-gray-700">
                  <p>${errorMessage}</p>
                </div>
                <div class="mt-4">
                  <button type="button" id="closeErrorBtn" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Cerrar
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;

        document.body.appendChild(errorMessageEl);

        // Agregar evento al botón de cierre
        document.getElementById('closeErrorBtn').addEventListener('click', () => {
          document.body.removeChild(errorMessageEl);
        });

        console.error('Errores al crear cita:', errors);
      }
    });
  }
  </script>