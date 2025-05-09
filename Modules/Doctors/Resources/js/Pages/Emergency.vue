<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gestión de Emergencias
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Registrar nueva cita de emergencia</h3>
            
            <form @submit.prevent="submitEmergency">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Paciente
                  </label>
                  <div class="relative">
                    <input 
                      type="text" 
                      v-model="searchPatient" 
                      @input="debouncedSearchPatients"
                      placeholder="Buscar paciente por nombre o expediente..." 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <div 
                      v-if="showPatientResults && patientResults.length > 0" 
                      class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto"
                    >
                      <div 
                        v-for="patient in patientResults" 
                        :key="patient.id"
                        @click="selectPatient(patient)"
                        class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                      >
                        <div class="font-semibold">{{ patient.user.name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          Email: {{ patient.user.email }} | Expediente: {{ patient.record_number || 'N/A' }}
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Información del paciente seleccionado -->
                  <div v-if="selectedPatient" class="mt-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                    <div class="font-semibold">{{ selectedPatient.user.name }}</div>
                    <div class="text-sm">
                      <div>Email: {{ selectedPatient.user.email }}</div>
                      <div>Expediente: {{ selectedPatient.record_number || 'N/A' }}</div>
                      <div v-if="selectedPatient.latest_vitals">
                        Últimos signos vitales: {{ formatDate(selectedPatient.latest_vitals.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tipo de Emergencia
                  </label>
                  <select 
                    v-model="form.appointment_type_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    required
                  >
                    <option v-for="type in appointmentTypes" :key="type.id" :value="type.id">
                      {{ type.name }}
                    </option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Fecha y Hora de Inicio
                  </label>
                  <input 
                    type="datetime-local" 
                    v-model="form.start_time"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    required
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Duración estimada (minutos)
                  </label>
                  <input 
                    type="number" 
                    v-model="duration"
                    min="15"
                    step="15"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    required
                  />
                </div>
                
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notas de Emergencia
                  </label>
                  <textarea 
                    v-model="form.notes"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-naturalbio-verde focus:ring focus:ring-naturalbio-verde focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    required
                  ></textarea>
                </div>
              </div>
              
              <div class="mt-6 flex justify-end">
                <button 
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition ease-in-out duration-150"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting">Procesando...</span>
                  <span v-else>Registrar Emergencia</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </app-layout>
  </template>
  
  <script>
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { debounce } from 'lodash';
  
  export default {
    components: {
      AppLayout
    },
    props: {
      doctor: Object,
      appointmentTypes: Array
    },
    data() {
      return {
        searchPatient: '',
        patientResults: [],
        showPatientResults: false,
        selectedPatient: null,
        duration: 30,
        isSubmitting: false,
        form: {
          patient_id: null,
          doctor_id: this.doctor ? this.doctor.id : null,
          appointment_type_id: '',
          start_time: this.getCurrentDateTime(),
          end_time: null,
          notes: '',
          emergency: true
        }
      };
    },
    created() {
      this.debouncedSearchPatients = debounce(this.searchPatients, 300);
      
      // Escuchar clics en el documento para cerrar resultados
      document.addEventListener('click', this.handleDocumentClick);
    },
    beforeUnmount() {
      document.removeEventListener('click', this.handleDocumentClick);
    },
    methods: {
      getCurrentDateTime() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        return `${year}-${month}-${day}T${hours}:${minutes}`;
      },
      formatDate(date) {
        if (!date) return 'N/A';
        return new Date(date).toLocaleDateString('es-GT');
      },
      handleDocumentClick(event) {
        const isClickInside = this.$el.contains(event.target);
        if (!isClickInside) {
          this.showPatientResults = false;
        }
      },
      searchPatients() {
        if (this.searchPatient.length < 2) {
          this.patientResults = [];
          this.showPatientResults = false;
          return;
        }
        
        axios.get('/api/patients/search', {
          params: { search: this.searchPatient }
        })
        .then(response => {
          this.patientResults = response.data;
          this.showPatientResults = true;
        })
        .catch(error => {
          console.error('Error buscando pacientes:', error);
        });
      },
      selectPatient(patient) {
        this.selectedPatient = patient;
        this.searchPatient = patient.user.name;
        this.form.patient_id = patient.id;
        this.showPatientResults = false;
      },
      calculateEndTime() {
        if (!this.form.start_time || !this.duration) return null;
        
        const start = new Date(this.form.start_time);
        const end = new Date(start.getTime() + this.duration * 60000);
        
        return end.toISOString();
      },
      submitEmergency() {
        if (!this.form.patient_id) {
          alert('Por favor seleccione un paciente');
          return;
        }
        
        this.isSubmitting = true;
        
        // Calcular hora de fin basada en la duración
        this.form.end_time = this.calculateEndTime();
        
        axios.post('/api/appointments/emergency', this.form)
          .then(response => {
            this.$inertia.visit(route('appointments.show', response.data.id), {
              onFinish: () => {
                this.isSubmitting = false;
              }
            });
          })
          .catch(error => {
            console.error('Error al registrar emergencia:', error);
            this.isSubmitting = false;
            
            if (error.response && error.response.data && error.response.data.message) {
              alert(`Error: ${error.response.data.message}`);
            } else {
              alert('Ha ocurrido un error al registrar la emergencia. Por favor intente nuevamente.');
            }
          });
      }
    }
  };
  </script>