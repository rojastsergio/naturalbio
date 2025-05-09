<template>
    <AppLayout title="Pacientes">
      <div class="p-4">
        <!-- Barra de búsqueda y botón -->
        <div class="flex flex-col sm:flex-row mb-4 gap-4">
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
              class="absolute right-0 top-0 h-full px-4 bg-naturalbio-verde text-white rounded-r-md hover:bg-naturalbio-verde-600"
            >
              Buscar
            </button>
          </div>
          
          <Link
            :href="route('patients.create')"
            class="inline-flex items-center justify-center px-4 py-2 bg-naturalbio-verde text-white rounded-md hover:bg-naturalbio-verde-600"
          >
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nuevo Paciente
          </Link>
        </div>
        
        <!-- Tabla de pacientes -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                        <div class="text-sm font-medium text-gray-900">{{ patient.name }}</div>
                        <div class="text-sm text-gray-500">{{ patient.expedient }}</div>
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
          
          <!-- Paginación -->
          <div v-if="patients.links" class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <Pagination :links="patients.links" />
          </div>
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
        search: this.filters?.search || ''
      };
    },
    watch: {
      'filters': {
        handler(newFilters) {
          this.search = newFilters?.search || '';
        },
        deep: true
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
      }
    }
  };
  </script>