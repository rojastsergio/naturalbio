<template>
    <AppLayout :title="'Recomendación: ' + recommendation.title">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-title font-semibold text-xl text-naturalbio-verde leading-tight">
            {{ recommendation.title }}
          </h2>
          <div class="flex">
            <Link v-if="$page.props.can.update_recommendations"
              :href="route('recommendations.edit', recommendation.id)"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
              Editar
            </Link>
            <Link
              :href="route('recommendations.index')"
              class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
              Volver
            </Link>
          </div>
        </div>
      </template>
      
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Detalles de la Recomendación -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Detalles de la Recomendación</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Información completa de la recomendación.</p>
              </div>
              <div>
                <span :class="statusClasses(recommendation.status)">
                  {{ statusLabel(recommendation.status) }}
                </span>
              </div>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700">
              <dl>
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Paciente</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2">
                    {{ recommendation.patient.name }} {{ recommendation.patient.last_name }}
                    <span class="text-gray-500 dark:text-gray-400 text-xs ml-2">(Exp: {{ recommendation.patient.expedient_number || 'N/A' }})</span>
                  </dd>
                </div>
                <div class="bg-white dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2">
                    <span :class="typeClasses(recommendation.type)">
                      {{ typeLabel(recommendation.type) }}
                    </span>
                    
                    <!-- Mostrar detalles del suplemento si aplica -->
                    <div v-if="recommendation.type === 'supplement' && recommendation.supplement" class="mt-2 p-3 bg-blue-50 dark:bg-blue-900 rounded-md">
                      <div class="font-semibold dark:text-blue-100">{{ recommendation.supplement.name }}</div>
                      <div class="text-sm mt-1 dark:text-blue-200" v-if="recommendation.supplement && recommendation.supplement.instructions">
                        {{ recommendation.supplement.instructions }}
                      </div>
                    </div>
                    
                    <!-- Mostrar detalles de la terapia si aplica -->
                    <div v-if="recommendation.type === 'therapy' && recommendation.therapy" class="mt-2 p-3 bg-purple-50 dark:bg-purple-900 rounded-md">
                      <div class="font-semibold dark:text-purple-100">{{ recommendation.therapy.name }}</div>
                      <div class="text-sm mt-1 dark:text-purple-200" v-if="recommendation.therapy && recommendation.therapy.description">
                        {{ recommendation.therapy.description }}
                      </div>
                    </div>
                  </dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Progreso</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2">
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mb-2">
                      <div 
                        class="bg-naturalbio-verde h-2.5 rounded-full" 
                        :style="{ width: recommendation.progress + '%' }"
                      ></div>
                    </div>
                    <div class="text-sm">{{ recommendation.progress }}% completado</div>
                  </dd>
                </div>
                <div class="bg-white dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Vencimiento</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2">
                    {{ recommendation.due_date ? formatDate(recommendation.due_date) : 'No especificada' }}
                  </dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aceptado por Paciente</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2">
                    <span :class="recommendation.accepted ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'" class="px-2 py-1 text-xs rounded-full">
                      {{ recommendation.accepted ? 'Aceptado' : 'Pendiente de aceptación' }}
                    </span>
                  </dd>
                </div>
                <div class="bg-white dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creado por</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2">
                    {{ recommendation.creator ? recommendation.creator.name : 'Usuario no disponible' }}
                    <span class="text-gray-500 dark:text-gray-400 text-xs ml-2">
                      ({{ formatDate(recommendation.created_at) }})
                    </span>
                  </dd>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="recommendation.notes">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notas</dt>
                  <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2">
                    {{ recommendation.notes }}
                  </dd>
                </div>
              </dl>
            </div>
          </div>
  
          <!-- Lista de Tareas -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Tareas Asignadas</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Lista de tareas para completar esta recomendación.</p>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">
              <div v-if="!recommendation.tasks || recommendation.tasks.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
                No hay tareas asignadas para esta recomendación.
              </div>
              <div v-for="(task, index) in recommendation.tasks" :key="index" class="p-4">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      :id="'task-' + index"
                      type="checkbox"
                      :checked="task.completed"
                      @change="toggleTask(index, $event.target.checked)"
                      class="focus:ring-naturalbio-verde h-4 w-4 text-naturalbio-verde border-gray-300 dark:border-gray-700 rounded"
                      :disabled="recommendation.status !== 'active'"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label :for="'task-' + index" class="font-medium text-gray-700 dark:text-gray-300" :class="{ 'line-through': task.completed }">
                      {{ task.title }}
                    </label>
                    <p class="text-gray-500 dark:text-gray-400 mt-1" v-if="task.description">{{ task.description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Head, Link, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  const props = defineProps({
    recommendation: Object,
  });
  
  // Funciones para formatear datos
  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
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
        return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full';
      case 'completed':
        return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full';
      case 'cancelled':
        return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full';
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
  
  // Alternar el estado completado de una tarea
  const toggleTask = (index, completed) => {
    router.post(route('recommendations.task', props.recommendation.id), {
      task_index: index,
      completed: completed
    }, {
      preserveState: true,
      preserveScroll: true,
    });
  };
  </script>