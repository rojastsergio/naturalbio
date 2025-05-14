<template>
  <AppLayout title="Doctores">
    <div class="p-4">
      <!-- Filtros y búsqueda -->
      <div class="flex flex-col mb-6 space-y-4">
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="relative flex-1">
            <input
              type="text"
              v-model="search"
              placeholder="Buscar doctores..."
              class="w-full rounded-md border-gray-300 focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50"
              @input="debouncedSearch"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
          
          <Link
            :href="route('doctors.create')"
            class="inline-flex justify-center items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition-colors duration-300"
          >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nuevo Doctor
          </Link>
        </div>
        
        <select
          v-model="specialty"
          class="w-full rounded-md border-gray-300 focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50"
          @change="filterBySpecialty"
        >
          <option value="">Todas las especialidades</option>
          <option v-for="spec in specialties" :key="spec" :value="spec">
            {{ spec }}
          </option>
        </select>
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
        <div v-for="doctor in doctors.data" :key="doctor.id" 
             class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300">
          
          <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
              <div v-if="doctor.user?.profile_photo_path" class="w-12 h-12 rounded-full overflow-hidden">
                <img :src="doctor.user.profile_photo_path" :alt="doctor.user?.name || doctor.name" class="w-full h-full object-cover" />
              </div>
              <div v-else class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                {{ getInitials(doctor.user?.name || doctor.name) }}
              </div>
              <div>
                <h3 class="font-semibold text-gray-800 text-lg">
                  {{ doctor.user?.name || doctor.name }}
                </h3>
                <p class="text-sm text-green-600 font-medium">
                  {{ doctor.specialty || 'Sin especialidad' }}
                </p>
              </div>
            </div>
            <span 
              class="px-2 py-1 text-xs rounded-full"
              :class="doctor.accepts_emergencies ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
            >
              {{ doctor.accepts_emergencies ? 'Emergencias' : 'Sin emergencias' }}
            </span>
          </div>

          <div class="p-4 space-y-3">
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span class="text-gray-600">{{ doctor.user?.email || doctor.email || 'No disponible' }}</span>
            </div>
            
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              <span class="text-gray-600">Licencia: {{ doctor.license_number || 'No registrada' }}</span>
            </div>
            
            <div class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-gray-600 font-medium">Consulta: {{ formatCurrency(doctor.consultation_fee) }}</span>
            </div>
            
            <div v-if="doctor.created_at" class="flex items-center text-sm">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-gray-600">Registro: {{ formatDate(doctor.created_at) }}</span>
            </div>
          </div>

          <div class="px-4 py-3 bg-gray-50 flex justify-between items-center border-t border-gray-200">
            <Link :href="route('doctors.show', doctor.id)" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
              Ver detalles
            </Link>
            <div class="flex space-x-3">
              <Link :href="route('doctors.edit', doctor.id)" class="text-sm text-green-600 hover:text-green-800">
                Editar
              </Link>
              <button @click.prevent="confirmDelete(doctor)" class="text-sm text-red-600 hover:text-red-800">
                Eliminar
              </button>
            </div>
          </div>
        </div>

        <div v-if="doctors.data.length === 0" class="col-span-full bg-white rounded-lg shadow p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-500">No hay doctores para mostrar</p>
          <Link
            :href="route('doctors.create')"
            class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-naturalbio-verde hover:bg-green-600"
          >
            Agregar primer doctor
          </Link>
        </div>
      </div>
      
      <!-- Vista de tabla (tradicional) -->
      <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div v-if="doctors.data.length === 0" class="p-6 text-center text-gray-500">
          No hay doctores para mostrar
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emergencias</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consulta</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="doctor in doctors.data" :key="doctor.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <img 
                      v-if="doctor.user?.profile_photo_path"
                      :src="doctor.user.profile_photo_path" 
                      :alt="doctor.user?.name || 'Doctor'"
                      class="h-10 w-10 rounded-full object-cover"
                    >
                    <div v-else class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                      {{ getInitials(doctor.user?.name || doctor.name) }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ doctor.user?.name || doctor.name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ doctor.user?.email || doctor.email }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ doctor.specialty || 'No especificada' }}</div>
                  <div class="text-sm text-gray-500">{{ doctor.license_number || 'Sin licencia' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="doctor.accepts_emergencies ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                  >
                    {{ doctor.accepts_emergencies ? 'Sí' : 'No' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatCurrency(doctor.consultation_fee) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link 
                    :href="route('doctors.show', doctor.id)" 
                    class="text-blue-600 hover:text-blue-900 mr-3"
                  >
                    Ver
                  </Link>
                  <Link 
                    :href="route('doctors.edit', doctor.id)" 
                    class="text-green-600 hover:text-green-900 mr-3"
                  >
                    Editar
                  </Link>
                  <button 
                    @click="confirmDelete(doctor)" 
                    class="text-red-600 hover:text-red-900"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Paginación -->
      <div v-if="doctors.links" class="mt-4">
        <Pagination :links="doctors.links" />
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash';

export default {
  components: {
    AppLayout,
    Link,
    Pagination
  },
  props: {
    doctors: Object,
    filters: Object,
    specialties: Array
  },
  data() {
    return {
      search: this.filters?.search || '',
      specialty: this.filters?.specialty || '',
      showDeleteModal: false,
      doctorToDelete: null,
      viewMode: localStorage.getItem('doctorViewMode') || 'cards' // Default to cards view
    };
  },
  created() {
    this.debouncedSearch = debounce(() => {
      this.$inertia.get(route('doctors.index'), {
        search: this.search,
        specialty: this.specialty
      }, {
        preserveState: true,
        preserveScroll: true
      });
    }, 300);
  },
  watch: {
    'viewMode': {
      handler(newMode) {
        localStorage.setItem('doctorViewMode', newMode);
      }
    }
  },
  methods: {
    formatCurrency(value) {
      if (value === null || value === undefined) return 'Q0.00';
      
      return new Intl.NumberFormat('es-GT', {
        style: 'currency',
        currency: 'GTQ',
        minimumFractionDigits: 2
      }).format(value);
    },
    formatDate(dateString) {
      if (!dateString) return 'No disponible';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('es-GT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(date);
    },
    getInitials(name) {
      if (!name) return 'DR';
      return name
        .split(' ')
        .map(part => part.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
    },
    filterBySpecialty() {
      this.$inertia.visit(route('doctors.index'), {
        data: {
          search: this.search,
          specialty: this.specialty
        },
        preserveState: true,
        preserveScroll: true
      });
    },
    confirmDelete(doctor) {
      if (confirm(`¿Está seguro que desea eliminar al doctor ${doctor.user?.name || doctor.name}?`)) {
        this.$inertia.delete(route('doctors.destroy', doctor.id));
      }
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