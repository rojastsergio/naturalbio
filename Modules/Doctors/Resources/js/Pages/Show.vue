<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Perfil del Doctor
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="md:flex">
            <!-- Información básica -->
            <div class="md:w-1/3 border-r border-gray-200 dark:border-gray-700 p-6">
              <div class="flex flex-col items-center text-center mb-6">
                <img 
                  :src="doctor?.user?.profile_photo_path || '/img/default-avatar.png'" 
                  :alt="doctor?.user?.name || 'Doctor'"
                  class="h-32 w-32 rounded-full object-cover mb-4"
                >
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ doctor?.user?.name || 'Doctor' }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ doctor?.user?.email || 'No disponible' }}</p>
                <div 
                  class="mt-2 px-2 py-1 text-xs font-semibold rounded-full" 
                  :class="doctor?.accepts_emergencies 
                    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' 
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                >
                  {{ doctor?.accepts_emergencies ? 'Acepta emergencias' : 'No acepta emergencias' }}
                </div>
              </div>

              <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Especialidad</h4>
                <p class="text-gray-900 dark:text-gray-100 mb-3">{{ doctor?.specialty || 'No especificada' }}</p>
                
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Número de Licencia</h4>
                <p class="text-gray-900 dark:text-gray-100 mb-3">{{ doctor?.license_number || 'No especificado' }}</p>
                
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Tarifa de Consulta</h4>
                <p class="text-gray-900 dark:text-gray-100 mb-3">{{ formatCurrency(doctor?.consultation_fee) }}</p>
              </div>

              <div class="mt-4 flex flex-col space-y-2">
                <Link
                  v-if="doctor?.id"
                  :href="route('doctors.edit', doctor.id)"
                  class="inline-flex justify-center items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:outline-none transition ease-in-out duration-150"
                >
                  Editar Perfil
                </Link>
                <Link
                  v-if="doctor?.id"
                  :href="route('doctors.availability', doctor.id)"
                  class="inline-flex justify-center items-center px-4 py-2 bg-naturalbio-azul border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-azul-dark focus:outline-none transition ease-in-out duration-150"
                >
                  Ver Disponibilidad
                </Link>
                <Link
                  v-if="doctor?.id"
                  :href="route('doctors.therapy-instructions', doctor.id)"
                  class="inline-flex justify-center items-center px-4 py-2 bg-naturalbio-dorado border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-dorado-dark focus:outline-none transition ease-in-out duration-150"
                >
                  Instrucciones Terapéuticas
                </Link>
              </div>
            </div>

            <!-- Contenido principal -->
            <div class="md:w-2/3 p-6">
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Biografía</h3>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                  <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                    {{ doctor?.biography || 'No hay biografía disponible para este doctor.' }}
                  </p>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Próximas Citas</h3>
                <div v-if="doctor?.appointments && doctor.appointments.length > 0">
                  <div class="overflow-hidden overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-md">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                      <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha y Hora</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Paciente</th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                          <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acción</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="appointment in doctor.appointments" :key="appointment.id">
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDateTime(appointment.start_time) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                              {{ appointment.patient?.user?.name || 'N/A' }}
                            </div>
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
                            <Link 
                              :href="route('appointments.show', appointment.id)" 
                              class="text-naturalbio-azul hover:text-naturalbio-azul-dark"
                            >
                              Ver
                            </Link>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div v-else class="text-gray-500 dark:text-gray-400 text-center py-4">
                  No hay citas programadas próximamente.
                </div>
              </div>

              <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Estadísticas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md text-center">
                    <div class="text-3xl font-bold text-naturalbio-verde">{{ safeStats.totalPatients }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Pacientes Totales</div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md text-center">
                    <div class="text-3xl font-bold text-naturalbio-verde">{{ safeStats.monthlyAppointments }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Citas este mes</div>
                  </div>
                  <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md text-center">
                    <div class="text-3xl font-bold text-naturalbio-verde">{{ safeStats.completionRate }}%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Tasa de finalización</div>
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
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  doctor: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    default: () => ({})
  }
});

// Estadísticas seguras con valores predeterminados
const safeStats = computed(() => {
  return {
    totalPatients: props.stats?.totalPatients || 0,
    monthlyAppointments: props.stats?.monthlyAppointments || 0,
    completionRate: props.stats?.completionRate || 0
  };
});

// Formatear moneda a formato guatemalteco
function formatCurrency(value) {
  if (value === null || value === undefined) return 'Q0.00';
  
  try {
    return new Intl.NumberFormat('es-GT', {
      style: 'currency',
      currency: 'GTQ',
      minimumFractionDigits: 2
    }).format(value);
  } catch (error) {
    console.error('Error al formatear moneda:', error);
    return `Q${Number(value || 0).toFixed(2)}`;
  }
}

// Formatear fecha y hora
function formatDateTime(dateTime) {
  if (!dateTime) return 'N/A';
  
  try {
    const date = new Date(dateTime);
    return date.toLocaleDateString('es-GT') + ' ' + 
      date.toLocaleTimeString('es-GT', { hour: '2-digit', minute: '2-digit' });
  } catch (error) {
    console.error('Error al formatear fecha y hora:', error);
    return dateTime;
  }
}

// Clases de estado para las citas
function statusClasses(status) {
  const classes = {
    'scheduled': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
    'confirmed': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
    'completed': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'cancelled': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
    'no-show': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
  };
  
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
}
</script>