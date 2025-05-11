<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Editar Tipo de Cita
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Nombre -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="form.name" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
                  required
                >
                <div v-if="form.errors.name" class="text-red-500 mt-1 text-sm">{{ form.errors.name }}</div>
              </div>

              <!-- Color -->
              <div>
                <label for="color" class="block text-sm font-medium text-gray-700">Color (Hex)</label>
                <div class="mt-1 flex">
                  <input 
                    type="color" 
                    id="color-picker" 
                    v-model="form.color" 
                    class="h-10 w-10 rounded border-gray-300 mr-2"
                  >
                  <input 
                    type="text" 
                    id="color" 
                    v-model="form.color" 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
                    placeholder="#3B82F6"
                  >
                </div>
                <div v-if="form.errors.color" class="text-red-500 mt-1 text-sm">{{ form.errors.color }}</div>
              </div>

              <!-- Duración -->
              <div>
                <label for="default_duration" class="block text-sm font-medium text-gray-700">Duración por defecto (minutos)</label>
                <input 
                  type="number" 
                  id="default_duration" 
                  v-model="form.default_duration" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
                  min="1"
                >
                <div v-if="form.errors.default_duration" class="text-red-500 mt-1 text-sm">{{ form.errors.default_duration }}</div>
              </div>

              <!-- Precio -->
              <div>
                <label for="default_price" class="block text-sm font-medium text-gray-700">Precio por defecto (Q)</label>
                <input 
                  type="number" 
                  id="default_price" 
                  v-model="form.default_price" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
                  min="0"
                  step="0.01"
                >
                <div v-if="form.errors.default_price" class="text-red-500 mt-1 text-sm">{{ form.errors.default_price }}</div>
              </div>
            </div>

            <!-- Descripción -->
            <div class="mt-6">
              <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
              <textarea 
                id="description" 
                v-model="form.description" 
                rows="3" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
              ></textarea>
              <div v-if="form.errors.description" class="text-red-500 mt-1 text-sm">{{ form.errors.description }}</div>
            </div>

            <!-- Estado -->
            <div class="mt-6">
              <div class="flex items-center">
                <input 
                  type="checkbox" 
                  id="active" 
                  v-model="form.active" 
                  class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                >
                <label for="active" class="ml-2 block text-sm text-gray-900">Activo</label>
              </div>
              <div v-if="form.errors.active" class="text-red-500 mt-1 text-sm">{{ form.errors.active }}</div>
            </div>

            <!-- Botones -->
            <div class="mt-8 flex justify-end">
              <Link 
                :href="route('appointment-types.index')" 
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 transition mr-2"
              >
                Cancelar
              </Link>
              <button 
                type="submit" 
                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:outline-none focus:border-emerald-700 focus:ring focus:ring-emerald-200 active:bg-emerald-600 transition"
                :disabled="form.processing"
              >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Actualizar Tipo de Cita
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { Link, useForm } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  components: {
    AppLayout,
    Link
  },
  
  props: {
    appointmentType: Object,
  },
  
  setup(props) {
    const form = useForm({
      name: props.appointmentType.name || '',
      color: props.appointmentType.color || '#3B82F6',
      description: props.appointmentType.description || '',
      default_price: props.appointmentType.default_price || null,
      default_duration: props.appointmentType.default_duration || 30,
      active: typeof props.appointmentType.active !== 'undefined' ? props.appointmentType.active : true,
    })

    function submit() {
      form.put(route('appointment-types.update', props.appointmentType.id))
    }

    return {
      form,
      submit
    }
  }
}
</script>