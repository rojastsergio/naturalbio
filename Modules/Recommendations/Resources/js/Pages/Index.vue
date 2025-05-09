<template>
    <AppLayout title="Recomendaciones">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-title font-semibold text-xl text-naturalbio-verde leading-tight">
            Recomendaciones
          </h2>
          <Link
  v-if="$page.props.can && $page.props.can.create_recommendations"
  :href="route('recommendations.create')" 
  class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde/90 focus:bg-naturalbio-verde active:bg-naturalbio-verde focus:outline-none focus:ring-2 focus:ring-naturalbio-verde focus:ring-offset-2 transition ease-in-out duration-150">
  Crear Recomendación
</Link>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Filtros -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
            <form @submit.prevent="search" class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
                <input 
                  v-model="filters.search" 
                  type="text" 
                  id="search" 
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                  placeholder="Título o descripción">
              </div>
  
              <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                <select 
                  v-model="filters.type" 
                  id="type" 
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm">
                  <option value="">Todos</option>
                  <option value="supplement">Suplemento</option>
                  <option value="therapy">Terapia</option>
                  <option value="questionnaire">Cuestionario</option>
                  <option value="custom">Personalizado</option>
                </select>
              </div>
  
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                <select 
                  v-model="filters.status" 
                  id="status" 
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm">
                  <option value="">Todos</option>
                  <option value="active">Activa</option>
                  <option value="completed">Completada</option>
                  <option value="cancelled">Cancelada</option>
                </select>
              </div>
  
              <div class="md:col-span-3 flex justify-end">
                <button 
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde/90 focus:bg-naturalbio-verde active:bg-naturalbio-verde focus:outline-none focus:ring-2 focus:ring-naturalbio-verde focus:ring-offset-2 transition ease-in-out duration-150">
                  Buscar
                </button>
                <button 
                  @click="resetFilters"
                  type="button"
                  class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                  Limpiar
                </button>
              </div>
            </form>
          </div>
  
          <!-- Lista de Recomendaciones -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div v-if="recommendations.data.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
              No se encontraron recomendaciones.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
              <div v-for="recommendation in recommendations.data" :key="recommendation.id" 
                class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-start">
                  <h3 class="text-lg font-medium text-naturalbio-verde">{{ recommendation.title }}</h3>
                  <span 
                    :class="statusClasses(recommendation.status)" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ statusLabel(recommendation.status) }}
                  </span>
                </div>
                
                <div class="mt-2 flex items-center">
                  <span 
                    :class="typeClasses(recommendation.type)" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ typeLabel(recommendation.type) }}
                  </span>
                  <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                    Paciente: {{ recommendation.patient.name }} {{ recommendation.patient.last_name }}
                  </span>
                </div>
                
                <div class="mt-3">
                  <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mb-1">
                    <div 
                      class="bg-naturalbio-verde h-2.5 rounded-full" 
                      :style="{ width: recommendation.progress + '%' }"
                    ></div>
                  </div>
                  <div class="text-xs text-right text-gray-500 dark:text-gray-400">
                    Progreso: {{ recommendation.progress }}%
                  </div>
                </div>
                
                <div class="mt-4 flex justify-between">
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span v-if="recommendation.due_date">
                      Vence: {{ formatDate(recommendation.due_date) }}
                    </span>
                    <span v-else>Sin fecha de vencimiento</span>
                  </div>
                  
                  <div class="flex space-x-2">
                    <Link 
                      :href="route('recommendations.show', recommendation.id)" 
                      class="inline-flex items-center px-3 py-1 text-xs text-naturalbio-verde hover:text-naturalbio-verde/80 focus:outline-none focus:underline transition">
                      Ver detalles
                    </Link>
                    
                    <Link 
  v-if="$page.props.can && $page.props.can.update_recommendations"
  :href="route('recommendations.edit', recommendation.id)" 
  class="inline-flex items-center px-3 py-1 text-xs text-blue-600 hover:text-blue-800 focus:outline-none focus:underline transition">
  Editar
</Link>

<button 
  v-if="$page.props.can && $page.props.can.delete_recommendations"
  @click="confirmDelete(recommendation)"
  class="inline-flex items-center px-3 py-1 text-xs text-red-600 hover:text-red-800 focus:outline-none focus:underline transition">
  Eliminar
</button>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Paginación -->
            <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
              <Pagination :links="recommendations.links" />
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal de confirmación para eliminar -->
      <ConfirmationModal
        :show="confirmingRecommendationDeletion"
        @close="confirmingRecommendationDeletion = false">
        <template #title>
          ¿Eliminar Recomendación?
        </template>
        <template #content>
          ¿Está seguro que desea eliminar esta recomendación? Esta acción no se puede deshacer.
        </template>
        <template #footer>
          <button 
            @click="confirmingRecommendationDeletion = false" 
            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
            Cancelar
          </button>
          <button 
            @click="deleteConfirmed" 
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Eliminar
          </button>
        </template>
      </ConfirmationModal>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref, computed, watch } from 'vue';
  import { useForm, Link, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import Pagination from '@/Components/Pagination.vue';
  import ConfirmationModal from '@/Components/ConfirmationModal.vue';
  
  // Propiedades
  const props = defineProps({
    recommendations: Object,
    filters: Object,
  });
  
  // Estado
  const filters = ref({ ...props.filters });
  const confirmingRecommendationDeletion = ref(false);
  const recommendationToDelete = ref(null);
  
  // Métodos
  const search = () => {
    router.get(route('recommendations.index'), filters.value, {
      preserveState: true,
      replace: true,
    });
  };
  
  const resetFilters = () => {
    filters.value = {
      search: '',
      type: '',
      status: '',
    };
    search();
  };
  
  const confirmDelete = (recommendation) => {
    recommendationToDelete.value = recommendation;
    confirmingRecommendationDeletion.value = true;
  };
  
  const deleteConfirmed = () => {
    router.delete(route('recommendations.destroy', recommendationToDelete.value.id), {
      onSuccess: () => {
        confirmingRecommendationDeletion.value = false;
        recommendationToDelete.value = null;
      },
    });
  };
  
  // Formateo de fechas
  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
  };
  
  // Clases para tipos
  const typeClasses = (type) => {
    switch (type) {
      case 'supplement':
        return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100';
      case 'therapy':
        return 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100';
      case 'questionnaire':
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
      case 'custom':
        return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
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
        return 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
      case 'completed':
        return 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100';
      case 'cancelled':
        return 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
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
  
  // Observar cambios en los filtros
  watch(filters, (value) => {
    // Opcional: búsqueda automática al cambiar filtros
    // search();
  }, { deep: true });
  </script>