<template>
    <AppLayout :title="`Suplemento: ${supplement.name}`">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-title font-semibold text-xl text-naturalbio-verde leading-tight">
            Detalles del Suplemento
          </h2>
          <div class="flex space-x-2">
            <Link 
              :href="route('supplements.index')" 
              class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition">
              Volver a Lista
            </Link>
            <Link 
              v-if="$page.props.can.update_supplements"
              :href="route('supplements.edit', supplement.id)" 
              class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none transition">
              Editar
            </Link>
          </div>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Columna Izquierda: Información Principal -->
                <div class="md:col-span-2">
                  <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-semibold text-naturalbio-verde">{{ supplement.name }}</h3>
                    <span 
                      :class="{ 
                        'bg-green-100 text-green-800': supplement.status === 'active', 
                        'bg-red-100 text-red-800': supplement.status === 'inactive' 
                      }" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ supplement.status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                  </div>
  
                  <!-- Descripción -->
                  <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Descripción</h4>
                    <div class="text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-4 rounded">
                      {{ supplement.description || 'No hay descripción disponible.' }}
                    </div>
                  </div>
  
                  <!-- Instrucciones -->
                  <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Instrucciones de Uso</h4>
                    <div class="text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-4 rounded whitespace-pre-line">
                      {{ supplement.instructions || 'No hay instrucciones disponibles.' }}
                    </div>
                  </div>
  
                  <!-- Archivos Multimedia -->
                  <div v-if="supplement.media && supplement.media.length" class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Archivos Multimedia</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                      <div 
                        v-for="(mediaItem, index) in supplement.media" 
                        :key="index" 
                        class="relative bg-gray-100 dark:bg-gray-700 p-2 rounded">
                        <!-- Vista previa si es imagen -->
                        <a 
                          v-if="isImage(mediaItem)" 
                          :href="'/storage/' + mediaItem.path" 
                          target="_blank" 
                          class="block mb-2">
                          <img 
                            :src="'/storage/' + mediaItem.path" 
                            :alt="mediaItem.name" 
                            class="w-full h-32 object-cover rounded">
                        </a>
                        
                        <div class="flex items-center">
                          <div v-if="!isImage(mediaItem)" class="flex-shrink-0 h-10 w-10 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                          </div>
                          <div class="ml-3 flex-1 overflow-hidden">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                              {{ mediaItem.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                              {{ formatFileSize(mediaItem.size) }}
                            </p>
                          </div>
                        </div>
                        <a 
                          :href="'/storage/' + mediaItem.path" 
                          target="_blank" 
                          class="mt-2 block text-center text-xs text-naturalbio-verde hover:text-naturalbio-verde/80 transition">
                          Descargar
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
  
                <!-- Columna Derecha: Información Secundaria -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                  <!-- Precios -->
                  <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Información de Precios</h4>
                    <div class="space-y-2">
                      <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Precio Predeterminado:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">Q {{ supplement.default_price }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Precio Final:</span>
                        <span class="font-bold text-naturalbio-verde">Q {{ supplement.price }}</span>
                      </div>
                    </div>
                  </div>
  
                  <!-- Estadísticas de Uso (podrían ser implementadas después) -->
                  <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Estadísticas</h4>
                    <div class="space-y-2">
                      <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">En Recetas:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">0</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">En Recomendaciones:</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">0</span>
                      </div>
                    </div>
                  </div>
  
                  <!-- Información de Registro -->
                  <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Información de Registro</h4>
                    <div class="space-y-2 text-sm">
                      <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Creado:</span>
                        <span class="text-gray-800 dark:text-gray-200">{{ formatDate(supplement.created_at) }}</span>
                      </div>
                      <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-300">Última Actualización:</span>
                        <span class="text-gray-800 dark:text-gray-200">{{ formatDate(supplement.updated_at) }}</span>
                      </div>
                    </div>
                  </div>
  
                  <!-- Acciones -->
                  <div class="mt-8">
                    <button 
                      v-if="$page.props.can.delete_supplements"
                      @click="confirmDelete"
                      class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded transition">
                      Eliminar Suplemento
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal de confirmación para eliminar -->
      <ConfirmationModal 
        :show="confirmingDeletion" 
        @close="confirmingDeletion = false">
        <template #title>
          ¿Eliminar Suplemento?
        </template>
        <template #content>
          ¿Está seguro que desea eliminar este suplemento y todos sus archivos asociados? Esta acción no se puede deshacer.
        </template>
        <template #footer>
          <button 
            @click="confirmingDeletion = false" 
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
  import { ref } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import ConfirmationModal from '@/Components/ConfirmationModal.vue';
  
  // Propiedades
  const props = defineProps({
    supplement: Object,
  });
  
  // Estado
  const confirmingDeletion = ref(false);
  
  // Métodos
  const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-GT', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };
  
  const isImage = (mediaItem) => {
    return mediaItem.type && mediaItem.type.startsWith('image/');
  };
  
  const formatFileSize = (bytes) => {
    if (!bytes) return '0 Bytes';
    bytes = parseInt(bytes);
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };
  
  const confirmDelete = () => {
    confirmingDeletion.value = true;
  };
  
  const deleteConfirmed = () => {
    router.delete(route('supplements.destroy', props.supplement.id));
  };
  </script>