<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gestión de Disponibilidad
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <availability-calendar 
              :availabilities="availabilities"
              @month-changed="fetchAvailabilities"
              @new-availability="showNewAvailabilityModal = true"
              @add-time-slot="addTimeSlot"
              @edit-availability="editAvailability"
            />
          </div>
        </div>
      </div>
  
      <!-- Modal para nueva disponibilidad -->
      <modal v-if="showNewAvailabilityModal" @close="showNewAvailabilityModal = false">
        <template #title>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ editMode ? 'Editar' : 'Nueva' }} Disponibilidad
          </h3>
        </template>
  
        <form @submit.prevent="saveAvailability">
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Fecha
            </label>
            <input 
              type="date" 
              v-model="availabilityForm.date" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
          </div>
  
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Hora de inicio
            </label>
            <input 
              type="time" 
              v-model="availabilityForm.start_time" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
          </div>
  
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Hora de fin
            </label>
            <input 
              type="time" 
              v-model="availabilityForm.end_time" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
          </div>
  
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Recurrencia
            </label>
            <select 
              v-model="availabilityForm.recurrence" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="">Sin recurrencia</option>
              <option value="daily">Diario</option>
              <option value="weekly">Semanal</option>
              <option value="biweekly">Quincenal</option>
              <option value="monthly">Mensual</option>
            </select>
          </div>
  
          <div class="mt-4" v-if="availabilityForm.recurrence">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Fecha de fin de recurrencia
            </label>
            <input 
              type="date" 
              v-model="availabilityForm.recurrence_end" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              :required="!!availabilityForm.recurrence"
            />
          </div>
  
          <div class="mt-4">
            <label class="flex items-center">
              <input 
                type="checkbox" 
                v-model="availabilityForm.active" 
                class="rounded border-gray-300 text-naturalbio-verde shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
              />
              <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Activo</span>
            </label>
          </div>
  
          <div class="flex justify-end mt-6">
            <button 
              type="button" 
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 mr-2 bg-gray-300 text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500"
              @click="showNewAvailabilityModal = false"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:outline-none transition ease-in-out duration-150"
            >
              {{ editMode ? 'Actualizar' : 'Guardar' }}
            </button>
            <button 
              v-if="editMode" 
              type="button" 
              class="inline-flex items-center px-4 py-2 ml-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition ease-in-out duration-150"
              @click="deleteAvailability"
            >
              Eliminar
            </button>
          </div>
        </form>
      </modal>
    </app-layout>
  </template>
  
  <script>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import Modal from '@/Components/Modal.vue';
  import AvailabilityCalendar from '../Components/AvailabilityCalendar.vue';
  import { useForm } from '@inertiajs/vue3';
  
  export default {
    components: {
      AppLayout,
      Modal,
      AvailabilityCalendar
    },
    props: {
      doctor: Object,
      availabilities: Array,
      filters: Object
    },
    data() {
      return {
        showNewAvailabilityModal: false,
        editMode: false,
        currentAvailabilityId: null,
        availabilityForm: {
          doctor_id: this.doctor ? this.doctor.id : null,
          date: '',
          start_time: '',
          end_time: '',
          recurrence: '',
          recurrence_end: '',
          active: true
        }
      };
    },
    methods: {
      fetchAvailabilities(date) {
        // Utilizar Inertia para recargar con nuevos parámetros
        this.$inertia.get(route('doctor.availability'), {
          start_date: date,
          end_date: this.getEndDateFromStart(date)
        }, {
          preserveState: true,
          preserveScroll: true
        });
      },
      getEndDateFromStart(startDate) {
        // Calcular un mes después
        const date = new Date(startDate);
        date.setMonth(date.getMonth() + 1);
        return date.toISOString().split('T')[0];
      },
      addTimeSlot(date) {
        this.editMode = false;
        this.currentAvailabilityId = null;
        this.availabilityForm = {
          doctor_id: this.doctor ? this.doctor.id : null,
          date: date,
          start_time: '09:00',
          end_time: '10:00',
          recurrence: '',
          recurrence_end: '',
          active: true
        };
        this.showNewAvailabilityModal = true;
      },
      editAvailability(availability) {
        this.editMode = true;
        this.currentAvailabilityId = availability.id;
        
        // Formatear fechas y horas para compatibilidad con inputs HTML
        const formatTime = (time) => {
          if (!time) return '';
          if (time instanceof Date) {
            return time.toTimeString().substring(0, 5);
          }
          if (typeof time === 'string' && time.includes('T')) {
            return new Date(time).toTimeString().substring(0, 5);
          }
          return time.substring(0, 5); // HH:MM
        };
        
        this.availabilityForm = {
          doctor_id: this.doctor ? this.doctor.id : null,
          date: availability.date,
          start_time: formatTime(availability.start_time),
          end_time: formatTime(availability.end_time),
          recurrence: availability.recurrence || '',
          recurrence_end: availability.recurrence_end || '',
          active: !!availability.active
        };
        
        this.showNewAvailabilityModal = true;
      },
      saveAvailability() {
        if (this.editMode) {
          // Actualizar disponibilidad existente
          axios.put(`/api/doctor-availabilities/${this.currentAvailabilityId}`, this.availabilityForm)
            .then(() => {
              this.showNewAvailabilityModal = false;
              this.$inertia.reload();
            })
            .catch(error => {
              console.error('Error al actualizar disponibilidad:', error);
            });
        } else {
          // Crear nueva disponibilidad
          axios.post('/api/doctor-availabilities', this.availabilityForm)
            .then(() => {
              this.showNewAvailabilityModal = false;
              this.$inertia.reload();
            })
            .catch(error => {
              console.error('Error al crear disponibilidad:', error);
            });
        }
      },
      deleteAvailability() {
        if (confirm('¿Está seguro de que desea eliminar esta disponibilidad?')) {
          axios.delete(`/api/doctor-availabilities/${this.currentAvailabilityId}`)
            .then(() => {
              this.showNewAvailabilityModal = false;
              this.$inertia.reload();
            })
            .catch(error => {
              console.error('Error al eliminar disponibilidad:', error);
            });
        }
      }
    }
  };
  </script>