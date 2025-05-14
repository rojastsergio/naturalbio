<template>
  <AppLayout title="Recetas">
    <div class="p-4">
      <!-- Filtros y búsqueda -->
      <div class="flex flex-col sm:flex-row mb-6 gap-4">
        <div class="relative flex-1">
          <input
            v-model="search"
            type="text"
            placeholder="Buscar recetas..."
            class="w-full rounded-md border-gray-300 focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50"
            @keyup.enter="searchRecetas"
          />
          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <button @click="searchRecetas" class="text-gray-500 hover:text-naturalbio-verde">
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
        </div>

        <select
          v-model="filters.status"
          class="w-full sm:w-40 rounded-md border-gray-300 focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50"
          @change="searchRecetas"
        >
          <option value="">Todos los estados</option>
          <option value="active">Activas</option>
          <option value="inactive">Inactivas</option>
        </select>

        <Link
          :href="route('prescriptions.create')"
          class="inline-flex justify-center items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition-colors duration-300"
        >
          <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Nueva Receta
        </Link>
      </div>

      <!-- Selector de vista -->
      <div class="flex items-center justify-end mb-4">
        <span class="text-sm text-gray-600 mr-2">Vista:</span>
        <button 
          @click="viewMode = 'cards'"
          :class="{'bg-naturalbio-verde text-white': viewMode === 'cards', 'bg-white text-gray-700': viewMode !== 'cards'}"
          class="px-3 py-1 rounded-l-md border border-gray-300 text-sm font-medium"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
          </svg>
        </button>
        <button 
          @click="viewMode = 'table'"
          :class="{'bg-naturalbio-verde text-white': viewMode === 'table', 'bg-white text-gray-700': viewMode !== 'table'}"
          class="px-3 py-1 rounded-r-md border border-gray-300 text-sm font-medium"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
          </svg>
        </button>
      </div>

      <!-- Vista de tarjetas -->
      <div v-if="viewMode === 'cards'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-for="prescription in prescriptions.data" :key="prescription.id" 
             class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300">
          
          <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex flex-col">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-naturalbio-verde" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="font-semibold text-gray-800">
                  {{ prescription.prescription_number || `RX-${prescription.id}` }}
                </h3>
              </div>
              <span class="text-xs text-gray-500 mt-1">
                Creada: {{ formatDate(prescription.issue_date) }}
              </span>
            </div>
            <span 
              class="px-2 py-1 text-xs rounded-full"
              :class="{
                'bg-green-100 text-green-800': prescription.status === 'active',
                'bg-gray-100 text-gray-800': prescription.status === 'inactive',
              }"
            >
              {{ prescription.status === 'active' ? 'Activa' : 'Inactiva' }}
            </span>
          </div>

          <div class="p-4 space-y-3">
            <!-- Paciente -->
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <div>
                <span class="font-medium text-gray-800">Paciente:</span>
                <span class="text-gray-700 ml-1">{{ prescription.patient.name }} {{ prescription.patient.last_name }}</span>
              </div>
            </div>
            
            <!-- Doctor -->
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <div>
                <span class="font-medium text-gray-800">Doctor:</span>
                <span class="text-gray-700 ml-1">{{ prescription.doctor?.user?.name || 'Sin doctor' }}</span>
              </div>
            </div>
            
            <!-- Especialidad -->
            <div v-if="prescription.doctor?.specialty" class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              <span class="text-gray-600">{{ prescription.doctor?.specialty }}</span>
            </div>
            
            <!-- Fecha de Expiración -->
            <div v-if="prescription.expiry_date" class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-gray-600">Expira: {{ formatDate(prescription.expiry_date) }}</span>
            </div>
            
            <!-- Número de medicamentos -->
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
              <span class="text-gray-600">{{ prescription.items?.length || 0 }} medicamentos</span>
            </div>
          </div>

          <div class="px-4 py-3 bg-gray-50 flex justify-between items-center border-t border-gray-200">
            <Link :href="route('prescriptions.show', prescription.id)" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
              Ver detalles
            </Link>
            <div class="flex space-x-3">
              <Link :href="route('prescriptions.edit', prescription.id)" class="text-sm text-green-600 hover:text-green-800">
                Editar
              </Link>
              <button @click.prevent="confirmDelete(prescription)" class="text-sm text-red-600 hover:text-red-800">
                Eliminar
              </button>
            </div>
          </div>
        </div>

        <!-- Estado vacío -->
        <div v-if="prescriptions.data.length === 0" class="col-span-full bg-white rounded-lg shadow p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-500">No hay recetas para mostrar</p>
          <Link
            :href="route('prescriptions.create')"
            class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-naturalbio-verde hover:bg-green-600"
          >
            Crear primera receta
          </Link>
        </div>
      </div>
      
      <!-- Vista de tabla (tradicional) -->
      <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Nº Receta
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Paciente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Doctor
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Fecha
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="prescription in prescriptions.data" :key="prescription.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  {{ prescription.prescription_number || `RX-${prescription.id}` }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="ml-2">
                      <div class="text-sm font-medium text-gray-900">
                        {{ prescription.patient.name }} {{ prescription.patient.last_name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ prescription.doctor?.user?.name || 'Sin doctor' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ prescription.doctor?.specialty || '' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ formatDate(prescription.issue_date) }}
                  </div>
                  <div v-if="prescription.expiry_date" class="text-xs text-gray-500">
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
                      class="text-blue-600 hover:text-blue-800"
                    >
                      Ver
                    </Link>
                    <Link
                      :href="route('prescriptions.edit', prescription.id)"
                      class="text-green-600 hover:text-green-800"
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
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  No hay recetas disponibles
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Paginación -->
      <div v-if="prescriptions.links" class="mt-4">
        <Pagination :links="prescriptions.links" />
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
const viewMode = ref(localStorage.getItem('prescriptionViewMode') || 'cards'); // Default to cards view

// Watch for changes in viewMode
watch(viewMode, (newValue) => {
  localStorage.setItem('prescriptionViewMode', newValue);
});

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

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>