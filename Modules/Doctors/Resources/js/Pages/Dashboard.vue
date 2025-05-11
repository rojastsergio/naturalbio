<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Panel de Control del Doctor
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Widgets de resumen -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <doctor-dashboard-widget
              title="Próximas Citas"
              :value="dashboardData.upcomingAppointments.length"
              suffix=" citas"
              type="info"
            >
              <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </template>
            </doctor-dashboard-widget>
  
            <doctor-dashboard-widget
              title="Pacientes Activos"
              :value="dashboardData.activePatients"
              suffix=" pacientes"
              type="success"
            >
              <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </template>
            </doctor-dashboard-widget>
  
            <doctor-dashboard-widget
              title="Ingresos del Mes"
              :value="dashboardData.monthlyIncome"
              :is-currency="true"
              type="default"
            >
              <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </template>
            </doctor-dashboard-widget>
          </div>
  
          <!-- Acciones rápidas -->
          <div class="flex flex-wrap gap-4 mb-6">
            <inertia-link
              :href="route('prescriptions.create')"
              class="bg-naturalbio-verde hover:bg-naturalbio-verde-dark text-white px-4 py-2 rounded-lg flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Nueva Receta
            </inertia-link>
  
            <inertia-link
              :href="route('doctor.emergency')"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Emergencia
            </inertia-link>
  
            <inertia-link
              :href="route('doctor.availability')"
              class="bg-naturalbio-azul hover:bg-naturalbio-azul-dark text-white px-4 py-2 rounded-lg flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Disponibilidad
            </inertia-link>
            
            <inertia-link
              :href="route('doctor.therapy-instructions')"
              class="bg-naturalbio-dorado hover:bg-naturalbio-dorado-dark text-white px-4 py-2 rounded-lg flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              Instrucciones Terapéuticas
            </inertia-link>
          </div>
  
          <!-- Citas de hoy -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Citas para Hoy
              </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
              <div v-if="dashboardData.todayAppointments.length === 0" class="text-gray-500 dark:text-gray-400">
                No hay citas programadas para hoy.
              </div>
              <div v-else>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hora</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Paciente</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                      <tr v-for="appointment in dashboardData.todayAppointments" :key="appointment.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                          {{ formatTime(appointment.start_time) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                          {{ appointment.patient.user.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                          {{ appointment.appointment_type?.name || 'General' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span 
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                            :class="statusClasses(appointment.status)"
                          >
                            {{ appointment.status }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <inertia-link 
                            :href="route('appointments.show', appointment.id)" 
                            class="text-naturalbio-azul hover:text-naturalbio-azul-dark"
                          >
                            Ver
                          </inertia-link>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Próximas citas -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Próximas Citas
              </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
              <div v-if="dashboardData.upcomingAppointments.length === 0" class="text-gray-500 dark:text-gray-400">
                No hay citas próximas programadas.
              </div>
              <div v-else>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha y Hora</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Paciente</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                      <tr v-for="appointment in dashboardData.upcomingAppointments" :key="appointment.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                          {{ formatDateTime(appointment.start_time) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                          {{ appointment.patient.user.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                          {{ appointment.appointment_type?.name || 'General' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <span 
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                            :class="statusClasses(appointment.status)"
                          >
                            {{ appointment.status }}
                          </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <inertia-link 
                            :href="route('appointments.show', appointment.id)" 
                            class="text-naturalbio-azul hover:text-naturalbio-azul-dark"
                          >
                            Ver
                          </inertia-link>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </app-layout>
  </template>
  
  <script>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import DoctorDashboardWidget from '../Components/DoctorDashboardWidget.vue';
  
  export default {
    components: {
      AppLayout,
      DoctorDashboardWidget
    },
    props: {
      doctor: Object,
      dashboardData: Object
    },
    methods: {
      formatTime(dateTime) {
        if (!dateTime) return '';
        
        const date = new Date(dateTime);
        return date.toLocaleTimeString('es-GT', { hour: '2-digit', minute: '2-digit' });
      },
      formatDateTime(dateTime) {
        if (!dateTime) return '';
        
        const date = new Date(dateTime);
        return date.toLocaleDateString('es-GT') + ' ' + 
          date.toLocaleTimeString('es-GT', { hour: '2-digit', minute: '2-digit' });
      },
      statusClasses(status) {
        const statusMap = {
          'scheduled': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
          'confirmed': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
          'completed': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
          'cancelled': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
          'no-show': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
          'emergency': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
        };
        
        return statusMap[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
      }
    }
  };
  </script>