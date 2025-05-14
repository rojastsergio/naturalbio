<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gestión de Disponibilidad
        </h2>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Título y botón Nueva Disponibilidad -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-naturalbio-gris">Disponibilidad de {{ doctor ? doctor.name : 'Doctor' }}</h1>
            <button
              @click="showNewAvailabilityModal = true"
              class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md text-sm font-medium text-white hover:bg-naturalbio-verde-700 active:bg-naturalbio-verde-800 focus:outline-none focus:ring-2 focus:ring-naturalbio-verde-500 focus:ring-offset-2 transition-colors duration-150 shadow-sm"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Nueva Disponibilidad
            </button>
          </div>

          <!-- Leyenda de colores -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-6">
            <h3 class="text-lg font-medium text-naturalbio-gris mb-3">Leyenda</h3>
            <div class="flex flex-wrap gap-4">
              <div class="flex items-center">
                <div class="w-4 h-4 rounded-full mr-2" style="background-color: #8BC34A;"></div>
                <span class="text-sm text-gray-700">Disponible</span>
              </div>
              <div class="flex items-center">
                <div class="w-4 h-4 rounded-full mr-2" style="background-color: #4CAF50;"></div>
                <span class="text-sm text-gray-700">Cita Programada</span>
              </div>
              <div class="flex items-center">
                <div class="w-4 h-4 rounded-full mr-2" style="background-color: #2196F3;"></div>
                <span class="text-sm text-gray-700">Cita Completada</span>
              </div>
              <div class="flex items-center">
                <div class="w-4 h-4 rounded-full mr-2" style="background-color: #9E9E9E;"></div>
                <span class="text-sm text-gray-700">Cita Cancelada</span>
              </div>
              <div class="flex items-center">
                <div class="w-4 h-4 rounded-full mr-2" style="background-color: #F44336;"></div>
                <span class="text-sm text-gray-700">No Asistió</span>
              </div>
            </div>
          </div>

          <!-- Calendario -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <availability-calendar
              :availabilities="availabilities"
              :appointments="doctorAppointments"
              :allowEditing="true"
              @month-changed="fetchAvailabilities"
              @new-availability="showNewAvailabilityModal = true"
              @availability-create="addTimeSlot"
              @edit-availability="editAvailability"
              @appointment-click="viewAppointment"
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
      appointments: {
        type: Array,
        default: () => []
      },
      filters: Object
    },
    data() {
      return {
        showNewAvailabilityModal: false,
        editMode: false,
        currentAvailabilityId: null,
        doctorAppointments: this.appointments || [],
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
      viewAppointment(appointment) {
        // Redireccionar a la página de detalle de cita
        this.$inertia.visit(route('appointments.show', appointment.id));
      },
      fetchAvailabilities(date) {
        // Utilizar router para recargar con nuevos parámetros
        this.$inertia.visit(route('doctor.availability'), {
          data: {
            all_dates: 'true',  // Solicitar todos los datos sin filtro de fecha
            start_date: date,
            end_date: this.getEndDateFromStart(date)
          },
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
      addTimeSlot(slotData) {
        this.editMode = false;
        this.currentAvailabilityId = null;

        // El formato puede ser una fecha (del antiguo calendario) o un objeto con date, start_time, end_time
        let date, startTime, endTime;

        if (typeof slotData === 'string') {
          // Formato antiguo (solo recibe la fecha)
          date = slotData;
          startTime = '09:00';
          endTime = '10:00';
        } else {
          // Formato nuevo (recibe un objeto con toda la información)
          date = slotData.date;
          startTime = slotData.start_time ? slotData.start_time.substring(0, 5) : '09:00';
          endTime = slotData.end_time ? slotData.end_time.substring(0, 5) : '10:00';
        }

        this.availabilityForm = {
          doctor_id: this.doctor ? this.doctor.id : null,
          date: date,
          start_time: startTime,
          end_time: endTime,
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
              this.$inertia.reload({ only: ['availabilities'] });
            })
            .catch(error => {
              console.error('Error al actualizar disponibilidad:', error);
            });
        } else {
          // Crear nueva disponibilidad
          axios.post('/api/doctor-availabilities', this.availabilityForm)
            .then(() => {
              this.showNewAvailabilityModal = false;
              this.$inertia.reload({ only: ['availabilities'] });
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
              this.$inertia.reload({ only: ['availabilities'] });
            })
            .catch(error => {
              console.error('Error al eliminar disponibilidad:', error);
            });
        }
      }
    }
  };
  </script>