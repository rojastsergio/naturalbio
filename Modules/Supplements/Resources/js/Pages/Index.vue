<template>
    <AppLayout title="Suplementos">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-title font-semibold text-xl text-naturalbio-verde leading-tight">
            Suplementos
          </h2>
          <Link v-if="$page.props.can.create_supplements" 
            :href="route('supplements.create')" 
            class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde/90 focus:bg-naturalbio-verde active:bg-naturalbio-verde focus:outline-none focus:ring-2 focus:ring-naturalbio-verde focus:ring-offset-2 transition ease-in-out duration-150">
            Crear Suplemento
          </Link>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Filtros -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
            <form @submit.prevent="search" class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buscar</label>
                <input 
                  v-model="filters.search" 
                  type="text" 
                  id="search" 
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                  placeholder="Nombre o descripción">
              </div>
  
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                <select 
                  v-model="filters.status" 
                  id="status" 
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm">
                  <option value="">Todos</option>
                  <option value="active">Activo</option>
                  <option value="inactive">Inactivo</option>
                </select>
              </div>
  
              <div>
                <label for="price_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio Mínimo (Q)</label>
                <input 
                  v-model="filters.price_min" 
                  type="number" 
                  id="price_min" 
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                  min="0"
                  step="0.01">
              </div>
  
              <div>
                <label for="price_max" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio Máximo (Q)</label>
                <input 
                  v-model="filters.price_max" 
                  type="number" 
                  id="price_max" 
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                  min="0"
                  step="0.01">
              </div>
  
              <div class="md:col-span-4 flex justify-end">
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
  
          <!-- Lista de Suplementos -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div v-if="supplements.data.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
              No se encontraron suplementos.
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
              <div v-for="supplement in supplements.data" :key="supplement.id" 
                class="bg-white dark:bg-gray-700 rounded-lg shadow p-4 border border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-start">
                  <h3 class="text-lg font-medium text-naturalbio-verde">{{ supplement.name }}</h3>
                  <span 
                    :class="{ 
                      'bg-green-100 text-green-800': supplement.status === 'active', 
                      'bg-red-100 text-red-800': supplement.status === 'inactive' 
                    }" 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ supplement.status === 'active' ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
                
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                  {{ supplement.description || 'Sin descripción' }}
                </div>
                
                <div class="mt-4 flex justify-between">
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-bold text-naturalbio-verde">Q {{ supplement.price }}</span>
                  </div>
                  
                  <div class="flex space-x-2">
                    <Link 
                      :href="route('supplements.show', supplement.id)" 
                      class="inline-flex items-center px-3 py-1 text-xs text-naturalbio-verde hover:text-naturalbio-verde/80 focus:outline-none focus:underline transition">
                      Ver detalles
                    </Link>
                    
                    <Link 
                      v-if="$page.props.can.update_supplements"
                      :href="route('supplements.edit', supplement.id)" 
                      class="inline-flex items-center px-3 py-1 text-xs text-blue-600 hover:text-blue-800 focus:outline-none focus:underline transition">
                      Editar
                    </Link>
                    
                    <button 
                      v-if="$page.props.can.delete_supplements"
                      @click="confirmDelete(supplement)"
                      class="inline-flex items-center px-3 py-1 text-xs text-red-600 hover:text-red-800 focus:outline-none focus:underline transition">
                      Eliminar
                    </button>
                  </div>
                </div>
                
                <div v-if="supplement.media && supplement.media.length" class="mt-3">
                  <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">{{ supplement.media.length }} {{ supplement.media.length === 1 ? 'archivo' : 'archivos' }}</span>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Paginación -->
            <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
              <Pagination :links="supplements.links" />
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal de confirmación para eliminar -->
      <ConfirmationModal
        :show="confirmingSupplementDeletion"
        @close="confirmingSupplementDeletion = false">
        <template #title>
          ¿Eliminar Suplemento?
        </template>
        <template #content>
          ¿Está seguro que desea eliminar este suplemento? Esta acción no se puede deshacer.
        </template>
        <template #footer>
          <button 
            @click="confirmingSupplementDeletion = false" 
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
    supplements: Object,
    filters: Object,
  });
  
  // Estado
  const filters = ref({ ...props.filters });
  const confirmingSupplementDeletion = ref(false);
  const supplementToDelete = ref(null);
  
  // Métodos
  const search = () => {
    router.get(route('supplements.index'), filters.value, {
      preserveState: true,
      replace: true,
    });
  };
  
  const resetFilters = () => {
    filters.value = {
      search: '',
      status: '',
      price_min: '',
      price_max: '',
    };
    search();
  };
  
  const confirmDelete = (supplement) => {
    supplementToDelete.value = supplement;
    confirmingSupplementDeletion.value = true;
  };
  
  const deleteConfirmed = () => {
    router.delete(route('supplements.destroy', supplementToDelete.value.id), {
      onSuccess: () => {
        confirmingSupplementDeletion.value = false;
        supplementToDelete.value = null;
      },
    });
  };
  
  // Observar cambios en los filtros para actualizar la URL
  watch(filters, (value) => {
    // Opcional: búsqueda automática al cambiar filtros
    // search();
  }, { deep: true });
  </script>