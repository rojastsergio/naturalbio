<template>
  <AppLayout title="Doctores">
    <div class="p-4">
      <!-- Filtros y búsqueda -->
      <div class="flex flex-col mb-4 space-y-4">
        <input
          type="text"
          v-model="search"
          placeholder="Buscar doctores..."
          class="w-full rounded-md border-gray-300 focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50"
          @input="debouncedSearch"
        />
        
        <div class="flex flex-col sm:flex-row gap-4">
          <select
            v-model="specialty"
            class="w-full sm:w-1/2 rounded-md border-gray-300 focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50"
            @change="filterBySpecialty"
          >
            <option value="">Todas las especialidades</option>
            <option v-for="spec in specialties" :key="spec" :value="spec">
              {{ spec }}
            </option>
          </select>
          
          <Link
            :href="route('doctors.create')"
            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-700"
          >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nuevo Doctor
          </Link>
        </div>
      </div>
      
      <!-- Tabla de doctores -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                      :src="doctor.user?.profile_photo_path || '/img/default-avatar.png'" 
                      :alt="doctor.user?.name || 'Doctor'"
                      class="h-10 w-10 rounded-full object-cover"
                    >
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
        
        <!-- Paginación -->
        <div v-if="doctors.links" class="px-6 py-3 bg-gray-50 border-t border-gray-200">
          <Pagination :links="doctors.links" />
        </div>
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
      doctorToDelete: null
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
  methods: {
    formatCurrency(value) {
      if (value === null || value === undefined) return 'Q0.00';
      
      return new Intl.NumberFormat('es-GT', {
        style: 'currency',
        currency: 'GTQ',
        minimumFractionDigits: 2
      }).format(value);
    },
    filterBySpecialty() {
      this.$inertia.get(route('doctors.index'), {
        search: this.search,
        specialty: this.specialty
      }, {
        preserveState: true,
        preserveScroll: true
      });
    },
    confirmDelete(doctor) {
      this.doctorToDelete = doctor;
      this.showDeleteModal = true;
    },
    deleteDoctor() {
      if (!this.doctorToDelete) return;
      
      this.$inertia.delete(route('doctors.destroy', this.doctorToDelete.id), {
        onSuccess: () => {
          this.showDeleteModal = false;
          this.doctorToDelete = null;
        }
      });
    }
  }
};
</script>