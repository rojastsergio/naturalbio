<template>
    <AppLayout :title="therapy.name">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalle de Terapia: {{ therapy.name }}
          </h2>
          <div class="flex gap-2">
            <Link
              :href="route('therapies.edit', therapy.id)"
              class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Editar
            </Link>
            <Link
              :href="route('therapies.therapy-instructions.create', { therapy_id: therapy.id })"
              class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
              Agregar Instrucción
            </Link>
          </div>
        </div>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Información general -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row md:justify-between mb-6">
              <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                  {{ therapy.name }}
                </h3>
                <span
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': therapy.status === 'active',
                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': therapy.status === 'inactive'
                  }"
                  class="text-xs font-medium px-2.5 py-0.5 rounded-full"
                >
                  {{ therapy.status === 'active' ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <div class="mt-4 md:mt-0 md:text-right">
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  Precio: Q {{ therapy.price || therapy.default_price }}
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                  Duración: {{ therapy.default_duration }} minutos
                </p>
              </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-700 py-4">
              <h4 class="font-semibold text-gray-900 dark:text-white mb-2">
                Descripción
              </h4>
              <p class="text-gray-600 dark:text-gray-300">
                {{ therapy.description || 'No hay descripción disponible.' }}
              </p>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-700 py-4">
              <h4 class="font-semibold text-gray-900 dark:text-white mb-2">
                Detalles adicionales
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Creada por:</span> {{ therapy.creator?.name || 'No disponible' }}
                  </p>
                  <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Fecha de creación:</span> {{ formatDate(therapy.created_at) }}
                  </p>
                </div>
                <div>
                  <p class="text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Última actualización:</span> {{ formatDate(therapy.updated_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Instrucciones terapéuticas -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Instrucciones terapéuticas
              </h3>
              <Link
                :href="route('therapies.therapy-instructions.create', { therapy_id: therapy.id })"
                class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <span class="mr-1">+</span> Agregar
              </Link>
            </div>
            
            <div v-if="therapy.instructions && therapy.instructions.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div
                v-for="instruction in therapy.instructions"
                :key="instruction.id"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
              >
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                  {{ instruction.title }}
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                  {{ instruction.description ? (instruction.description.length > 100 ? instruction.description.slice(0, 100) + '...' : instruction.description) : 'Sin descripción' }}
                </p>
                <div class="flex justify-between items-center mt-2">
                  <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ instruction.body_area ? `Área: ${instruction.body_area}` : '' }}
                  </span>
                  <Link
                    :href="route('therapies.therapy-instructions.show', instruction.id)"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                  >
                    Ver detalle
                  </Link>
                </div>
              </div>
            </div>
            
            <p v-else class="text-gray-500 dark:text-gray-400 italic">
              No hay instrucciones terapéuticas asociadas a esta terapia. Agrega una nueva instrucción para ayudar a los terapistas en la aplicación del tratamiento.
            </p>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  const props = defineProps({
    therapy: Object,
  });
  
  // Formatear fecha
  const formatDate = (dateString) => {
    if (!dateString) return 'No disponible';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-GT', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }).format(date);
  };
  </script>