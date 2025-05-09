<template>
    <div class="feed-container">
      <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          Registro de Progreso
        </h3>
        
        <div class="flex items-center mt-2">
          <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div
              class="bg-green-600 h-2.5 rounded-full"
              :style="{ width: `${overallProgress}%` }"
            ></div>
          </div>
          <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ overallProgress }}%
          </span>
        </div>
      </div>
      
      <!-- Formulario para registrar nuevo progreso -->
      <form @submit.prevent="submitProgress" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <InputLabel for="progress_percentage" value="Porcentaje de progreso" />
            <div class="flex items-center mt-1">
              <input
                id="progress_percentage"
                type="range"
                min="0"
                max="100"
                v-model="form.progress_percentage"
                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
              />
              <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ form.progress_percentage }}%
              </span>
            </div>
            <span v-if="errors.progress_percentage" class="text-sm text-red-600 dark:text-red-400">
              {{ errors.progress_percentage }}
            </span>
          </div>
          
          <div>
            <InputLabel for="status" value="Estado" />
            <select
              id="status"
              v-model="form.status"
              class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            >
              <option value="not_started">No iniciado</option>
              <option value="in_progress">En progreso</option>
              <option value="completed">Completado</option>
              <option value="cancelled">Cancelado</option>
            </select>
            <span v-if="errors.status" class="text-sm text-red-600 dark:text-red-400">
              {{ errors.status }}
            </span>
          </div>
        </div>
        
        <div>
          <InputLabel for="notes" value="Notas" />
          <textarea
            id="notes"
            v-model="form.notes"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            rows="3"
            placeholder="Agregar notas sobre el progreso..."
          ></textarea>
          <span v-if="errors.notes" class="text-sm text-red-600 dark:text-red-400">
            {{ errors.notes }}
          </span>
        </div>
        
        <div class="flex justify-end mt-4">
          <button
            type="submit"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
            :disabled="loading"
          >
            <span v-if="loading" class="mr-2">
              <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            Registrar progreso
          </button>
        </div>
      </form>
      
      <!-- Feed de progreso -->
      <div v-if="progresses.length" class="space-y-4">
        <div
          v-for="progress in progresses"
          :key="progress.id"
          class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 border-l-4"
          :class="{
            'border-yellow-500': progress.status === 'not_started',
            'border-blue-500': progress.status === 'in_progress',
            'border-green-500': progress.status === 'completed',
            'border-red-500': progress.status === 'cancelled'
          }"
        >
          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': progress.status === 'not_started',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': progress.status === 'in_progress',
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': progress.status === 'completed',
                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': progress.status === 'cancelled'
                  }"
                  class="text-xs font-medium px-2.5 py-0.5 rounded-full"
                >
                  {{ getStatusLabel(progress.status) }}
                </span>
                <span class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                  {{ progress.progress_percentage }}% completado
                </span>
              </div>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ formatDate(progress.recorded_at) }}
              </p>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
              Por: {{ progress.recorded_by?.name || 'Sistema' }}
            </div>
          </div>
          
          <p v-if="progress.notes" class="mt-2 text-gray-600 dark:text-gray-300">
            {{ progress.notes }}
          </p>
        </div>
      </div>
      
      <p v-else class="text-gray-500 dark:text-gray-400 italic text-center py-4">
        No hay registros de progreso aún.
      </p>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import axios from 'axios';
  import InputLabel from '@/Components/InputLabel.vue';
  
  const props = defineProps({
    assignmentId: {
      type: [Number, String],
      required: true
    },
    initialProgress: {
      type: Array,
      default: () => []
    }
  });
  
  const progresses = ref(props.initialProgress || []);
  const loading = ref(false);
  const errors = ref({});
  
  const form = ref({
    progress_percentage: 0,
    status: 'not_started',
    notes: ''
  });
  
  const overallProgress = computed(() => {
    if (progresses.value.length === 0) return 0;
    const latest = progresses.value[0];
    return latest.progress_percentage || 0;
  });
  
  const getStatusLabel = (status) => {
    switch (status) {
      case 'not_started': return 'No iniciado';
      case 'in_progress': return 'En progreso';
      case 'completed': return 'Completado';
      case 'cancelled': return 'Cancelado';
      default: return status;
    }
  };
  
  const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-GT', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(date);
  };
  
  const loadProgresses = async () => {
    try {
      loading.value = true;
      const response = await axios.get(`/api/therapy-assignments/${props.assignmentId}/progress`);
      progresses.value = response.data.progresses || [];
      
      // Si hay progreso, inicializar el formulario con el último valor
      if (progresses.value.length > 0) {
        const latest = progresses.value[0];
        form.value.progress_percentage = latest.progress_percentage;
        form.value.status = latest.status;
      }
    } catch (error) {
      console.error('Error loading progresses:', error);
    } finally {
      loading.value = false;
    }
  };
  
  const submitProgress = async () => {
    try {
      loading.value = true;
      errors.value = {};
      
      const response = await axios.post(`/api/therapy-assignments/${props.assignmentId}/progress`, form.value);
      
      // Agregar el nuevo progreso al principio del arreglo
      progresses.value = [response.data.progress, ...progresses.value];
      
      // Resetear notas pero mantener porcentaje y estado
      form.value.notes = '';
      
    } catch (error) {
      if (error.response && error.response.data && error.response.data.errors) {
        errors.value = error.response.data.errors;
      } else {
        console.error('Error submitting progress:', error);
      }
    } finally {
      loading.value = false;
    }
  };
  
  onMounted(() => {
    loadProgresses();
  });
  </script>