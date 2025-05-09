<template>
    <AppLayout :title="'Recomendaciones de ' + patient.name">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-title font-semibold text-xl text-naturalbio-verde leading-tight">
            Recomendaciones del Paciente
          </h2>
          <div class="flex">
            <Link
              :href="route('recommendations.create', { patient_id: patient.id })"
              class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde/90 focus:bg-naturalbio-verde active:bg-naturalbio-verde focus:outline-none focus:ring-2 focus:ring-naturalbio-verde focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
              Nueva Recomendación
            </Link>
            <Link
              :href="route('patients.show', patient.id)"
              class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
              Volver al Paciente
            </Link>
          </div>
        </div>
      </template>
      
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="mb-4 text-gray-600 dark:text-gray-400">
            {{ patient.name }} {{ patient.last_name }}
          </div>
          
          <!-- Filtros -->
          <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                <select
                  id="status"
                  v-model="filters.status"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                  @change="applyFilters"
                >
                  <option value="">Todos los estados</option>
                  <option value="active">Activas</option>
                  <option value="completed">Completadas</option>
                  <option value="cancelled">Canceladas</option>
                </select>
              </div>
            </div>
          </div>
  
          <!-- Tarjetas de Recomendaciones -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="recommendation in recommendations.data" :key="recommendation.id" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex justify-between items-start">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-naturalbio-verde dark:text-naturalbio-verde truncate" :title="recommendation.title">
                    {{ recommendation.title }}
                  </h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(recommendation.created_at) }}
                  </p>
                </div>
                <span :class="statusClasses(recommendation.status)">
                  {{ statusLabel(recommendation.status) }}
                </span>
              </div>
              <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:p-6">
                <div class="mb-4">
                  <span :class="typeClasses(recommendation.type)" class="mb-2 inline-block">
                    {{ typeLabel(recommendation.type) }}
                  </span>
                  
                  <div class="mt-4">
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mb-1">
                      <div 
                        class="bg-naturalbio-verde h-2.5 rounded-full" 
                        :style="{ width: recommendation.progress + '%' }"
                      ></div>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-right">{{ recommendation.progress }}%</div>
                  </div>
                </div>
                
                <div v-if="recommendation.tasks && recommendation.tasks.length > 0" class="mt-4">
                  <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tareas ({{ countCompletedTasks(recommendation.tasks) }}/{{ recommendation.tasks.length }})</h4>
                  <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 max-h-32 overflow-y-auto">
                    <li v-for="(task, index) in recommendation.tasks" :key="index" class="flex items-start">
                      <span class="mr-2" v-if="task.completed">✓</span>
                      <span class="mr-2" v-else>○</span>
                      <span :class="{ 'line-through': task.completed }">{{ task.title }}</span>
                    </li>
                  </ul>
                </div>
                
                <div class="mt-6 flex justify-end">
                  <Link 
                    :href="route('recommendations.show', recommendation.id)" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-naturalbio-verde bg-naturalbio-verde/10 hover:bg-naturalbio-verde/20 dark:bg-naturalbio-verde/20 dark:hover:bg-naturalbio-verde/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
                  >
                    Ver Detalles
                  </Link>
                </div>
              </div>
            </div>
            
            <div v-if="recommendations.data.length === 0" class="col-span-full">
              <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                No se encontraron recomendaciones para este paciente con los filtros aplicados.
              </div>
            </div>
          </div>
  
          <!-- Paginación -->
          <div class="mt-6">
            <Pagination :links="recommendations.links" />
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import Pagination from '@/Components/Pagination.vue';
  
  const props = defineProps({
    patient: Object,
    recommendations: Object,
    filters: Object,
  });
  
  // Estado local para los filtros
  const filters = ref({
    status: props.filters.status || '',
  });
  
  // Función para aplicar filtros
  const applyFilters = () => {
    router.get(route('patients.recommendations', props.patient.id), filters.value, {
      preserveState: true,
      replace: true,
    });
  };
  
  // Funciones auxiliares
  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
  };
  
  const countCompletedTasks = (tasks) => {
    if (!tasks) return 0;
    return tasks.filter(task => task.completed).length;
  };
  
  // Clases para tipos
  const typeClasses = (type) => {
    switch (type) {
      case 'supplement':
        return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
      case 'therapy':
        return 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
      case 'questionnaire':
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
      case 'custom':
        return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
    }
  };
  
  // Etiquetas para tipos
  const typeLabel = (type) => {
    switch (type) {
      case 'supplement':
        return 'Suplemento';
      case 'therapy':
        return 'Terapia';
      case 'questionnaire':
        return 'Cuestionario';
      case 'custom':
        return 'Personalizado';
      default:
        return 'Desconocido';
    }
  };
  
  // Clases para estados
  const statusClasses = (status) => {
    switch (status) {
      case 'active':
        return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
      case 'completed':
        return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
      case 'cancelled':
        return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100 px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
    }
  };
  
  // Etiquetas para estados
  const statusLabel = (status) => {
    switch (status) {
      case 'active':
        return 'Activa';
      case 'completed':
        return 'Completada';
      case 'cancelled':
        return 'Cancelada';
      default:
        return 'Desconocido';
    }
  };
  </script>