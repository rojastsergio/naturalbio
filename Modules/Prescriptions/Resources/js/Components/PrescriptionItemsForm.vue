<template>
    <div>
      <div class="flex justify-between items-center mb-3">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          {{ title }}
        </h3>
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
  
      <div v-if="modelValue.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
        No hay ítems en la receta. Haga clic en "Agregar ítem" para comenzar.
      </div>
  
      <div
        v-for="(item, index) in modelValue"
        :key="index"
        class="border dark:border-gray-700 rounded-md p-4 mb-4"
      >
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
            <div v-if="errors && errors[`items.${index}.name`]" class="text-red-500 text-xs mt-1">
              {{ errors[`items.${index}.name`] }}
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
  
      <div v-if="errors && errors.items" class="text-red-500 text-xs mt-1">
        {{ errors.items }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue';
  
  const props = defineProps({
    modelValue: {
      type: Array,
      required: true
    },
    title: {
      type: String,
      default: 'Medicamentos/Suplementos'
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  function addItem() {
    const items = [...props.modelValue];
    items.push({
      name: '',
      posology: '',
      instructions: '',
      dosage: '',
      frequency: '',
      duration: '',
      is_supplement: false,
      supplement_id: null,
      notes: ''
    });
    emit('update:modelValue', items);
  }
  
  function removeItem(index) {
    const items = [...props.modelValue];
    items.splice(index, 1);
    emit('update:modelValue', items);
  }
  </script>