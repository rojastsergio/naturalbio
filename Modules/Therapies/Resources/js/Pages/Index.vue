<template>
    <AppLayout title="Terapias">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Catálogo de Terapias
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Filtros y búsqueda -->
            <div class="flex flex-col md:flex-row justify-between mb-6">
              <div class="w-full md:w-1/3 mb-4 md:mb-0">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                  </div>
                  <input
                    type="search"
                    v-model="search"
                    @input="debounceSearch"
                    class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Buscar terapias..."
                  />
                </div>
              </div>
              
              <div class="flex gap-2">
                <select
                  v-model="status"
                  @change="filter"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                  <option value="">Todos los estados</option>
                  <option value="active">Activos</option>
                  <option value="inactive">Inactivos</option>
                </select>
                
                <Link
                  :href="route('therapies.create')"
                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <span class="mr-2">+</span> Nueva Terapia
                </Link>
              </div>
            </div>
  
            <!-- Lista de terapias -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="therapy in therapies.data"
                :key="therapy.id"
                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-md overflow-hidden"
              >
                <div class="p-4">
                  <div class="flex justify-between">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
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
                  
                  <p class="mt-2 text-gray-600 dark:text-gray-300 text-sm">
                    {{ therapy.description ? (therapy.description.length > 100 ? therapy.description.slice(0, 100) + '...' : therapy.description) : 'Sin descripción' }}
                  </p>
                  
                  <div class="mt-4 flex justify-between items-center">
                    <div>
                      <p class="text-gray-700 dark:text-gray-300 font-semibold">
                        Precio: Q {{ therapy.price || therapy.default_price }}
                      </p>
                      <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Duración: {{ therapy.default_duration }} min
                      </p>
                    </div>
                    
                    <div class="flex gap-2">
                      <Link
                        :href="route('therapies.show', therapy.id)"
                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                      >
                        Ver
                      </Link>
                      <Link
                        :href="route('therapies.edit', therapy.id)"
                        class="inline-flex items-center px-3 py-1.5 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                      >
                        Editar
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Paginación -->
            <div class="mt-6">
              <Pagination class="mt-6" :links="therapies.links" />
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref, watch, computed } from 'vue';
  import { Link, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import Pagination from '@/Components/Pagination.vue';
  
  const props = defineProps({
    therapies: Object,
    filters: Object,
  });
  
  const search = ref(props.filters.search || '');
  const status = ref(props.filters.status || '');
  
  const filter = () => {
    router.get(route('therapies.index'), {
      search: search.value,
      status: status.value,
    }, {
      preserveState: true,
      replace: true,
    });
  };
  
  // Debounce para no enviar demasiadas peticiones durante la búsqueda
  let timeout;
  const debounceSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      filter();
    }, 500);
  };
  </script>