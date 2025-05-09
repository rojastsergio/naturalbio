<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Editar Doctor
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Usuario
                </label>
                <!-- Información del usuario (no editable) -->
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                  <div class="font-semibold">{{ doctor?.user?.name || 'Usuario' }}</div>
                  <div class="text-sm">
                    <div>Email: {{ doctor?.user?.email || 'No disponible' }}</div>
                  </div>
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Especialidad
                </label>
                <input 
                  type="text" 
                  v-model="form.specialty"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <div v-if="form.errors.specialty" class="text-red-500 text-sm mt-1">
                  {{ form.errors.specialty }}
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Número de Licencia
                </label>
                <input 
                  type="text" 
                  v-model="form.license_number"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <div v-if="form.errors.license_number" class="text-red-500 text-sm mt-1">
                  {{ form.errors.license_number }}
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Tarifa de Consulta (Q)
                </label>
                <input 
                  type="number" 
                  v-model="form.consultation_fee"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <div v-if="form.errors.consultation_fee" class="text-red-500 text-sm mt-1">
                  {{ form.errors.consultation_fee }}
                </div>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Biografía
                </label>
                <textarea 
                  v-model="form.biography"
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                ></textarea>
                <div v-if="form.errors.biography" class="text-red-500 text-sm mt-1">
                  {{ form.errors.biography }}
                </div>
              </div>
              
              <div>
                <label class="flex items-center">
                  <input 
                    type="checkbox" 
                    v-model="form.accepts_emergencies" 
                    class="rounded border-gray-300 text-naturalbio-verde shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                  />
                  <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Acepta emergencias</span>
                </label>
                <div v-if="form.errors.accepts_emergencies" class="text-red-500 text-sm mt-1">
                  {{ form.errors.accepts_emergencies }}
                </div>
              </div>
            </div>
            
            <div class="mt-6 flex justify-end">
              <Link
                :href="route('doctors.index')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 mr-2 bg-gray-300 text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500"
              >
                Cancelar
              </Link>
              <button 
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:outline-none transition ease-in-out duration-150"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Guardando...' : 'Actualizar Doctor' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  doctor: {
    type: Object,
    required: true
  }
});

// Inicializar formulario con valores por defecto seguros
const form = useForm({
  specialty: props.doctor?.specialty || '',
  license_number: props.doctor?.license_number || '',
  biography: props.doctor?.biography || '',
  consultation_fee: props.doctor?.consultation_fee || 0,
  accepts_emergencies: props.doctor?.accepts_emergencies || false
});

// Método para enviar el formulario
function submit() {
  if (props.doctor?.id) {
    form.put(route('doctors.update', props.doctor.id));
  }
}
</script>