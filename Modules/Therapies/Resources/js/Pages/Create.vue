<template>
    <AppLayout title="Nueva Terapia">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Crear Nueva Terapia
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información básica -->
                <div class="col-span-1 md:col-span-2">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Información Básica
                  </h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <InputLabel for="name" value="Nombre" />
                      <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                      />
                      <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    
                    <div>
                      <InputLabel for="status" value="Estado" />
                      <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                      >
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                      </select>
                      <InputError class="mt-2" :message="form.errors.status" />
                    </div>
                  </div>
                </div>
                
                <div class="col-span-1 md:col-span-2">
                  <InputLabel for="description" value="Descripción" />
                  <textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    rows="4"
                  ></textarea>
                  <InputError class="mt-2" :message="form.errors.description" />
                </div>
                
                <!-- Precios y duración -->
                <div>
                  <InputLabel for="default_price" value="Precio predeterminado (Q)" />
                  <TextInput
                    id="default_price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full"
                    v-model="form.default_price"
                    required
                  />
                  <InputError class="mt-2" :message="form.errors.default_price" />
                </div>
                
                <div>
                  <InputLabel for="price" value="Precio actual (Q) (Opcional)" />
                  <TextInput
                    id="price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full"
                    v-model="form.price"
                  />
                  <InputError class="mt-2" :message="form.errors.price" />
                </div>
                
                <div>
                  <InputLabel for="default_duration" value="Duración predeterminada (minutos)" />
                  <TextInput
                    id="default_duration"
                    type="number"
                    min="1"
                    class="mt-1 block w-full"
                    v-model="form.default_duration"
                    required
                  />
                  <InputError class="mt-2" :message="form.errors.default_duration" />
                </div>
                
                <!-- Media (simplificado) -->
                <div class="col-span-1 md:col-span-2">
                  <InputLabel value="Archivos multimedia (implementación futura)" />
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    La funcionalidad para subir imágenes y otros archivos multimedia se implementará próximamente.
                  </p>
                </div>
              </div>
              
              <div class="flex items-center justify-end mt-6">
                <Link
                  :href="route('therapies.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2"
                >
                  Cancelar
                </Link>
                <PrimaryButton
                  class="ml-4"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Crear terapia
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Link, useForm } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import TextInput from '@/Components/TextInput.vue';
  import InputError from '@/Components/InputError.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  
  const form = useForm({
    name: '',
    description: '',
    default_price: 0.00,
    price: null,
    default_duration: 60,
    media: null,
    status: 'active',
  });
  
  const submit = () => {
    form.post(route('therapies.store'), {
      onSuccess: () => {
        form.reset();
      },
    });
  };
  </script>