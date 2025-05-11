<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Tipos de Citas
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Lista de Tipos de Citas</h3>
            <Link
              v-if="$page.props.auth.user.roles.includes('owner') || $page.props.auth.user.roles.includes('editor')"
              :href="route('appointment-types.create')"
              class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:outline-none focus:border-emerald-700 focus:ring focus:ring-emerald-200 active:bg-emerald-600 transition"
            >
              Crear Nuevo Tipo
            </Link>
          </div>

          <div v-if="appointmentTypes.data.length === 0" class="text-center py-8">
            <p class="text-gray-500">No hay tipos de citas registrados.</p>
            <Link
              v-if="$page.props.auth.user.roles.includes('owner') || $page.props.auth.user.roles.includes('editor')"
              :href="route('appointment-types.create')"
              class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:outline-none focus:border-emerald-700 focus:ring focus:ring-emerald-200 active:bg-emerald-600 transition"
            >
              Crear Primer Tipo de Cita
            </Link>
          </div>

          <table v-else class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="type in appointmentTypes.data" :key="type.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-4 h-4 mr-2 rounded-full" :style="{ backgroundColor: type.color || '#cccccc' }"></div>
                    <div class="text-sm font-medium text-gray-900">{{ type.name }}</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ type.default_duration ? `${type.default_duration} minutos` : 'No especificado' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ type.default_price ? `Q${type.default_price.toFixed(2)}` : 'No especificado' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="type.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ type.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link
                    v-if="$page.props.auth.user.roles.includes('owner') || $page.props.auth.user.roles.includes('editor')"
                    :href="route('appointment-types.edit', type.id)"
                    class="text-emerald-600 hover:text-emerald-900 mr-3"
                  >
                    Editar
                  </Link>
                  <button
                    v-if="$page.props.auth.user.roles.includes('owner')"
                    @click="confirmDeleteType(type)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <pagination class="mt-6" :links="appointmentTypes.links" />
        </div>
      </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <confirmation-modal v-if="confirmingDelete" @close="closeModal">
      <template #title>
        Eliminar Tipo de Cita
      </template>
      <template #content>
        ¿Está seguro que desea eliminar este tipo de cita? Esta acción no se puede deshacer.
      </template>
      <template #footer>
        <secondary-button @click="closeModal">
          Cancelar
        </secondary-button>

        <danger-button
          class="ml-3"
          @click="deleteType"
        >
          Eliminar
        </danger-button>
      </template>
    </confirmation-modal>
  </app-layout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { ref } from 'vue'

export default {
  components: {
    AppLayout,
    Link,
    Pagination,
    ConfirmationModal,
    SecondaryButton,
    DangerButton
  },
  
  props: {
    appointmentTypes: Object
  },
  
  setup() {
    const confirmingDelete = ref(false)
    const typeToDelete = ref(null)
    
    function confirmDeleteType(type) {
      typeToDelete.value = type
      confirmingDelete.value = true
    }
    
    function closeModal() {
      confirmingDelete.value = false
      setTimeout(() => {
        typeToDelete.value = null
      }, 300)
    }
    
    function deleteType() {
      if (typeToDelete.value) {
        Inertia.delete(route('appointment-types.destroy', typeToDelete.value.id), {
          onSuccess: () => {
            closeModal()
          }
        })
      }
    }
    
    return {
      confirmingDelete,
      typeToDelete,
      confirmDeleteType,
      closeModal,
      deleteType
    }
  }
}
</script>