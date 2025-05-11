<template>
  <div class="appointment-form">
    <!-- PASO 1: SELECCIONAR PACIENTE -->
    <div v-if="currentStep === 1" class="step-container">
      <h3 class="step-title">Paso 1: Seleccionar Paciente</h3>
      
      <!-- Paciente preseleccionado -->
      <div v-if="preselectedPatient" class="selected-patient">
        <div class="flex items-center">
          <div v-if="preselectedPatient.photo_path" class="mr-3">
            <img :src="preselectedPatient.photo_path" alt="Foto del paciente" class="h-12 w-12 rounded-full object-cover" />
          </div>
          <div>
            <div class="font-semibold">{{ preselectedPatient.name }} {{ preselectedPatient.last_name }}</div>
            <div class="text-sm text-gray-600">
              {{ preselectedPatient.expedient_number || 'Sin expediente' }}
            </div>
          </div>
        </div>
      </div>
      
      <!-- Buscar paciente -->
      <div v-else>
        <div class="mb-4">
          <label for="patient-search" class="block text-sm font-medium text-gray-700">Buscar paciente</label>
          <div class="relative mt-1">
            <input
              type="text"
              id="patient-search"
              v-model="patientSearch"
              @input="searchPatients"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
              placeholder="Nombre o número de expediente..."
            />
            <div v-if="isSearching" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </div>
        </div>
        
        <!-- Resultados de búsqueda -->
        <div v-if="patients.length > 0" class="mt-2 max-h-60 overflow-y-auto rounded-md bg-white shadow-lg">
          <ul class="divide-y divide-gray-200">
            <li
              v-for="patient in patients"
              :key="patient.id"
              @click="selectPatient(patient)"
              class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
            >
              {{ patient.full_name }}
            </li>
          </ul>
        </div>
        
        <!-- Mensaje "no se encontraron pacientes" -->
        <div v-else-if="patientSearch.length >= 2 && !isSearching" class="mt-2 p-4 border rounded bg-gray-50">
          <div class="flex flex-col items-center text-center">
            <p class="text-sm text-gray-600 mb-3">No se encontraron pacientes con esta búsqueda.</p>
            <a
              href="/patients/create?redirect_after=/appointments/create"
              target="_blank"
              onclick="window.open('/patients/create?redirect_after=/appointments/create', '_blank'); return false;"
              class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:outline-none focus:border-emerald-700 focus:ring focus:ring-emerald-200 active:bg-emerald-600 transition"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Crear Nuevo Paciente
            </a>
            <p class="text-xs text-gray-500 mt-2">Se abrirá en una nueva pestaña. Luego regrese aquí para seleccionarlo.</p>
          </div>
        </div>
        
        <!-- Paciente seleccionado -->
        <div v-if="selectedPatient" class="mt-4 p-3 border rounded-md bg-gray-50">
          <div class="font-semibold">{{ selectedPatient.full_name }}</div>
        </div>
      </div>
    </div>
    
    <!-- PASO 2: SELECCIONAR DOCTOR -->
    <div v-if="currentStep === 2" class="step-container">
      <h3 class="step-title">Paso 2: Seleccionar Doctor</h3>

      <div v-if="selectedDoctor" class="mb-4">
        <div class="flex justify-between items-center p-3 bg-emerald-50 border border-emerald-200 rounded-md">
          <div class="flex items-center">
            <div v-if="selectedDoctor.photo_path" class="mr-3">
              <img :src="selectedDoctor.photo_path" alt="Foto del doctor" class="h-12 w-12 rounded-full object-cover" />
            </div>
            <div>
              <div class="font-semibold text-emerald-800">Doctor Seleccionado</div>
              <div class="font-medium">Dr. {{ selectedDoctor.name }}</div>
            </div>
          </div>
          <button
            type="button"
            class="text-emerald-600 hover:text-emerald-800"
            @click="selectedDoctor = null"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="!selectedDoctor" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
        <div
          v-for="doctor in doctors"
          :key="doctor.id"
          class="border rounded-md p-3 cursor-pointer hover:bg-gray-50"
          @click="selectDoctor(doctor)"
        >
          <div class="flex items-center">
            <div v-if="doctor.photo_path" class="mr-3">
              <img :src="doctor.photo_path" alt="Foto del doctor" class="h-10 w-10 rounded-full object-cover" />
            </div>
            <div>
              <div class="font-semibold">Dr. {{ doctor.name }}</div>
              <div class="text-sm text-gray-600">{{ doctor.specialty || 'Sin especialidad' }}</div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="doctors.length === 0" class="text-center py-4 text-gray-500">
        No hay doctores disponibles.
      </div>
    </div>
    
    <!-- PASO 3: TIPO DE CITA -->
    <div v-if="currentStep === 3" class="step-container">
      <h3 class="step-title">Paso 3: Tipo de Cita</h3>

      <div v-if="selectedType" class="mb-4">
        <div class="flex justify-between items-center p-3 bg-emerald-50 border border-emerald-200 rounded-md">
          <div class="flex items-center">
            <div class="w-6 h-6 mr-3 rounded-full" :style="{ backgroundColor: selectedType.color || '#cccccc' }"></div>
            <div>
              <div class="font-semibold text-emerald-800">Tipo de Cita Seleccionado</div>
              <div class="font-medium">{{ selectedType.name }}</div>
              <div class="text-sm text-gray-600">
                <span v-if="selectedType.default_duration">{{ selectedType.default_duration }} min</span>
                <span v-if="selectedType.default_duration && selectedType.default_price"> · </span>
                <span v-if="selectedType.default_price">Q{{ selectedType.default_price }}</span>
              </div>
            </div>
          </div>
          <button
            type="button"
            class="text-emerald-600 hover:text-emerald-800"
            @click="selectedType = null"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="!selectedType" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
        <div
          v-for="type in appointmentTypes"
          :key="type.id"
          class="border rounded-md p-3 cursor-pointer hover:bg-gray-50"
          @click="selectAppointmentType(type)"
        >
          <div class="flex items-center">
            <div class="w-4 h-4 mr-2 rounded-full" :style="{ backgroundColor: type.color || '#cccccc' }"></div>
            <div>
              <div class="font-semibold">{{ type.name }}</div>
              <div class="text-sm text-gray-600">
                <span v-if="type.default_duration">{{ type.default_duration }} min</span>
                <span v-if="type.default_duration && type.default_price"> · </span>
                <span v-if="type.default_price">Q{{ type.default_price }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="appointmentTypes.length === 0" class="text-center py-4 text-gray-500">
        No hay tipos de cita disponibles.
      </div>
    </div>
    
    <!-- PASO 4: SELECCIONAR TERAPIAS -->
    <div v-if="currentStep === 4" class="step-container">
      <h3 class="step-title">Paso 4: Seleccionar Terapias</h3>

      <!-- Terapias seleccionadas -->
      <div v-if="selectedTherapies.length > 0" class="mb-4">
        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-md">
          <div class="font-semibold text-emerald-800 mb-2">Terapias Seleccionadas ({{ selectedTherapies.length }})</div>
          <div class="flex flex-wrap gap-2">
            <div
              v-for="therapy in selectedTherapies"
              :key="therapy.id"
              class="flex items-center bg-white border border-emerald-300 rounded-full px-3 py-1"
            >
              <span class="text-sm mr-2">{{ therapy.name }}</span>
              <button
                @click.stop="toggleTherapy(therapy)"
                class="text-emerald-600 hover:text-emerald-800 focus:outline-none"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Lista de terapias disponibles -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-4">
        <div
          v-for="therapy in therapies"
          :key="therapy.id"
          class="border rounded-md p-3 cursor-pointer hover:bg-gray-50 transition-all"
          :class="{ 'ring-2 ring-emerald-500 bg-emerald-50': isTherapySelected(therapy.id) }"
          @click="toggleTherapy(therapy)"
        >
          <div class="flex items-center">
            <div class="flex-grow">
              <div class="font-semibold">{{ therapy.name }}</div>
              <div class="text-sm text-gray-600">
                {{ therapy.description && therapy.description.length > 60 ? therapy.description.slice(0, 60) + '...' : therapy.description }}
              </div>
              <div class="mt-1 text-xs text-gray-500 flex gap-2">
                <span v-if="therapy.default_duration">{{ therapy.default_duration }} min</span>
                <span v-if="therapy.default_price">Q{{ therapy.default_price }}</span>
              </div>
            </div>
            <div class="ml-2">
              <div class="w-6 h-6 rounded-full flex items-center justify-center" :class="isTherapySelected(therapy.id) ? 'bg-emerald-500' : 'bg-gray-200'">
                <svg v-if="isTherapySelected(therapy.id)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="therapies.length === 0" class="text-center py-8 bg-gray-50 rounded-md border border-gray-200 mt-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-gray-600">No hay terapias disponibles.</p>
        <p class="text-sm text-gray-500 mt-1">Las terapias deben ser configuradas por un administrador.</p>
      </div>
    </div>
    
    <!-- PASO 5: PROGRAMAR FECHA Y HORA -->
    <div v-if="currentStep === 5" class="step-container">
      <h3 class="step-title">Paso 5: Programar Fecha y Hora</h3>

      <div class="bg-emerald-50 border border-emerald-200 rounded-md p-4 mb-6">
        <div class="text-sm text-emerald-800">
          <p><strong>Importante:</strong> Seleccione la fecha y hora para la cita.</p>
          <p class="mt-1">La duración predeterminada se basa en el tipo de cita seleccionado.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
        <div>
          <label for="appointment-date" class="block text-sm font-medium text-gray-700">Fecha <span class="text-red-500">*</span></label>
          <input
            type="date"
            id="appointment-date"
            v-model="appointmentDate"
            :min="today"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
          />
          <p v-if="!appointmentDate" class="mt-1 text-sm text-red-600">Este campo es obligatorio</p>
        </div>

        <div>
          <label for="appointment-time" class="block text-sm font-medium text-gray-700">Hora <span class="text-red-500">*</span></label>
          <input
            type="time"
            id="appointment-time"
            v-model="appointmentTime"
            step="300"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
          />
          <p v-if="!appointmentTime" class="mt-1 text-sm text-red-600">Este campo es obligatorio</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <button
              v-for="time in ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00']"
              type="button"
              :key="time"
              class="px-2 py-1 text-xs border rounded-md"
              :class="appointmentTime === time ? 'bg-emerald-100 border-emerald-300 text-emerald-800' : 'bg-gray-50 border-gray-300 text-gray-700 hover:bg-gray-100'"
              @click="appointmentTime = time"
            >
              {{ time }}
            </button>
          </div>
        </div>

        <div>
          <label for="appointment-duration" class="block text-sm font-medium text-gray-700">Duración (minutos) <span class="text-red-500">*</span></label>
          <div class="mt-1 flex items-center">
            <input
              type="number"
              id="appointment-duration"
              v-model.number="appointmentDuration"
              min="5"
              step="5"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
            />
            <div class="ml-2 flex flex-wrap gap-1">
              <button
                v-for="duration in [15, 30, 45, 60]"
                type="button"
                :key="duration"
                class="px-2 py-1 text-xs border rounded-md"
                :class="appointmentDuration === duration ? 'bg-emerald-100 border-emerald-300 text-emerald-800' : 'bg-gray-50 border-gray-300 text-gray-700 hover:bg-gray-100'"
                @click="appointmentDuration = duration"
              >
                {{ duration }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- PASO 6: CONFIRMAR CITA -->
    <div v-if="currentStep === 6" class="step-container">
      <h3 class="step-title">Paso 6: Confirmar Cita</h3>
      
      <div class="bg-gray-50 p-4 rounded-md mt-4">
        <dl class="divide-y divide-gray-200">
          <div class="grid grid-cols-3 gap-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Paciente</dt>
            <dd class="text-sm font-medium text-gray-900 col-span-2">
              {{ selectedPatient ? selectedPatient.full_name : 'No seleccionado' }}
            </dd>
          </div>
          
          <div class="grid grid-cols-3 gap-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Doctor</dt>
            <dd class="text-sm font-medium text-gray-900 col-span-2">
              {{ selectedDoctor ? `Dr. ${selectedDoctor.name}` : 'No seleccionado' }}
            </dd>
          </div>
          
          <div class="grid grid-cols-3 gap-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Tipo de Cita</dt>
            <dd class="text-sm font-medium text-gray-900 col-span-2">
              {{ selectedType ? selectedType.name : 'No seleccionado' }}
            </dd>
          </div>
          
          <div class="grid grid-cols-3 gap-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Terapias</dt>
            <dd class="text-sm font-medium text-gray-900 col-span-2">
              <span v-if="selectedTherapies.length === 0">No seleccionadas</span>
              <ul v-else class="list-disc pl-5">
                <li v-for="therapy in selectedTherapies" :key="therapy.id">{{ therapy.name }}</li>
              </ul>
            </dd>
          </div>
          
          <div class="grid grid-cols-3 gap-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
            <dd class="text-sm font-medium text-gray-900 col-span-2">
              {{ appointmentDate }} {{ appointmentTime }}
            </dd>
          </div>
          
          <div class="grid grid-cols-3 gap-4 py-3">
            <dt class="text-sm font-medium text-gray-500">Duración</dt>
            <dd class="text-sm font-medium text-gray-900 col-span-2">{{ appointmentDuration }} minutos</dd>
          </div>
        </dl>
        
        <div class="mt-4">
          <label for="appointment-notes" class="block text-sm font-medium text-gray-700">Notas</label>
          <textarea
            id="appointment-notes"
            v-model="appointmentNotes"
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50"
            placeholder="Añadir notas o instrucciones..."
          ></textarea>
        </div>
      </div>
    </div>
    
    <!-- BOTONES DE NAVEGACIÓN -->
    <div class="flex justify-between mt-8">
      <button
        v-if="currentStep > 1"
        type="button"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
        @click="prevStep"
      >
        Atrás
      </button>
      <div v-else></div>
      
      <button
        v-if="currentStep < 6"
        type="button"
        class="px-4 py-2 text-sm font-medium text-white bg-naturalbio-verde border border-transparent rounded-md shadow-sm hover:bg-naturalbio-verde-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
        @click="goToNextStep"
        :disabled="!canProceed"
      >
        Siguiente
      </button>
      
      <button
        v-else
        type="button"
        class="px-4 py-2 text-sm font-medium text-white bg-naturalbio-verde border border-transparent rounded-md shadow-sm hover:bg-naturalbio-verde-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
        @click="submitAppointment"
      >
        Guardar Cita
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { format } from 'date-fns';
import axios from 'axios';

export default {
  props: {
    patients: {
      type: Array,
      default: () => []
    },
    doctors: {
      type: Array,
      default: () => []
    },
    appointmentTypes: {
      type: Array,
      default: () => []
    },
    therapies: {
      type: Array,
      default: () => []
    },
    therapists: {
      type: Array,
      default: () => []
    },
    preselectedPatient: {
      type: Object,
      default: null
    },
    preselectedDoctor: {
      type: Object,
      default: null
    },
    preselectedType: {
      type: Object,
      default: null
    },
    appointmentId: {
      type: Number,
      default: null
    },
    defaultDate: {
      type: String,
      default: ''
    }
  },
  
  emits: ['submit', 'cancel'],
  
  setup(props, { emit }) {
    // Estado del formulario
    const currentStep = ref(1);
    const patientSearch = ref('');
    const patients = ref([]);
    const isSearching = ref(false);
    
    // Valores seleccionados
    const selectedPatient = ref(props.preselectedPatient || null);
    const selectedDoctor = ref(props.preselectedDoctor || null);
    const selectedType = ref(props.preselectedType || null);
    const selectedTherapies = ref([]);
    
    // Detalles de la cita
    const appointmentDate = ref('');
    const appointmentTime = ref('');
    const appointmentDuration = ref(60);
    const appointmentNotes = ref('');
    
    // Log de datos para depuración
    console.log('Datos iniciales:');
    console.log('- Doctores:', props.doctors);
    console.log('- Tipos de cita:', props.appointmentTypes);
    console.log('- Terapias:', props.therapies);
    
    // Computed properties
    const today = computed(() => {
      return format(new Date(), 'yyyy-MM-dd');
    });
    
    const canProceed = computed(() => {
      switch (currentStep.value) {
        case 1:
          return !!selectedPatient.value;
        case 2:
          return true; // Doctor es opcional
        case 3:
          return true; // Tipo es opcional
        case 4:
          return true; // Terapias son opcionales
        case 5:
          return !!appointmentDate.value && !!appointmentTime.value && appointmentDuration.value > 0;
        default:
          return true;
      }
    });
    
    // Métodos
    function searchPatients() {
      if (patientSearch.value.length < 2) {
        patients.value = [];
        return;
      }
      
      isSearching.value = true;
      
      axios.get(`/api/patients/search?search=${encodeURIComponent(patientSearch.value)}`)
        .then(response => {
          console.log('Respuesta de búsqueda:', response.data);
          patients.value = response.data.patients || [];
        })
        .catch(error => {
          console.error('Error en búsqueda:', error);
          patients.value = [];
        })
        .finally(() => {
          isSearching.value = false;
        });
    }
    
    function selectPatient(patient) {
      console.log('Seleccionando paciente:', patient);
      selectedPatient.value = patient;
    }
    
    function selectDoctor(doctor) {
      console.log('Seleccionando doctor:', doctor);
      selectedDoctor.value = doctor;
    }
    
    function selectAppointmentType(type) {
      console.log('Seleccionando tipo de cita:', type);
      selectedType.value = type;
      appointmentDuration.value = type.default_duration || 60;
    }
    
    function toggleTherapy(therapy) {
      console.log('Toggling terapia:', therapy);
      const index = selectedTherapies.value.findIndex(t => t.id === therapy.id);
      
      if (index >= 0) {
        selectedTherapies.value.splice(index, 1);
      } else {
        selectedTherapies.value.push(therapy);
      }
    }
    
    function isTherapySelected(therapyId) {
      return selectedTherapies.value.some(t => t.id === therapyId);
    }
    
    function goToNextStep() {
      console.log('Avanzando al siguiente paso desde:', currentStep.value);
      if (currentStep.value < 6 && canProceed.value) {
        currentStep.value++;
      }
    }
    
    function prevStep() {
      console.log('Retrocediendo al paso anterior desde:', currentStep.value);
      if (currentStep.value > 1) {
        currentStep.value--;
      }
    }
    
    function submitAppointment() {
      console.log('Enviando formulario de cita');

      // Crear fecha y hora combinada para start_time
      const startDateTime = appointmentDate.value && appointmentTime.value
        ? `${appointmentDate.value}T${appointmentTime.value}`
        : null;

      const formData = {
        patient_id: selectedPatient.value?.id,
        doctor_id: selectedDoctor.value?.id,
        appointment_type_id: selectedType.value?.id,
        therapies: selectedTherapies.value.map(t => ({
          id: t.id,
          price: t.default_price || 0,
          duration: t.default_duration || 30
        })),
        start_time: startDateTime, // Campo combinado para backend
        // Mantenemos date y time para frontend
        date: appointmentDate.value,
        time: appointmentTime.value,
        duration: appointmentDuration.value,
        notes: appointmentNotes.value,
      };

      if (props.appointmentId) {
        formData.id = props.appointmentId;
      }

      console.log('Datos de la cita a enviar:', formData);
      emit('submit', formData);
    }
    
    // Inicialización
    onMounted(() => {
      console.log('Componente montado');
      console.log('Datos recibidos:');
      console.log('- Doctores:', props.doctors.length, 'items');
      console.log('- Tipos de cita:', props.appointmentTypes.length, 'items');
      console.log('- Terapias:', props.therapies.length, 'items');
      console.log('- Terapeutas:', props.therapists.length, 'items');

      // Inicializar fecha
      appointmentDate.value = props.defaultDate || format(new Date(), 'yyyy-MM-dd');

      // Inicializar hora predeterminada (9:00 AM)
      appointmentTime.value = '09:00';

      // Si hay preselecciones, avanza los pasos automáticamente
      if (props.preselectedPatient) {
        selectedPatient.value = props.preselectedPatient;
      }

      if (props.preselectedDoctor) {
        selectedDoctor.value = props.preselectedDoctor;
      }

      if (props.preselectedType) {
        selectedType.value = props.preselectedType;
        appointmentDuration.value = props.preselectedType.default_duration || 60;
      }
    });
    
    return {
      // Estado y valores seleccionados
      currentStep,
      patientSearch,
      patients,
      isSearching,
      selectedPatient,
      selectedDoctor,
      selectedType,
      selectedTherapies,
      
      // Detalles de la cita
      appointmentDate,
      appointmentTime,
      appointmentDuration,
      appointmentNotes,
      
      // Propiedades calculadas
      today,
      canProceed,
      
      // Métodos
      searchPatients,
      selectPatient,
      selectDoctor,
      selectAppointmentType,
      toggleTherapy,
      isTherapySelected,
      goToNextStep,
      prevStep,
      submitAppointment
    };
  }
};
</script>

<style>
.step-container {
  margin-bottom: 1rem;
}

.step-title {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #1f2937;
}

.bg-naturalbio-verde {
  background-color: #247868;
}

.bg-naturalbio-verde-700 {
  background-color: #1d5f54;
}

.ring-naturalbio-verde {
  --tw-ring-color: #247868;
}
</style>