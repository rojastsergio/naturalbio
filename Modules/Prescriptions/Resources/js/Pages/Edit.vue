<template>
    <AppLayout title="Editar Receta">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Editar Receta: {{ prescription.prescription_number || `RX-${prescription.id}` }}
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Paciente -->
                <div>
                  <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Paciente *
                  </label>
                  <select
                    id="patient_id"
                    v-model="form.patient_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                    required
                  >
                    <option value="" disabled>Seleccionar paciente</option>
                    <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                      {{ patient.name }} {{ patient.last_name }}
                    </option>
                  </select>
                  <div v-if="form.errors.patient_id" class="text-red-500 text-xs mt-1">
                    {{ form.errors.patient_id }}
                  </div>
                </div>
  
                <!-- Doctor -->
                <div>
                  <label for="doctor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Doctor *
                  </label>
                  <select
                    id="doctor_id"
                    v-model="form.doctor_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                    required
                  >
                    <option value="" disabled>Seleccionar doctor</option>
                    <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                      {{ doctor.name }} ({{ doctor.specialty }})
                    </option>
                  </select>
                  <div v-if="form.errors.doctor_id" class="text-red-500 text-xs mt-1">
                    {{ form.errors.doctor_id }}
                  </div>
                </div>
  
                <!-- Fecha de emisión -->
                <div>
                  <label for="issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Fecha de emisión *
                  </label>
                  <input
                    id="issue_date"
                    type="date"
                    v-model="form.issue_date"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                    required
                  />
                  <div v-if="form.errors.issue_date" class="text-red-500 text-xs mt-1">
                    {{ form.errors.issue_date }}
                  </div>
                </div>
  
                <!-- Fecha de expiración -->
                <div>
                  <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Fecha de expiración
                  </label>
                  <input
                    id="expiry_date"
                    type="date"
                    v-model="form.expiry_date"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                  />
                  <div v-if="form.errors.expiry_date" class="text-red-500 text-xs mt-1">
                    {{ form.errors.expiry_date }}
                  </div>
                </div>
  
                <!-- Estado -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Estado
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                  >
                    <option value="active">Activa</option>
                    <option value="inactive">Inactiva</option>
                  </select>
                  <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">
                    {{ form.errors.status }}
                  </div>
                </div>
              </div>
  
              <!-- Diagnóstico -->
              <div class="mb-6">
                <label for="diagnosis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Diagnóstico
                </label>
                <textarea
                  id="diagnosis"
                  v-model="form.diagnosis"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                ></textarea>
                <div v-if="form.errors.diagnosis" class="text-red-500 text-xs mt-1">
                  {{ form.errors.diagnosis }}
                </div>
              </div>
  
              <!-- Instrucciones generales -->
              <div class="mb-6">
                <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Instrucciones generales
                </label>
                <textarea
                  id="instructions"
                  v-model="form.instructions"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                ></textarea>
                <div v-if="form.errors.instructions" class="text-red-500 text-xs mt-1">
                  {{ form.errors.instructions }}
                </div>
              </div>
  
              <!-- Ítems de la receta -->
              <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Medicamentos/Suplementos</h3>
                  <button
                    type="button"
                    @click="addItem"
                    class="inline-flex items-center px-3 py-1 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:bg-naturalbio-verde-dark active:bg-naturalbio-verde-dark focus:outline-none focus:ring-2 focus:ring-naturalbio-verde-light focus:ring-offset-2 transition ease-in-out duration-150"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                      />
                    </svg>
                    Agregar ítem
                  </button>
                </div>
  
                <div v-if="form.items.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                  No hay ítems en la receta. Haga clic en "Agregar ítem" para comenzar.
                </div>
  
                <div v-for="(item, index) in form.items" :key="index" class="border dark:border-gray-700 rounded-md p-4 mb-4">
                  <div class="flex justify-between items-start mb-4">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">
                      Ítem #{{ index + 1 }}
                    </h4>
                    <button
                      type="button"
                      @click="removeItem(index)"
                      class="text-red-600 hover:text-red-800"
                    >
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v10M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"
                        />
                      </svg>
                    </button>
                  </div>
  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nombre del medicamento/suplemento *
                      </label>
                      <input
                        type="text"
                        v-model="item.name"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                        required
                      />
                      <div v-if="form.errors[`items.${index}.name`]" class="text-red-500 text-xs mt-1">
                        {{ form.errors[`items.${index}.name`] }}
                      </div>
                    </div>
  
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Dosificación
                      </label>
                      <input
                        type="text"
                        v-model="item.dosage"
                        placeholder="Ej: 1 tableta"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                      />
                    </div>
  
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Frecuencia
                      </label>
                      <input
                        type="text"
                        v-model="item.frequency"
                        placeholder="Ej: Cada 8 horas"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                      />
                    </div>
  
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Duración
                      </label>
                      <input
                        type="text"
                        v-model="item.duration"
                        placeholder="Ej: Durante 7 días"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                      />
                    </div>
                  </div>
  
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Posología completa
                    </label>
                    <textarea
                      v-model="item.posology"
                      rows="2"
                      placeholder="Instrucciones detalladas de dosificación"
                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                    ></textarea>
                  </div>
  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      Instrucciones adicionales
                    </label>
                    <textarea
                      v-model="item.instructions"
                      rows="2"
                      placeholder="Indicaciones adicionales (ej: tomar con alimentos)"
                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-naturalbio-verde rounded-md shadow-sm"
                    ></textarea>
                  </div>
                </div>
  
                <div v-if="form.errors.items" class="text-red-500 text-xs mt-1">
                  {{ form.errors.items }}
                </div>
              </div>
  
              <div class="flex items-center justify-end mt-6 space-x-4">
                <Link
                  :href="route('prescriptions.show', prescription.id)"
                  class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Cancelar
                </Link>
  
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:bg-naturalbio-verde-dark active:bg-naturalbio-verde-dark focus:outline-none focus:ring-2 focus:ring-naturalbio-verde-light focus:ring-offset-2 transition ease-in-out duration-150"
                  :disabled="form.processing"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Actualizar receta
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import { Link, useForm } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  const props = defineProps({
    prescription: Object,
    patients: Array,
    doctors: Array,
  });
  
  // Convertir fecha de string a formato de input date (YYYY-MM-DD)
  function formatDateForInput(dateString) {
    if (!dateString) return null;
    const date = new Date(dateString);
    return date.toISOString().slice(0, 10);
  }
  
  const form = useForm({
    patient_id: props.prescription.patient_id,
    doctor_id: props.prescription.doctor_id,
    issue_date: formatDateForInput(props.prescription.issue_date),
    expiry_date: formatDateForInput(props.prescription.expiry_date),
    diagnosis: props.prescription.diagnosis || '',
    instructions: props.prescription.instructions || '',
    status: props.prescription.status || 'active',
    items: props.prescription.items.map(item => ({
      name: item.name,
      posology: item.posology || '',
      instructions: item.instructions || '',
      is_supplement: item.is_supplement || false,
      supplement_id: item.supplement_id,
      dosage: item.dosage || '',
      frequency: item.frequency || '',
      duration: item.duration || '',
      notes: item.notes || '',
    })),
  });
  
  function addItem() {
    form.items.push({
      name: '',
      posology: '',
      instructions: '',
      dosage: '',
      frequency: '',
      duration: '',
      is_supplement: false,
      supplement_id: null,
      notes: '',
    });
  }
  
  function removeItem(index) {
    form.items.splice(index, 1);
  }
  
  function submit() {
    if (form.items.length === 0) {
      form.setError('items', 'Debe agregar al menos un medicamento/suplemento a la receta.');
      return;
    }
  
    form.put(route('prescriptions.update', props.prescription.id), {
      onSuccess: () => {
        // No es necesario resetear el formulario ya que seremos redirigidos
      },
    });
  }
  
  onMounted(() => {
    // Si no hay ítems, añadir uno vacío por defecto
    if (form.items.length === 0) {
      addItem();
    }
  });
  </script>