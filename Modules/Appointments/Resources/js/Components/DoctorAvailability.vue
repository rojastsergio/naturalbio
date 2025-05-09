<template>
    <div class="doctor-availability-component">
      <div class="bg-white rounded-lg shadow-sm p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Disponibilidad Semanal</h3>
        
        <div class="grid grid-cols-7 gap-2 mb-4">
          <div 
            v-for="(day, index) in daysOfWeek" 
            :key="index"
            class="text-center text-xs font-medium text-gray-600 py-1"
          >
            {{ day }}
          </div>
        </div>
        
        <div
          v-for="(timeSlot, slotIndex) in timeSlots"
          :key="slotIndex"
          class="grid grid-cols-7 gap-2 mb-2"
        >
          <div 
            v-for="(day, dayIndex) in days" 
            :key="`${slotIndex}-${dayIndex}`"
            class="availability-cell"
            :class="{
              'bg-naturalbio-verde text-white': isAvailable(day, timeSlot),
              'cursor-pointer': isEditable
            }"
            @click="isEditable && toggleAvailability(day, timeSlot)"
          >
            <span v-if="dayIndex === 0 || (dayIndex === days.length - 1 && slotIndex % 2 === 0)">
              {{ timeSlot.label }}
            </span>
          </div>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
          <button 
            v-if="isEditable && hasChanges"
            type="button"
            class="px-4 py-2 bg-naturalbio-verde text-white rounded-md hover:bg-naturalbio-verde-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-naturalbio-verde-500"
            @click="saveAvailability"
          >
            Guardar Cambios
          </button>
          
          <div v-if="isEditable" class="text-sm text-gray-500">
            Haga clic en las celdas para marcar/desmarcar disponibilidad
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, watch } from 'vue';
  import { startOfWeek, format, addDays, parseISO, addMinutes } from 'date-fns';
  import { es } from 'date-fns/locale';
  import axios from 'axios';
  
  const props = defineProps({
    doctorId: {
      type: [Number, String],
      required: true
    },
    clinicId: {
      type: [Number, String],
      required: true
    },
    isEditable: {
      type: Boolean,
      default: false
    },
    initialAvailability: {
      type: Array,
      default: () => []
    },
    startHour: {
      type: Number,
      default: 8 // 8:00 AM
    },
    endHour: {
      type: Number,
      default: 18 // 6:00 PM
    },
    slotDuration: {
      type: Number,
      default: 30 // 30 minutos
    }
  });
  
  const emit = defineEmits(['update', 'save']);
  
  // Estado
  const availability = ref([...props.initialAvailability]);
  const originalAvailability = ref([...props.initialAvailability]);
  const isLoading = ref(false);
  
  // Días de la semana
  const daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
  
  // Generar días para la semana actual
  const days = computed(() => {
    const today = new Date();
    const weekStart = startOfWeek(today, { weekStartsOn: 1 }); // Lunes como inicio de semana
    
    return Array.from({ length: 7 }, (_, i) => {
      const date = addDays(weekStart, i);
      return {
        date: format(date, 'yyyy-MM-dd'),
        dayOfWeek: i,
        label: format(date, 'd MMM', { locale: es })
      };
    });
  });
  
  // Generar slots de tiempo
  const timeSlots = computed(() => {
    const slots = [];
    const slotsPerHour = 60 / props.slotDuration;
    const totalHours = props.endHour - props.startHour;
    const totalSlots = totalHours * slotsPerHour;
    
    for (let i = 0; i < totalSlots; i++) {
      const minutes = i * props.slotDuration;
      const timeObj = new Date();
      timeObj.setHours(props.startHour, minutes, 0);
      
      const timeObj2 = new Date();
      timeObj2.setHours(props.startHour, minutes + props.slotDuration, 0);
      
      slots.push({
        start: format(timeObj, 'HH:mm'),
        end: format(timeObj2, 'HH:mm'),
        label: format(timeObj, 'HH:mm')
      });
    }
    
    return slots;
  });
  
  // Comprobar si hay cambios
  const hasChanges = computed(() => {
    if (availability.value.length !== originalAvailability.value.length) {
      return true;
    }
    
    // Comparar cada entrada de disponibilidad
    for (const avail of availability.value) {
      const original = originalAvailability.value.find(
        o => o.date === avail.date && o.start_time === avail.start_time && o.end_time === avail.end_time
      );
      
      if (!original) {
        return true;
      }
    }
    
    // Comprobar si se ha eliminado alguna disponibilidad
    for (const original of originalAvailability.value) {
      const avail = availability.value.find(
        a => a.date === original.date && a.start_time === original.start_time && a.end_time === original.end_time
      );
      
      if (!avail) {
        return true;
      }
    }
    
    return false;
  });
  
  // Métodos
  function isAvailable(day, timeSlot) {
    return availability.value.some(avail => 
      avail.date === day.date && 
      avail.start_time === timeSlot.start && 
      avail.end_time === timeSlot.end
    );
  }
  
  function toggleAvailability(day, timeSlot) {
    const isSlotAvailable = isAvailable(day, timeSlot);
    
    if (isSlotAvailable) {
      // Eliminar disponibilidad
      availability.value = availability.value.filter(avail => 
        !(avail.date === day.date && avail.start_time === timeSlot.start && avail.end_time === timeSlot.end)
      );
    } else {
      // Añadir disponibilidad
      availability.value.push({
        date: day.date,
        start_time: timeSlot.start,
        end_time: timeSlot.end,
        doctor_id: props.doctorId,
        clinic_id: props.clinicId
      });
    }
    
    emit('update', availability.value);
  }
  
  function saveAvailability() {
    isLoading.value = true;
    
    // Preparar datos para enviar al servidor
    const availabilityToSave = availability.value.map(avail => ({
      date: avail.date,
      start_time: avail.start_time,
      end_time: avail.end_time,
      doctor_id: props.doctorId,
      clinic_id: props.clinicId,
      active: true
    }));
    
    // Enviar al servidor (usando axios o fetch API)
    axios.post(route('api.doctor-availabilities.bulk-create'), {
      doctor_id: props.doctorId,
      dates: availabilityToSave.map(a => a.date),
      start_time: availabilityToSave[0]?.start_time,
      end_time: availabilityToSave[0]?.end_time,
      active: true
    })
      .then(response => {
        originalAvailability.value = [...availability.value];
        emit('save', response.data);
      })
      .catch(error => {
        console.error('Error al guardar disponibilidad:', error);
      })
      .finally(() => {
        isLoading.value = false;
      });
  }
  
  // Cargar disponibilidades existentes
  function loadAvailability() {
    if (!props.doctorId || !props.clinicId) {
      return;
    }
    
    isLoading.value = true;
    
    // Obtener fechas de la semana actual
    const weekDates = days.value.map(day => day.date);
    
    axios.get(route('api.doctor-availabilities.index'), {
      params: {
        doctor_id: props.doctorId,
        dates: weekDates
      }
    })
      .then(response => {
        availability.value = response.data.availabilities.map(avail => ({
          id: avail.id,
          date: avail.date,
          start_time: format(parseISO(avail.start_time), 'HH:mm'),
          end_time: format(parseISO(avail.end_time), 'HH:mm'),
          doctor_id: avail.doctor_id,
          clinic_id: avail.clinic_id
        }));
        
        originalAvailability.value = [...availability.value];
      })
      .catch(error => {
        console.error('Error al cargar disponibilidad:', error);
      })
      .finally(() => {
        isLoading.value = false;
      });
  }
  
  // Observar cambios en doctorId o clinicId para recargar
  watch([() => props.doctorId, () => props.clinicId], () => {
    loadAvailability();
  });
  
  // Cargar al montar el componente
  onMounted(() => {
    if (props.initialAvailability.length === 0) {
      loadAvailability();
    }
  });
  </script>
  
  <style scoped>
  .availability-cell {
    @apply h-10 text-xs flex items-center justify-center border border-gray-200 rounded-sm hover:bg-gray-100;
  }
  </style>