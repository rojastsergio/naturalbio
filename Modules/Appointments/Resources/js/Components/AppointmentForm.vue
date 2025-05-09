<template>
    <div class="appointment-form">
      <div v-if="currentStep === 1" class="step-container">
        <h3 class="step-title">Paso 1: Seleccionar Paciente</h3>
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
        <div v-else>
          <div class="mb-4">
            <label for="patient-search" class="block text-sm font-medium text-gray-700">Buscar paciente</label>
            <div class="mt-1 relative">
              <input
                id="patient-search"
                v-model="patientSearch"
                class="shadow-sm focus:ring-naturalbio-verde focus:border-naturalbio-verde block w-full sm:text-sm border-gray-300 rounded-md"
                placeholder="Buscar por nombre o expediente"
                @input="searchPatients"
              />
              <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
            </div>
          </div>
          
          <div v-if="patients.length > 0" class="patient-list">
            <div
              v-for="patient in patients"
              :key="patient.id"
              class="patient-item"
              :class="{ 'border-naturalbio-verde': selectedPatient?.id === patient.id }"
              @click="selectPatient(patient)"
            >
              <div class="flex items-center">
                <div v-if="patient.photo_path" class="mr-3">
                  <img :src="patient.photo_path" alt="Foto del paciente" class="h-10 w-10 rounded-full object-cover" />
                </div>
                <div class="min-w-0 flex-1">
                  <div class="font-semibold truncate">{{ patient.name }} {{ patient.last_name }}</div>
                  <div class="text-sm text-gray-600">{{ patient.expedient_number || 'Sin expediente' }}</div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else-if="patientSearch && !isSearching" class="text-gray-500 text-center py-4">
            No se encontraron pacientes. <a href="#" class="text-naturalbio-verde hover:underline" @click.prevent="goToCreatePatient">Crear nuevo paciente</a>
          </div>
        </div>
      </div>
      
      <div v-if="currentStep === 2" class="step-container">
        <h3 class="step-title">Paso 2: Seleccionar Doctor (Opcional)</h3>
        <div class="mb-4">
          <label for="doctor-select" class="block text-sm font-medium text-gray-700">Doctor</label>
          <select
            id="doctor-select"
            v-model="selectedDoctor"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-naturalbio-verde focus:border-naturalbio-verde sm:text-sm rounded-md"
          >
            <option :value="null">Sin asignar</option>
            <option v-for="doctor in doctors" :key="doctor.id" :value="doctor">
              {{ doctor.name }}
            </option>
          </select>
        </div>
      </div>
      
      <div v-if="currentStep === 3" class="step-container">
        <h3 class="step-title">Paso 3: Tipo de Cita</h3>
        <div class="grid grid-cols-2 gap-3">
          <div
            v-for="type in appointmentTypes"
            :key="type.id"
            class="appointment-type-card"
            :class="{ 'ring-2 ring-naturalbio-verde': selectedType?.id === type.id }"
            :style="{ borderLeftColor: type.color }"
            @click="selectAppointmentType(type)"
          >
            <div class="font-medium">{{ type.name }}</div>
            <div class="text-sm text-gray-600">
              <span>{{ formatPrice(type.default_price) }}</span>
              <span class="mx-1">•</span>
              <span>{{ type.default_duration }} min</span>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="currentStep === 4" class="step-container">
        <h3 class="step-title">Paso 4: Terapias</h3>
        <div v-if="therapies.length === 0" class="text-gray-500 text-center py-4">
          No hay terapias disponibles.
        </div>
        <div v-else class="space-y-3">
          <div
            v-for="therapy in therapies"
            :key="therapy.id"
            class="therapy-card"
            :class="{ 'ring-2 ring-naturalbio-verde': isTherapySelected(therapy.id) }"
            @click="toggleTherapy(therapy)"
          >
            <div class="flex items-start">
              <div class="h-5 w-5 mr-2">
                <input
                  type="checkbox"
                  :checked="isTherapySelected(therapy.id)"
                  class="h-4 w-4 text-naturalbio-verde focus:ring-naturalbio-verde border-gray-300 rounded"
                  @click.stop
                  @change="toggleTherapy(therapy)"
                />
              </div>
              <div class="flex-grow">
                <div class="font-medium">{{ therapy.name }}</div>
                <div class="text-sm text-gray-600">
                  <span>{{ formatPrice(therapy.default_price) }}</span>
                  <span class="mx-1">•</span>
                  <span>{{ therapy.default_duration }} min</span>
                </div>
              </div>
            </div>
            
            <div v-if="isTherapySelected(therapy.id)" class="mt-2 pt-2 border-t border-gray-200">
              <div>
                <label :for="`therapy-price-${therapy.id}`" class="block text-xs font-medium text-gray-700">Precio</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">Q</span>
                  </div>
                  <input
                    :id="`therapy-price-${therapy.id}`"
                    type="number"
                    min="0"
                    step="0.01"
                    :value="getSelectedTherapyPrice(therapy.id)"
                    class="focus:ring-naturalbio-verde focus:border-naturalbio-verde block w-full pl-7 sm:text-sm border-gray-300 rounded-md"
                    @input="updateTherapyPrice(therapy.id, $event.target.value)"
                  />
                </div>
              </div>
              
              <div class="mt-2">
                <label :for="`therapy-therapist-${therapy.id}`" class="block text-xs font-medium text-gray-700">Terapista (Opcional)</label>
                <select
                  :id="`therapy-therapist-${therapy.id}`"
                  :value="getSelectedTherapyTherapist(therapy.id)"
                  class="mt-1 block w-full pl-3 pr-10 py-1 text-sm border-gray-300 focus:outline-none focus:ring-naturalbio-verde focus:border-naturalbio-verde rounded-md"
                  @change="updateTherapyTherapist(therapy.id, $event.target.value)"
                >
                  <option :value="null">Sin asignar</option>
                  <option v-for="therapist in therapists" :key="therapist.id" :value="therapist.id">
                    {{ therapist.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="currentStep === 5" class="step-container">
        <h3 class="step-title">Paso 5: Fecha y Hora</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="appointment-date" class="block text-sm font-medium text-gray-700">Fecha</label>
            <input
              id="appointment-date"
              type="date"
              v-model="appointmentDate"
              class="mt-1 focus:ring-naturalbio-verde focus:border-naturalbio-verde block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              :min="today"
              @change="loadAvailability"
            />
          </div>
          
          <div>
            <label for="appointment-time" class="block text-sm font-medium text-gray-700">Hora</label>
            <div class="mt-1">
              <div v-if="loadingAvailability" class="flex items-center justify-center py-2 text-sm text-gray-500">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-naturalbio-verde" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cargando disponibilidad...
              </div>
              
              <div v-else-if="availableSlots.length === 0 && selectedDoctor">
                <div class="text-sm text-red-600">
                  No hay horarios disponibles para el doctor seleccionado en esta fecha.
                </div>
              </div>
              
              <div v-else-if="!selectedDoctor">
                <input
                  id="appointment-time"
                  type="time"
                  v-model="appointmentTime"
                  class="focus:ring-naturalbio-verde focus:border-naturalbio-verde block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                />
              </div>
              
              <div v-else class="grid grid-cols-3 gap-2">
                <button
                  v-for="slot in availableSlots"
                  :key="`${slot.start}-${slot.end}`"
                  type="button"
                  class="time-slot-btn"
                  :class="{ 'bg-naturalbio-verde text-white': appointmentTime === slot.start }"
                  @click="selectTimeSlot(slot)"
                >
                  {{ slot.start }}
                </button>
              </div>
            </div>
          </div>
          
          <div class="md:col-span-2">
            <label for="appointment-duration" class="block text-sm font-medium text-gray-700">Duración (minutos)</label>
            <input
              id="appointment-duration"
              type="number"
              v-model.number="appointmentDuration"
              min="1"
              class="mt-1 focus:ring-naturalbio-verde focus:border-naturalbio-verde block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
        </div>
      </div>
      
      <div v-if="currentStep === 6" class="step-container">
        <h3 class="step-title">Paso 6: Notas</h3>
        <div>
          <label for="appointment-notes" class="block text-sm font-medium text-gray-700">Notas para la cita</label>
          <textarea
            id="appointment-notes"
            v-model="appointmentNotes"
            rows="4"
            class="mt-1 focus:ring-naturalbio-verde focus:border-naturalbio-verde block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            placeholder="Agregue cualquier nota relevante para esta cita..."
          ></textarea>
        </div>
        
        <div class="mt-6">
          <h4 class="font-medium text-gray-700">Resumen de la Cita</h4>
          <div class="mt-2 bg-gray-50 rounded-md p-3">
            <dl class="divide-y divide-gray-200">
              <div class="py-2 grid grid-cols-3">
                <dt class="text-sm font-medium text-gray-500">Paciente</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ selectedPatient?.name }} {{ selectedPatient?.last_name }}</dd>
              </div>
              <div class="py-2 grid grid-cols-3">
                <dt class="text-sm font-medium text-gray-500">Doctor</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ selectedDoctor?.name || 'Sin asignar' }}</dd>
              </div>
              <div class="py-2 grid grid-cols-3">
                <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ selectedType?.name || 'Sin tipo' }}</dd>
              </div>
              <div class="py-2 grid grid-cols-3">
                <dt class="text-sm font-medium text-gray-500">Terapias</dt>
                <dd class="text-sm text-gray-900 col-span-2">
                  <span v-if="selectedTherapies.length === 0">Sin terapias</span>
                  <ul v-else class="list-disc pl-5">
                    <li v-for="therapy in selectedTherapies" :key="therapy.id">
                      {{ therapy.name }} ({{ formatPrice(therapy.price) }})
                    </li>
                  </ul>
                </dd>
              </div>
              <div class="py-2 grid grid-cols-3">
                <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
                <dd class="text-sm text-gray-900 col-span-2">
                  {{ formatAppointmentDateTime() }}
                </dd>
              </div>
              <div class="py-2 grid grid-cols-3">
                <dt class="text-sm font-medium text-gray-500">Duración</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ appointmentDuration }} minutos</dd>
              </div>
              <div class="py-2 grid grid-cols-3">
                <dt class="text-sm font-medium text-gray-500">Precio Total</dt>
                <dd class="text-sm font-medium text-naturalbio-verde col-span-2">{{ formatPrice(calculateTotalPrice()) }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
      
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
          @click="nextStep"
          :disabled="!canProceed"
        >
          Siguiente
        </button>
        <button
          v-else
          type="button"
          class="px-4 py-2 text-sm font-medium text-white bg-naturalbio-verde border border-transparent rounded-md shadow-sm hover:bg-naturalbio-verde-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde"
          @click="submitForm"
        >
          Crear Cita
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, watch } from 'vue';
  import { format } from 'date-fns';
  import { es } from 'date-fns/locale';
  import { router } from '@inertiajs/vue3';
  import axios from 'axios';
  
  const props = defineProps({
    appointmentTypes: {
      type: Array,
      default: () => []
    },
    doctors: {
      type: Array,
      default: () => []
    },
    therapists: {
      type: Array,
      default: () => []
    },
    therapies: {
      type: Array,
      default: () => []
    },
    preselectedPatient: {
      type: Object,
      default: null
    },
    defaultDate: {
      type: String,
      default: () => format(new Date(), 'yyyy-MM-dd')
    }
  });
  
  const emit = defineEmits(['submit']);
  
  // Estado del formulario
  const currentStep = ref(1);
  const patientSearch = ref('');
  const patients = ref([]);
  const isSearching = ref(false);
  const selectedPatient = ref(null);
  const selectedDoctor = ref(null);
  const selectedType = ref(null);
  const selectedTherapies = ref([]);
  const appointmentDate = ref(props.defaultDate);
  const appointmentTime = ref('');
  const appointmentDuration = ref(60);
  const appointmentNotes = ref('');
  const availableSlots = ref([]);
  const loadingAvailability = ref(false);
  
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
    
    axios.get(route('api.patients.search', {
      search: patientSearch.value
    }))
      .then(response => {
        patients.value = response.data.patients;
      })
      .catch(error => {
        console.error('Error al buscar pacientes:', error);
      })
      .finally(() => {
        isSearching.value = false;
      });
  }
  
  function selectPatient(patient) {
    selectedPatient.value = patient;
  }
  
  function selectAppointmentType(type) {
    selectedType.value = type;
    appointmentDuration.value = type.default_duration || 60;
  }
  
  function toggleTherapy(therapy) {
    const index = selectedTherapies.value.findIndex(t => t.id === therapy.id);
    
    if (index >= 0) {
      selectedTherapies.value.splice(index, 1);
    } else {
      selectedTherapies.value.push({
        id: therapy.id,
        name: therapy.name,
        price: therapy.default_price || 0,
        duration: therapy.default_duration || 30,
        therapist_id: null
      });
    }
  }
  
  function isTherapySelected(therapyId) {
    return selectedTherapies.value.some(therapy => therapy.id === therapyId);
  }
  
  function getSelectedTherapyPrice(therapyId) {
    const therapy = selectedTherapies.value.find(t => t.id === therapyId);
    return therapy ? therapy.price : 0;
  }
  
  function getSelectedTherapyTherapist(therapyId) {
    const therapy = selectedTherapies.value.find(t => t.id === therapyId);
    return therapy ? therapy.therapist_id : null;
  }
  
  function updateTherapyPrice(therapyId, price) {
    const therapy = selectedTherapies.value.find(t => t.id === therapyId);
    if (therapy) {
      therapy.price = parseFloat(price) || 0;
    }
  }
  
  function updateTherapyTherapist(therapyId, therapistId) {
    const therapy = selectedTherapies.value.find(t => t.id === therapyId);
    if (therapy) {
      therapy.therapist_id = therapistId ? parseInt(therapistId) : null;
    }
  }
  
  function loadAvailability() {
    if (!selectedDoctor.value || !appointmentDate.value) {
      availableSlots.value = [];
      return;
    }
    
    loadingAvailability.value = true;
    
    axios.get(route('api.appointments.availability'), {
      params: {
        doctor_id: selectedDoctor.value.id,
        date: appointmentDate.value,
        duration: appointmentDuration.value
      }
    })
      .then(response => {
        availableSlots.value = response.data.slots || [];
        
        // Resetear tiempo seleccionado si no está disponible
        if (appointmentTime.value && !availableSlots.value.some(slot => slot.start === appointmentTime.value)) {
          appointmentTime.value = '';
        }
      })
      .catch(error => {
        console.error('Error al cargar disponibilidad:', error);
        availableSlots.value = [];
      })
      .finally(() => {
        loadingAvailability.value = false;
      });
  }
  
  function selectTimeSlot(slot) {
    appointmentTime.value = slot.start;
  }
  
  function calculateTotalPrice() {
    const typePrice = selectedType.value ? selectedType.value.default_price || 0 : 0;
    const therapiesPrice = selectedTherapies.value.reduce((sum, therapy) => sum + (therapy.price || 0), 0);
    
    return typePrice + therapiesPrice;
  }
  
  function formatPrice(price) {
    return `Q ${parseFloat(price || 0).toFixed(2)}`;
  }
  
  function formatAppointmentDateTime() {
    if (!appointmentDate.value || !appointmentTime.value) {
      return 'No especificado';
    }
    
    const dateObj = new Date(`${appointmentDate.value}T${appointmentTime.value}`);
    
    return format(dateObj, 'EEEE, d MMMM yyyy HH:mm', { locale: es });
  }
  
  function nextStep() {
    if (currentStep.value < 6 && canProceed.value) {
      currentStep.value++;
      
      // Cargar disponibilidad al llegar al paso 5
      if (currentStep.value === 5) {
        loadAvailability();
      }
    }
  }
  
  function prevStep() {
    if (currentStep.value > 1) {
      currentStep.value--;
    }
  }
  
  function goToCreatePatient() {
    router.visit(route('patients.create'));
  }
  
  function submitForm() {
    const startDateTime = new Date(`${appointmentDate.value}T${appointmentTime.value}`);
    
    const formData = {
      patient_id: selectedPatient.value.id,
      doctor_id: selectedDoctor.value ? selectedDoctor.value.id : null,
      appointment_type_id: selectedType.value ? selectedType.value.id : null,
      start_time: startDateTime.toISOString(),
      duration: appointmentDuration.value,
      notes: appointmentNotes.value,
      therapies: selectedTherapies.value.map(therapy => ({
        id: therapy.id,
        price: therapy.price,
        duration: therapy.duration,
        therapist_id: therapy.therapist_id
      }))
    };
    
    emit('submit', formData);
  }
  
  // Watchers
  watch(() => props.preselectedPatient, (newVal) => {
    if (newVal) {
      selectedPatient.value = newVal;
    }
  }, { immediate: true });
  
  watch([selectedDoctor, appointmentDate, appointmentDuration], () => {
    loadAvailability();
  });
  
  onMounted(() => {
    // Si hay un paciente preseleccionado, avanzar al paso 2
    if (props.preselectedPatient) {
      selectedPatient.value = props.preselectedPatient;
      nextStep();
    }
  });
  </script>
  
  <style scoped>
  .step-container {
    @apply space-y-4;
  }
  
  .step-title {
    @apply text-lg font-medium text-gray-900 mb-4;
  }
  
  .selected-patient {
    @apply p-4 border border-gray-300 rounded-md;
  }
  
  .patient-list {
    @apply divide-y divide-gray-200 max-h-64 overflow-y-auto border border-gray-300 rounded-md;
  }
  
  .patient-item {
    @apply p-3 hover:bg-gray-50 cursor-pointer border-l-4 border-transparent;
  }
  
  .appointment-type-card {
    @apply p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 border-l-4;
  }
  
  .therapy-card {
    @apply p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50;
  }
  
  .time-slot-btn {
    @apply py-2 px-1 text-center text-sm border border-gray-300 rounded-md hover:bg-gray-100;
  }
  </style>