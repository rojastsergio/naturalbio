<template>
  <app-layout>
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Título y botón Nueva Cita -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-naturalbio-gris">Citas</h1>
          <Link
            :href="route('appointments.create')"
            class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md text-sm font-medium text-white hover:bg-naturalbio-verde-700 active:bg-naturalbio-verde-800 focus:outline-none focus:ring-2 focus:ring-naturalbio-verde-500 focus:ring-offset-2 transition-colors duration-150 shadow-sm"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nueva Cita
          </Link>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
              <h3 class="text-sm font-medium text-naturalbio-gris mb-1">Citas Programadas</h3>
              <p class="text-3xl font-semibold text-naturalbio-verde">{{ parseNumber(statistics.scheduled) }}</p>
            </div>
            <div class="h-12 w-12 bg-naturalbio-verde bg-opacity-10 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-naturalbio-verde" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>

          <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
              <h3 class="text-sm font-medium text-naturalbio-gris mb-1">Citas Completadas</h3>
              <p class="text-3xl font-semibold text-naturalbio-azul">{{ parseNumber(statistics.completed) }}</p>
            </div>
            <div class="h-12 w-12 bg-naturalbio-azul bg-opacity-10 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-naturalbio-azul" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>

          <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
              <h3 class="text-sm font-medium text-naturalbio-gris mb-1">Ingresos Totales</h3>
              <p class="text-3xl font-semibold text-naturalbio-dorado">Q {{ formatCurrency(statistics.total_revenue) }}</p>
            </div>
            <div class="h-12 w-12 bg-naturalbio-dorado bg-opacity-10 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-naturalbio-dorado" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Calendario de citas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
          <div class="p-6">
            <appointment-calendar
              :appointments="calendarAppointments"
              :appointment-types="appointmentTypes"
              :initial-view="calendarView"
              @date-change="handleCalendarDateChange"
              @appointment-click="viewAppointment"
            />
          </div>
        </div>

        <!-- Filtros de búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
          <div class="p-6">
            <div class="flex items-center mb-4">
              <svg class="w-5 h-5 mr-2 text-naturalbio-gris" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
              </svg>
              <span class="text-naturalbio-gris font-medium">Filtros</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
              <!-- Filtro por fecha -->
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <input
                  type="date"
                  v-model="localFilters.date"
                  class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-20"
                  @change="applyFilters"
                />
              </div>
              
              <!-- Filtro por estado -->
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                <select
                  v-model="localFilters.status"
                  class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-20"
                  @change="applyFilters"
                >
                  <option value="">Todos los estados</option>
                  <option value="scheduled">Programada</option>
                  <option value="completed">Completada</option>
                  <option value="cancelled">Cancelada</option>
                  <option value="no-show">No asistió</option>
                </select>
              </div>
              
              <!-- Filtro por paciente -->
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
                <input
                  type="text"
                  v-model="localFilters.search"
                  placeholder="Buscar paciente..."
                  class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-20"
                  @input="debouncedSearch"
                />
                <div v-if="isSearching" class="absolute inset-y-0 right-3 flex items-center">
                  <svg class="animate-spin h-4 w-4 text-naturalbio-verde" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
              </div>
              
              <!-- Filtro por doctor -->
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <select
                  v-model="localFilters.doctor_id"
                  class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-20"
                  @change="applyFilters"
                >
                  <option value="">Todos los doctores</option>
                  <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                    {{ doctor.name }}
                  </option>
                </select>
              </div>
            </div>
            
            <!-- Etiquetas de filtros activos -->
            <div class="flex flex-wrap gap-2" v-if="hasActiveFilters">
              <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-naturalbio-verde bg-opacity-10 text-naturalbio-verde" v-if="localFilters.date">
                <span>Fecha: {{ formatDate(localFilters.date) }}</span>
                <button @click="clearFilter('date')" class="ml-2 focus:outline-none">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              
              <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-naturalbio-verde bg-opacity-10 text-naturalbio-verde" v-if="localFilters.status">
                <span>Estado: {{ statusText[localFilters.status] }}</span>
                <button @click="clearFilter('status')" class="ml-2 focus:outline-none">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              
              <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-naturalbio-verde bg-opacity-10 text-naturalbio-verde" v-if="localFilters.search">
                <span>Paciente: {{ localFilters.search }}</span>
                <button @click="clearFilter('search')" class="ml-2 focus:outline-none">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              
              <div class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-naturalbio-verde bg-opacity-10 text-naturalbio-verde" v-if="localFilters.doctor_id">
                <span>Doctor: {{ getDoctorName(localFilters.doctor_id) }}</span>
                <button @click="clearFilter('doctor_id')" class="ml-2 focus:outline-none">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              
              <button 
                @click="clearAllFilters" 
                class="text-sm text-naturalbio-azul hover:text-naturalbio-azul-700 hover:underline focus:outline-none"
              >
                Limpiar todos
              </button>
            </div>
          </div>
        </div>

        <!-- Lista de citas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
          <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-medium text-naturalbio-gris">Lista de Citas</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-naturalbio-verde bg-opacity-10 text-naturalbio-verde">
              {{ parseNumber(appointments.total) }} citas
            </span>
          </div>
          
          <!-- Lista de tarjetas de citas -->
          <div class="p-4">
            <div v-if="!appointments.data || appointments.data.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No hay citas</h3>
              <p class="mt-1 text-sm text-gray-500">No hay citas que coincidan con los filtros seleccionados.</p>
            </div>
            
            <!-- Grid de citas -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div 
                v-for="appointment in appointments.data" 
                :key="appointment.id"
                class="border rounded-lg overflow-hidden transition-shadow duration-200 bg-white hover:shadow-md"
              >
                <!-- Cabecera de la tarjeta de cita con color según estado -->
                <div 
                  class="px-4 py-2 text-white text-sm font-medium"
                  :class="{
                    'bg-naturalbio-verde': appointment.status === 'scheduled',
                    'bg-naturalbio-azul': appointment.status === 'completed',
                    'bg-naturalbio-dorado': appointment.status === 'no-show',
                    'bg-naturalbio-gris': appointment.status === 'cancelled'
                  }"
                >
                  <div class="flex justify-between items-center">
                    <span>{{ formatDateTime(appointment.start_time) }}</span>
                    <span class="capitalize">{{ statusText[appointment.status] || appointment.status }}</span>
                  </div>
                </div>
                
                <!-- Contenido de la tarjeta -->
                <div class="p-4">
                  <!-- Datos del paciente -->
                  <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 mr-3">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <div>
                      <h3 class="font-medium text-naturalbio-gris">{{ appointment.patient?.name || 'Paciente no asignado' }}</h3>
                      <p class="text-sm text-gray-500">{{ appointment.type ? appointment.type.name : 'Sin tipo definido' }}</p>
                    </div>
                  </div>
                  
                  <!-- Información adicional -->
                  <div class="mb-3 text-sm text-gray-600">
                    <div class="flex items-start mb-1" v-if="appointment.doctor">
                      <svg class="w-4 h-4 mt-0.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      <span>Dr. {{ appointment.doctor.name }}</span>
                    </div>
                    
                    <div class="flex items-start mb-1" v-if="appointment.therapies && appointment.therapies.length">
                      <svg class="w-4 h-4 mt-0.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                      </svg>
                      <span>{{ appointment.therapies.length }} 
                        {{ appointment.therapies.length === 1 ? 'terapia' : 'terapias' }}
                      </span>
                    </div>
                    
                    <div class="flex items-start" v-if="appointment.price !== undefined && appointment.price !== null">
                      <svg class="w-4 h-4 mt-0.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span class="font-medium text-naturalbio-dorado">Q {{ formatCurrency(appointment.price) }}</span>
                    </div>
                  </div>
                  
                  <!-- Botones de acción -->
                  <div class="flex justify-between mt-4">
                    <button 
                      @click="viewAppointment(appointment)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-naturalbio-verde-700 bg-naturalbio-verde-100 hover:bg-naturalbio-verde-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde-500"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                      Ver
                    </button>
                    
                    <button 
                      v-if="hasPermission('update appointments')"
                      @click="editAppointment(appointment)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                      Editar
                    </button>
                    
                    <button 
                      v-if="hasPermission('send reminders') && appointment.status === 'scheduled'"
                      @click="sendReminder(appointment)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                      </svg>
                      Recordar
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Paginación -->
            <pagination v-if="appointments.links" :links="appointments.links" class="mt-6" />
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import debounce from 'lodash/debounce';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppointmentCalendar from 'Modules/Appointments/Resources/js/Components/AppointmentCalendar.vue';
import Pagination from '@/Components/Pagination.vue';

const page = usePage();

const props = defineProps({
  appointments: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({
      search: '',
      date: '',
      status: '',
      doctor_id: ''
    })
  },
  appointmentTypes: {
    type: Array,
    default: () => []
  },
  statistics: {
    type: Object,
    default: () => ({
      scheduled: 0,
      completed: 0,
      total_revenue: 0
    })
  },
  doctors: {
    type: Array,
    default: () => []
  }
});

// Estado
const localFilters = ref({ ...props.filters });
const isSearching = ref(false);
const calendarAppointments = ref([]);
const calendarMonth = ref('');
const calendarYear = ref('');
const calendarView = ref('timeGridDay'); // Cambio a la vista diaria de FullCalendar

// Constantes para mapeo
const statusText = {
  'scheduled': 'Programada',
  'completed': 'Completada',
  'cancelled': 'Cancelada',
  'no-show': 'No asistió'
};

// Funciones de utilidad para manejar tipos de datos
function parseNumber(value) {
  if (value === null || value === undefined) return 0;
  const parsedValue = parseInt(value, 10);
  return isNaN(parsedValue) ? 0 : parsedValue;
}

function formatCurrency(value) {
  if (value === null || value === undefined) return '0.00';
  const numValue = typeof value === 'number' ? value : Number(value);
  return isNaN(numValue) ? '0.00' : numValue.toFixed(2);
}

// Computed
const hasActiveFilters = computed(() => {
  return localFilters.value.date || 
         localFilters.value.status || 
         localFilters.value.search || 
         localFilters.value.doctor_id;
});

// Métodos de permisos
function hasPermission(permission) {
  return page.props.auth?.user?.permissions?.includes(permission) || false;
}

// Métodos
const debouncedSearch = debounce(() => {
  applyFilters();
}, 500);

function applyFilters() {
  isSearching.value = true;
  router.get(route('appointments.index'), localFilters.value, {
    preserveState: true,
    replace: true,
    onSuccess: () => {
      isSearching.value = false;
    }
  });
}

function clearFilter(filterName) {
  localFilters.value[filterName] = '';
  applyFilters();
}

function clearAllFilters() {
  Object.keys(localFilters.value).forEach(key => {
    localFilters.value[key] = '';
  });
  applyFilters();
}

function formatDate(dateString) {
  if (!dateString) return '';
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-GT', { day: '2-digit', month: '2-digit', year: 'numeric' });
  } catch (e) {
    console.error('Error al formatear fecha:', e);
    return dateString;
  }
}

function formatDateTime(dateTimeString) {
  if (!dateTimeString) return '';
  try {
    const date = new Date(dateTimeString);
    return date.toLocaleTimeString('es-GT', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    });
  } catch (e) {
    console.error('Error al formatear fecha y hora:', e);
    return dateTimeString;
  }
}

function getDoctorName(doctorId) {
  if (!props.doctors) return '';
  const doctor = props.doctors.find(doc => doc.id == doctorId);
  return doctor ? doctor.name : '';
}

function viewAppointment(appointment) {
  router.visit(route('appointments.show', appointment.id));
}

function editAppointment(appointment) {
  router.visit(route('appointments.edit', appointment.id));
}

function sendReminder(appointment) {
  // Crear elemento toast para notificaciones
  const toastId = 'toast-' + appointment.id;
  
  const toastElement = document.createElement('div');
  toastElement.id = toastId;
  toastElement.className = 'fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 z-50 flex items-center transition-opacity duration-300';
  toastElement.innerHTML = `
    <svg class="animate-spin h-5 w-5 text-naturalbio-verde mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <div>Enviando recordatorio...</div>
  `;
  document.body.appendChild(toastElement);
  
  axios.post(route('api.appointments.reminder', appointment.id))
    .then(response => {
      if (response.data.success) {
        toastElement.innerHTML = `
          <svg class="h-5 w-5 text-naturalbio-verde mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div>Recordatorio enviado exitosamente</div>
        `;
        
        setTimeout(() => {
          toastElement.classList.add('opacity-0');
          setTimeout(() => {
            document.body.removeChild(toastElement);
          }, 300);
        }, 3000);
      }
    })
    .catch(error => {
      console.error('Error al enviar recordatorio:', error);
      
      toastElement.innerHTML = `
        <svg class="h-5 w-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>Error al enviar recordatorio</div>
      `;
      
      setTimeout(() => {
        toastElement.classList.add('opacity-0');
        setTimeout(() => {
          document.body.removeChild(toastElement);
        }, 300);
      }, 3000);
    });
}

function handleCalendarDateChange(date) {
  calendarMonth.value = date.month;
  calendarYear.value = date.year;
  loadCalendarAppointments();
}

function loadCalendarAppointments() {
  if (!calendarMonth.value || !calendarYear.value) {
    return;
  }
  
  console.log('Cargando citas para el calendario:', calendarMonth.value, calendarYear.value);
  
  axios.get(route('appointments.index'), {
    params: {
      month: calendarMonth.value,
      year: calendarYear.value,
      format: 'calendar'
    }
  })
    .then(response => {
      console.log('Citas del calendario recibidas:', response.data.appointments);
      calendarAppointments.value = response.data.appointments || [];
    })
    .catch(error => {
      console.error('Error al cargar citas para el calendario:', error);
    });
}

// Cargar citas para el calendario al montar el componente
onMounted(() => {
  console.log('Componente montado, cargando calendario...');
  
  const today = new Date();
  calendarMonth.value = (today.getMonth() + 1).toString().padStart(2, '0');
  calendarYear.value = today.getFullYear().toString();
  
  // Establecer por defecto la vista diaria de FullCalendar
  calendarView.value = 'timeGridDay';
  
  // Cargar citas para el calendario
  loadCalendarAppointments();
});
</script>

<style scoped>
/* Estilos generales */
.rounded-lg {
  border-radius: 0.5rem;
}

/* Estilos para las tarjetas */
.card-hover {
  transition: all 0.2s ease;
}

.card-hover:hover {
  transform: translateY(-2px);
}

/* Transiciones para elementos */
.transition-opacity {
  transition-property: opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Mejoras para botones y controles */
button:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
}

input:focus, select:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
}
</style>