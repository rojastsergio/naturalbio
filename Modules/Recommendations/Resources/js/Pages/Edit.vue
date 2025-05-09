<template>
    <AppLayout :title="'Editar: ' + recommendation.title">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-title font-semibold text-xl text-naturalbio-verde leading-tight">
            Editar Recomendación
          </h2>
        </div>
      </template>
  
      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form @submit.prevent="submit" class="p-6">
              <!-- Información del paciente (solo lectura) -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paciente</label>
                <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-700 rounded-md dark:text-gray-300">
                  {{ recommendation.patient.name }} {{ recommendation.patient.last_name }}
                  <span class="text-gray-500 dark:text-gray-400 text-xs ml-2">(Exp: {{ recommendation.patient.expedient_number || 'N/A' }})</span>
                </div>
              </div>
  
              <!-- Título -->
              <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título de la Recomendación</label>
                <input
                  type="text"
                  id="title"
                  v-model="form.title"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                  required
                />
                <div v-if="form.errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.title }}
                </div>
              </div>
  
              <!-- Tareas -->
              <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tareas</label>
                  <button
                    type="button"
                    @click="addTask"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-naturalbio-verde hover:bg-naturalbio-verde/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
                  >
                    Agregar Tarea
                  </button>
                </div>
                
                <div v-for="(task, index) in form.tasks" :key="index" class="border dark:border-gray-600 rounded-md p-3 mb-2 dark:bg-gray-700">
                  <div class="flex items-center mb-2">
                    <input
                      type="checkbox"
                      v-model="task.completed"
                      class="h-4 w-4 text-naturalbio-verde focus:ring-naturalbio-verde border-gray-300 dark:border-gray-700 rounded"
                    />
                    <span class="ml-2 text-sm dark:text-gray-300" :class="{ 'line-through': task.completed }">
                      Completada
                    </span>
                  </div>
                  <div class="mb-2">
                    <label :for="'task-title-' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                    <input
                      :id="'task-title-' + index"
                      v-model="task.title"
                      type="text"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                      required
                    />
                  </div>
                  <div class="mb-2">
                    <label :for="'task-description-' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                    <textarea
                      :id="'task-description-' + index"
                      v-model="task.description"
                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                      rows="2"
                    ></textarea>
                  </div>
                  <div class="flex justify-end">
                    <button
                      type="button"
                      @click="removeTask(index)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-800 dark:text-red-100 dark:hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      Eliminar
                    </button>
                  </div>
                </div>
                
                <div v-if="form.errors.tasks" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.tasks }}
                </div>
              </div>
  
              <!-- Fecha de vencimiento -->
              <div class="mb-4">
                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Vencimiento</label>
                <input
                  type="date"
                  id="due_date"
                  v-model="form.due_date"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                />
                <div v-if="form.errors.due_date" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.due_date }}
                </div>
              </div>
  
              <!-- Notas -->
              <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas</label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                ></textarea>
                <div v-if="form.errors.notes" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.notes }}
                </div>
              </div>
  
              <!-- Estado -->
              <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                <select
                  id="status"
                  v-model="form.status"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring-naturalbio-verde sm:text-sm"
                >
                  <option value="active">Activa</option>
                  <option value="completed">Completada</option>
                  <option value="cancelled">Cancelada</option>
                </select>
                <div v-if="form.errors.status" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.status }}
                </div>
              </div>
  
              <div class="flex justify-end mt-6">
                <Link
                  :href="route('recommendations.show', recommendation.id)"
                  class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde mr-2"
                >
                  Cancelar
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-naturalbio-verde hover:bg-naturalbio-verde/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
                >
                  Actualizar Recomendación
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { Head, Link, useForm } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  
  const props = defineProps({
    recommendation: Object,
  });
  
  // Estado del formulario
  const form = useForm({
    title: props.recommendation.title,
    tasks: props.recommendation.tasks || [],
    due_date: props.recommendation.due_date,
    notes: props.recommendation.notes || '',
    status: props.recommendation.status || 'active',
  });
  
  // Agregar tarea
  const addTask = () => {
    form.tasks.push({
      title: '',
      description: '',
      completed: false
    });
  };
  
  // Eliminar tarea
  const removeTask = (index) => {
    form.tasks.splice(index, 1);
  };
  
  // Enviar formulario
  const submit = () => {
    form.put(route('recommendations.update', props.recommendation.id));
  };
  </script>