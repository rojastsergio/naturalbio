<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Instrucciones Terapéuticas
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="mb-6 flex justify-between items-center">
            <div class="w-1/3">
              <input
                type="text"
                v-model="search"
                placeholder="Buscar instrucciones..."
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                @input="debouncedSearch"
              />
            </div>
            <button
              @click="showNewInstructionModal = true"
              class="bg-naturalbio-verde hover:bg-naturalbio-verde-dark text-white font-bold py-2 px-4 rounded"
            >
              Nueva Instrucción
            </button>
          </div>
  
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div v-if="instructions.data.length === 0" class="p-6 text-gray-500 dark:text-gray-400 text-center">
              No se encontraron instrucciones terapéuticas. Crea una nueva instrucción para empezar.
            </div>
            <div v-else>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                <div 
                  v-for="instruction in instructions.data" 
                  :key="instruction.id"
                  class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer"
                  @click="viewInstruction(instruction)"
                >
                  <h3 class="text-lg font-semibold text-naturalbio-verde mb-2">{{ instruction.name }}</h3>
                  <p class="text-gray-600 dark:text-gray-400 text-sm mb-2 line-clamp-2">{{ instruction.description }}</p>
                  <div class="flex justify-between text-xs text-gray-500 dark:text-gray-500">
                    <span>Creado: {{ formatDate(instruction.created_at) }}</span>
                    <span>
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      Ver
                    </span>
                  </div>
                </div>
              </div>
              <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <pagination :links="instructions.links" />
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal para nueva instrucción terapéutica -->
      <modal v-if="showNewInstructionModal" @close="showNewInstructionModal = false">
        <template #title>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ editMode ? 'Editar' : 'Nueva' }} Instrucción Terapéutica
          </h3>
        </template>
  
        <form @submit.prevent="saveInstruction">
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Nombre
            </label>
            <input 
              type="text" 
              v-model="instructionForm.name" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
          </div>
  
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Descripción
            </label>
            <textarea 
              v-model="instructionForm.description" 
              rows="2"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            ></textarea>
          </div>
  
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Instrucciones
            </label>
            <textarea 
              v-model="instructionForm.instructions" 
              rows="4"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            ></textarea>
          </div>
  
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Áreas del cuerpo
            </label>
            <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
              <div v-for="area in bodyAreas" :key="area.value">
                <label class="inline-flex items-center">
                  <input 
                    type="checkbox" 
                    :value="area.value" 
                    v-model="selectedBodyAreas"
                    class="rounded border-gray-300 text-naturalbio-verde shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                  />
                  <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ area.label }}</span>
                </label>
              </div>
            </div>
          </div>
  
          <div class="flex justify-end mt-6">
            <button 
              type="button" 
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 mr-2 bg-gray-300 text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500"
              @click="showNewInstructionModal = false"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:outline-none transition ease-in-out duration-150"
            >
              {{ editMode ? 'Actualizar' : 'Guardar' }}
            </button>
            <button 
              v-if="editMode" 
              type="button" 
              class="inline-flex items-center px-4 py-2 ml-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition ease-in-out duration-150"
              @click="deleteInstruction"
            >
              Eliminar
            </button>
          </div>
        </form>
      </modal>
  
      <!-- Modal para ver instrucción detallada -->
      <modal v-if="showViewInstructionModal" @close="showViewInstructionModal = false" :max-width="'2xl'">
        <template #title>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ currentInstruction.name }}
          </h3>
        </template>
  
        <div class="p-4">
          <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</h4>
            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ currentInstruction.description || 'Sin descripción' }}</p>
          </div>
  
          <div class="mb-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Instrucciones</h4>
            <div class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ currentInstruction.instructions }}</div>
          </div>
  
          <div class="mb-4" v-if="currentInstruction.body_areas && currentInstruction.body_areas.length > 0">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Áreas del cuerpo</h4>
            <div class="mt-1 flex flex-wrap gap-2">
              <span 
                v-for="area in getBodyAreaLabels(currentInstruction.body_areas)" 
                :key="area"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-naturalbio-verde bg-opacity-10 text-naturalbio-verde"
              >
                {{ area }}
              </span>
            </div>
          </div>
  
          <div class="flex justify-end mt-6">
            <button 
              type="button" 
              class="inline-flex items-center px-4 py-2 bg-naturalbio-verde border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-naturalbio-verde-dark focus:outline-none transition ease-in-out duration-150 mr-2"
              @click="editCurrentInstruction"
            >
              Editar
            </button>
            <button 
              type="button" 
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 bg-gray-300 text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500"
              @click="showViewInstructionModal = false"
            >
              Cerrar
            </button>
          </div>
        </div>
      </modal>
    </app-layout>
  </template>
  
  <script>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import Modal from '@/Components/Modal.vue';
  import Pagination from '@/Components/Pagination.vue';
  import { debounce } from 'lodash';
  
  export default {
    components: {
      AppLayout,
      Modal,
      Pagination
    },
    props: {
      doctor: Object,
      instructions: Object,
      filters: Object
    },
    data() {
      return {
        search: this.filters?.search || '',
        showNewInstructionModal: false,
        showViewInstructionModal: false,
        editMode: false,
        currentInstructionId: null,
        currentInstruction: {},
        instructionForm: {
          doctor_id: this.doctor ? this.doctor.id : null,
          name: '',
          description: '',
          instructions: '',
          body_areas: []
        },
        selectedBodyAreas: [],
        bodyAreas: [
          { value: 'head', label: 'Cabeza' },
          { value: 'neck', label: 'Cuello' },
          { value: 'shoulders', label: 'Hombros' },
          { value: 'arms', label: 'Brazos' },
          { value: 'back', label: 'Espalda' },
          { value: 'chest', label: 'Pecho' },
          { value: 'abdomen', label: 'Abdomen' },
          { value: 'hips', label: 'Caderas' },
          { value: 'legs', label: 'Piernas' },
          { value: 'feet', label: 'Pies' }
        ]
      };
    },
    created() {
      this.debouncedSearch = debounce(() => {
        this.$inertia.get(route('doctor.therapy-instructions'), {
          search: this.search
        }, {
          preserveState: true,
          preserveScroll: true
        });
      }, 300);
    },
    methods: {
      formatDate(date) {
        if (!date) return '';
        return new Date(date).toLocaleDateString('es-GT');
      },
      viewInstruction(instruction) {
        this.currentInstruction = instruction;
        this.showViewInstructionModal = true;
      },
      editCurrentInstruction() {
        this.showViewInstructionModal = false;
        this.editInstruction(this.currentInstruction);
      },
      editInstruction(instruction) {
        this.editMode = true;
        this.currentInstructionId = instruction.id;
        
        this.instructionForm = {
          doctor_id: this.doctor ? this.doctor.id : null,
          name: instruction.name,
          description: instruction.description || '',
          instructions: instruction.instructions,
        };
        
        this.selectedBodyAreas = instruction.body_areas || [];
        this.showNewInstructionModal = true;
      },
      saveInstruction() {
        // Asignar áreas del cuerpo seleccionadas al formulario
        const formData = {
          ...this.instructionForm,
          body_areas: this.selectedBodyAreas
        };
        
        if (this.editMode) {
          // Actualizar instrucción existente
          axios.put(`/api/therapy-instructions/${this.currentInstructionId}`, formData)
            .then(() => {
              this.showNewInstructionModal = false;
              this.$inertia.reload();
            })
            .catch(error => {
              console.error('Error al actualizar instrucción:', error);
            });
        } else {
          // Crear nueva instrucción
          axios.post('/api/therapy-instructions', formData)
            .then(() => {
              this.showNewInstructionModal = false;
              this.$inertia.reload();
            })
            .catch(error => {
              console.error('Error al crear instrucción:', error);
            });
        }
      },
      deleteInstruction() {
        if (confirm('¿Está seguro de que desea eliminar esta instrucción terapéutica?')) {
          axios.delete(`/api/therapy-instructions/${this.currentInstructionId}`)
            .then(() => {
              this.showNewInstructionModal = false;
              this.$inertia.reload();
            })
            .catch(error => {
              console.error('Error al eliminar instrucción:', error);
            });
        }
      },
      getBodyAreaLabels(areas) {
        if (!areas || !Array.isArray(areas)) return [];
        
        return areas.map(area => {
          const found = this.bodyAreas.find(item => item.value === area);
          return found ? found.label : area;
        });
      }
    }
  };
  </script>