<template>
    <AppLayout title="Crear Suplemento">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-title font-semibold text-xl text-naturalbio-verde leading-tight">
            Crear Suplemento
          </h2>
          <Link 
            :href="route('supplements.index')" 
            class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition">
            Volver a Suplementos
          </Link>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form @submit.prevent="submit" class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="col-span-2 md:col-span-1">
                  <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                  <input 
                    v-model="form.name" 
                    type="text" 
                    id="name" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                    required>
                  <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>
  
                <!-- Estado -->
                <div class="col-span-2 md:col-span-1">
                  <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                  <select 
                    v-model="form.status" 
                    id="status" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm">
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                  </select>
                  <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
                </div>
  
                <!-- Precio Default -->
                <div>
                  <label for="default_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio Predeterminado (Q)</label>
                  <input 
                    v-model="form.default_price" 
                    type="number" 
                    id="default_price" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                    min="0"
                    step="0.01"
                    required>
                  <div v-if="form.errors.default_price" class="text-red-500 text-xs mt-1">{{ form.errors.default_price }}</div>
                </div>
  
                <!-- Precio -->
                <div>
                  <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio Final (Q)</label>
                  <input 
                    v-model="form.price" 
                    type="number" 
                    id="price" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                    min="0"
                    step="0.01"
                    required>
                  <div v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</div>
                </div>
  
                <!-- Descripción -->
                <div class="col-span-2">
                  <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                  <textarea 
                    v-model="form.description" 
                    id="description" 
                    rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"></textarea>
                  <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                </div>
  
                <!-- Instrucciones de Uso -->
                <div class="col-span-2">
                  <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instrucciones de Uso</label>
                  <textarea 
                    v-model="form.instructions" 
                    id="instructions" 
                    rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"></textarea>
                  <div v-if="form.errors.instructions" class="text-red-500 text-xs mt-1">{{ form.errors.instructions }}</div>
                </div>
  
                <!-- Archivos Multimedia -->
                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Archivos Multimedia</label>
                  <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <svg 
                        class="mx-auto h-12 w-12 text-gray-400" 
                        stroke="currentColor" 
                        fill="none" 
                        viewBox="0 0 48 48" 
                        aria-hidden="true">
                        <path 
                          d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                          stroke-width="2" 
                          stroke-linecap="round" 
                          stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label 
                          for="media" 
                          class="relative cursor-pointer bg-white dark:bg-gray-900 rounded-md font-medium text-naturalbio-verde hover:text-naturalbio-verde/80 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-naturalbio-verde">
                          <span>Subir archivos</span>
                          <input 
                            id="media" 
                            name="media" 
                            type="file" 
                            class="sr-only" 
                            @change="addMedia"
                            multiple>
                        </label>
                        <p class="pl-1">o arrastra y suelta</p>
                      </div>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        PNG, JPG, GIF, PDF hasta 10MB
                      </p>
                    </div>
                  </div>
                  <div v-if="form.errors.media" class="text-red-500 text-xs mt-1">{{ form.errors.media }}</div>
  
                  <!-- Vista previa de archivos -->
                  <div v-if="mediaFiles.length > 0" class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div 
                      v-for="(file, index) in mediaFiles" 
                      :key="index" 
                      class="relative bg-gray-100 dark:bg-gray-700 p-2 rounded">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                          <svg v-if="isImage(file)" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          <svg v-else class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                          </svg>
                        </div>
                        <div class="ml-3 flex-1 overflow-hidden">
                          <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                            {{ file.name }}
                          </p>
                          <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatFileSize(file.size) }}
                          </p>
                        </div>
                      </div>
                      <button 
                        type="button" 
                        @click="removeMedia(index)" 
                        class="absolute top-1 right-1 text-gray-500 hover:text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
  
              <!-- Botones -->
              <div class="mt-6 flex justify-end space-x-3">
                <Link 
                  :href="route('supplements.index')" 
                  class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition">
                  Cancelar
                </Link>
                <button 
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde/90 focus:bg-naturalbio-verde active:bg-naturalbio-verde focus:outline-none focus:ring-2 focus:ring-naturalbio-verde focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                  {{ form.processing ? 'Guardando...' : 'Guardar Suplemento' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useForm, Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  // Estado del formulario
  const form = useForm({
    name: '',
    description: '',
    instructions: '',
    default_price: '',
    price: '',
    status: 'active',
    media: [], // Para enviar al servidor
  });
  
  // Estado local para manejar los archivos multimedia
  const mediaFiles = ref([]);
  
  // Métodos
  const addMedia = (event) => {
    const files = event.target.files;
    if (!files.length) return;
  
    // Agregar los archivos al formulario y a la vista previa
    for (let i = 0; i < files.length; i++) {
      form.media.push(files[i]);
      mediaFiles.value.push(files[i]);
    }
  };
  
  const removeMedia = (index) => {
    mediaFiles.value.splice(index, 1);
    form.media.splice(index, 1);
  };
  
  const isImage = (file) => {
    return file.type.startsWith('image/');
  };
  
  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };
  
  const submit = () => {
    form.post(route('supplements.store'), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset();
        mediaFiles.value = [];
      },
    });
  };
  </script>