<template>
    <AppLayout title="Recetas">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Recetas
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
              <div class="flex items-center">
                <div class="relative">
                  <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar recetas..."
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm mr-4"
                    @keyup.enter="searchRecetas"
                  />
                  <button
                    @click="searchRecetas"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-700 dark:text-gray-300"
                  >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                      />
                    </svg>
                  </button>
                </div>
  
                <select
                  v-model="filters.status"
                  class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm mr-4"
                  @change="searchRecetas"
                >
                  <option value="">Estado</option>
                  <option value="active">Activa</option>
                  <option value="inactive">Inactiva</option>
                </select>
              </div>
  
              <Link
                :href="route('prescriptions.create')"
                class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:bg-naturalbio-verde-dark active:bg-naturalbio-verde-dark focus:outline-none focus:ring-2 focus:ring-naturalbio-verde-light focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                  />
                </svg>
                Nueva Receta
              </Link>
            </div>
  
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Nº Receta
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Paciente
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Doctor
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Fecha
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Estado
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                  <tr v-for="prescription in prescriptions.data" :key="prescription.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ prescription.prescription_number || `RX-${prescription.id}` }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="ml-2">
                          <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ prescription.patient.name }} {{ prescription.patient.last_name }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">
                        {{ prescription.doctor?.user?.name || 'Sin doctor' }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ prescription.doctor?.specialty || '' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">
                        {{ formatDate(prescription.issue_date) }}
                      </div>
                      <div v-if="prescription.expiry_date" class="text-xs text-gray-500 dark:text-gray-400">
                        Expira: {{ formatDate(prescription.expiry_date) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                        :class="{
                          'bg-green-100 text-green-800': prescription.status === 'active',
                          'bg-gray-100 text-gray-800': prescription.status === 'inactive',
                        }"
                      >
                        {{ prescription.status === 'active' ? 'Activa' : 'Inactiva' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex items-center space-x-3">
                        <Link
                          :href="route('prescriptions.show', prescription.id)"
                          class="text-naturalbio-azul hover:text-naturalbio-azul-dark"
                        >
                          Ver
                        </Link>
                        <Link
                          :href="route('prescriptions.edit', prescription.id)"
                          class="text-naturalbio-azul hover:text-naturalbio-azul-dark"
                        >
                          Editar
                        </Link>
                        <button
                          @click="confirmDelete(prescription)"
                          class="text-red-600 hover:text-red-800"
                        >
                          Eliminar
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="prescriptions.data.length === 0">
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                      No hay recetas disponibles
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
  
            <div class="mt-6">
              <Pagination :links="prescriptions.links" />
            </div>
          </div>
        </div>
      </div>
  
      <!-- Confirmation Modal -->
      <ConfirmationModal
        :show="confirmingDeletion"
        @close="confirmingDeletion = false"
        :title="'Eliminar Receta'"
        :content="'¿Estás seguro que deseas eliminar esta receta? Esta acción no se puede deshacer.'"
        :button="'Eliminar'"
        @confirm="deleteReceta"
      />
    </AppLayout>
  </template>
  
  <script setup>
  import { ref, watch, computed } from 'vue';
  import { Link, router, usePage } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import Pagination from '@/Components/Pagination.vue';
  import ConfirmationModal from '@/Components/ConfirmationModal.vue';
  
  const props = defineProps({
    prescriptions: Object,
    filters: Object,
  });
  
  const search = ref(props.filters.search || '');
  const confirmingDeletion = ref(false);
  const prescriptionToDelete = ref(null);
  
  // Format date using the browser's locale
  function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-GT', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  }
  
  function searchRecetas() {
    router.get(
      route('prescriptions.index'),
      {
        search: search.value,
        status: props.filters.status,
      },
      {
        preserveState: true,
        replace: true,
      }
    );
  }
  
  function confirmDelete(prescription) {
    prescriptionToDelete.value = prescription;
    confirmingDeletion.value = true;
  }
  
  function deleteReceta() {
    if (prescriptionToDelete.value) {
      router.delete(route('prescriptions.destroy', prescriptionToDelete.value.id), {
        onSuccess: () => {
          confirmingDeletion.value = false;
          prescriptionToDelete.value = null;
        },
      });
    }
  }
  
  // Watch for changes in filters
  watch(
    () => props.filters.status,
    (newValue, oldValue) => {
      if (newValue !== oldValue) {
        searchRecetas();
      }
    }
  );
  </script>