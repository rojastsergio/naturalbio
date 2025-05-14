<template>
  <AppLayout title="Pacientes">
    <div class="p-4">
      <!-- Barra de búsqueda y botón -->
      <div class="flex flex-col sm:flex-row mb-6 gap-4">
        <div class="relative flex-1">
          <input
            type="text"
            v-model="search"
            placeholder="Buscar pacientes..."
            class="w-full rounded-md border-gray-300 focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50"
            @keyup.enter="searchPatients"
          />
          <button
            @click="searchPatients"
            class="absolute right-0 top-0 h-full px-4 bg-naturalbio-verde text-white rounded-r-md hover:bg-green-600"
          >
            Buscar
          </button>
        </div>
        
        <Link
          :href="route('patients.create')"
          class="inline-flex items-center justify-center px-4 py-2 bg-naturalbio-verde text-white rounded-md hover:bg-green-600 transition-colors duration-300"
        >
          <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nuevo Paciente
        </Link>
      </div>

      <!-- Vista de tarjetas -->
      <div v-if="viewMode === 'cards'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-for="patient in patients.data" :key="patient.id" 
             class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300">
          
          <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold">
                {{ getInitials(patient.name + ' ' + (patient.last_name || '')) }}
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">
                  {{ patient.name }} {{ patient.last_name }}
                </h3>
                <p class="text-xs text-gray-500">
                  # {{ patient.expedient_number || 'Sin expediente' }}
                </p>
              </div>
            </div>
            <span 
              class="px-2 py-1 text-xs rounded-full"
              :class="patient.status === 'active' || patient.status === 'Activo' 
                ? 'bg-green-100 text-green-800' 
                : 'bg-red-100 text-red-800'"
            >
              {{ patient.status === 'active' || patient.status === 'Activo' ? 'Activo' : 'Inactivo' }}
            </span>
          </div>

          <div class="p-4 space-y-2">
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
              <span class="text-gray-600">{{ patient.email || 'No registrado' }}</span>
            </div>
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
              <span class="text-gray-600">{{ patient.phone || 'No registrado' }}</span>
            </div>
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span class="text-gray-600">Última visita: {{ patient.last_visit || 'No registrada' }}</span>
            </div>
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span class="text-gray-600">Edad: {{ patient.age ? patient.age + ' años' : 'No registrada' }}</span>
            </div>
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span class="text-gray-600">Registro: {{ formatDate(patient.created_at) }}</span>
            </div>
          </div>

          <div class="px-4 py-3 bg-gray-50 flex justify-between items-center border-t border-gray-200">
            <Link :href="route('patients.show', patient.id)" class="text-sm text-blue-600 hover:text-blue-800">
              Ver detalles
            </Link>
            <div class="flex space-x-2">
              <Link :href="route('patients.edit', patient.id)" class="text-sm text-green-600 hover:text-green-800">
                Editar
              </Link>
              <button @click.prevent="confirmDelete(patient)" class="text-sm text-red-600 hover:text-red-800">
                Eliminar
              </button>
            </div>
          </div>
        </div>

        <div v-if="patients.data.length === 0" class="col-span-full bg-white rounded-lg shadow p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-500">No hay pacientes para mostrar</p>
          <Link
            :href="route('patients.create')"
            class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-naturalbio-verde hover:bg-green-600"
          >
            Agregar primer paciente
          </Link>
        </div>
      </div>

      <!-- Vista de tabla (tradicional) -->
      <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última visita</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="patient in patients.data" :key="patient.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                      {{ getInitials(patient.name) }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ patient.name }} {{ patient.last_name }}</div>
                      <div class="text-sm text-gray-500">{{ patient.expedient_number || 'Sin expediente' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ patient.email || '-' }}</div>
                  <div class="text-sm text-gray-500">{{ patient.phone || '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ patient.age ? patient.age + ' años' : 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ patient.last_visit || 'No registrada' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="patient.status === 'active' || patient.status === 'Activo' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'"
                  >
                    {{ patient.status === 'active' || patient.status === 'Activo' ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link :href="route('patients.show', patient.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                    Ver
                  </Link>
                  <Link :href="route('patients.edit', patient.id)" class="text-green-600 hover:text-green-900 mr-3">
                    Editar
                  </Link>
                  <button @click="confirmDelete(patient)" class="text-red-600 hover:text-red-900">
                    Eliminar
                  </button>
                </td>
              </tr>
              <tr v-if="patients.data.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  No hay datos para mostrar
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Toggle para cambiar vista -->
      <div class="flex items-center justify-end mt-4 mb-2">
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
      
      <!-- Paginación -->
      <div v-if="patients.links" class="mt-4">
        <Pagination :links="patients.links" />
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

export default {
  components: {
    AppLayout,
    Link,
    Pagination
  },
  props: {
    patients: Object,
    filters: Object
  },
  data() {
    return {
      search: this.filters?.search || '',
      viewMode: localStorage.getItem('patientViewMode') || 'cards' // Default to cards view
    };
  },
  watch: {
    'filters': {
      handler(newFilters) {
        this.search = newFilters?.search || '';
      },
      deep: true
    },
    'viewMode': {
      handler(newMode) {
        localStorage.setItem('patientViewMode', newMode);
      }
    }
  },
  methods: {
    searchPatients() {
      this.$inertia.get(route('patients.index'), { 
        search: this.search 
      }, {
        preserveState: true,
        replace: true
      });
    },
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map(part => part.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
    },
    confirmDelete(patient) {
      if (confirm(`¿Está seguro que desea eliminar al paciente ${patient.name}?`)) {
        this.$inertia.delete(route('patients.destroy', patient.id));
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'No disponible';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('es-GT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(date);
    }
  }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>