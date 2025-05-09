<template>
  <div class="appointment-calendar">
    <!-- Selector de vistas (Día, Semana, Mes) -->
    <div class="flex justify-between items-center mb-4">
      <div class="flex items-center space-x-2">
        <button 
          @click="calendar.prev()" 
          class="p-2 rounded-full hover:bg-gray-100 text-naturalbio-gris"
          aria-label="Período anterior"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        
        <button 
          @click="goToToday" 
          class="px-3 py-1 text-sm font-medium rounded-md bg-naturalbio-verde-50 text-naturalbio-verde hover:bg-naturalbio-verde-100"
        >
          Hoy
        </button>
        
        <button 
          @click="calendar.next()" 
          class="p-2 rounded-full hover:bg-gray-100 text-naturalbio-gris"
          aria-label="Período siguiente"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
        
        <h2 class="text-xl font-semibold text-naturalbio-verde ml-2">
          {{ calendarTitle }}
        </h2>
      </div>
      
      <div class="bg-gray-100 inline-flex rounded-lg p-1 shadow-sm">
        <button 
          @click="changeView('timeGridDay')" 
          :class="[
            'px-4 py-2 text-sm font-medium rounded-md transition-colors',
            currentView === 'timeGridDay' 
              ? 'bg-white text-naturalbio-verde shadow-sm' 
              : 'text-gray-500 hover:text-naturalbio-verde'
          ]"
        >
          Día
        </button>
        <button 
          @click="changeView('timeGridWeek')" 
          :class="[
            'px-4 py-2 text-sm font-medium rounded-md transition-colors',
            currentView === 'timeGridWeek' 
              ? 'bg-white text-naturalbio-verde shadow-sm' 
              : 'text-gray-500 hover:text-naturalbio-verde'
          ]"
        >
          Semana
        </button>
        <button 
          @click="changeView('dayGridMonth')" 
          :class="[
            'px-4 py-2 text-sm font-medium rounded-md transition-colors',
            currentView === 'dayGridMonth' 
              ? 'bg-white text-naturalbio-verde shadow-sm' 
              : 'text-gray-500 hover:text-naturalbio-verde'
          ]"
        >
          Mes
        </button>
      </div>
    </div>
    
    <!-- Componente FullCalendar -->
    <FullCalendar 
      ref="fullCalendar"
      :options="calendarOptions" 
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

// Importaciones de FullCalendar
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';

const props = defineProps({
  appointments: {
    type: Array,
    default: () => []
  },
  appointmentTypes: {
    type: Array,
    default: () => []
  },
  initialView: {
    type: String,
    default: 'timeGridDay' // Por defecto vista diaria
  }
});

const emit = defineEmits(['date-change', 'appointment-click']);

const fullCalendar = ref(null);
const currentView = ref(props.initialView);
const calendarTitle = ref('');
const currentDate = ref(new Date());

// Referencia al API de FullCalendar
const calendar = computed(() => {
  return fullCalendar.value ? fullCalendar.value.getApi() : null;
});

// Transformar citas al formato de eventos de FullCalendar
const calendarEvents = computed(() => {
  return props.appointments.map(appointment => {
    // Determinar el color basado en el tipo de cita o estado
    let backgroundColor = getAppointmentTypeColor(appointment.appointment_type_id);
    
    // Para citas canceladas o no asistidas, usar colores diferentes
    if (appointment.status === 'cancelled') {
      backgroundColor = '#546E7A'; // Gris Piedra (naturalbio-gris)
    } else if (appointment.status === 'no-show') {
      backgroundColor = '#FBC02D'; // Dorado Natural (naturalbio-dorado)
    } else if (appointment.status === 'completed') {
      backgroundColor = '#00ACC1'; // Azul Serenidad (naturalbio-azul)
    }
    
    return {
      id: appointment.id,
      title: appointment.patient ? appointment.patient.name : 'Sin paciente',
      start: appointment.start_time,
      end: appointment.end_time,
      backgroundColor: backgroundColor,
      borderColor: backgroundColor,
      textColor: '#ffffff',
      extendedProps: {
        status: appointment.status,
        type: appointment.type ? appointment.type.name : 'Sin tipo',
        patient: appointment.patient,
        doctor: appointment.doctor,
        fullAppointment: appointment
      }
    };
  });
});

// Opciones de configuración para FullCalendar
const calendarOptions = computed(() => {
  return {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: currentView.value,
    headerToolbar: false, // Ocultamos la barra de herramientas predeterminada
    locale: esLocale,
    events: calendarEvents.value,
    editable: false, // No permitimos arrastrar eventos
    selectable: true, // Permitimos seleccionar fechas
    selectMirror: true,
    dayMaxEvents: true, // Permitir "más" link cuando hay demasiados eventos
    weekends: true, // Mostrar fines de semana
    nowIndicator: true, // Mostrar indicador de "ahora" en las vistas de día y semana
    allDaySlot: false, // No mostrar el slot "todo el día"
    slotDuration: '00:15:00', // Intervalos de 15 minutos
    slotLabelFormat: {
      hour: '2-digit',
      minute: '2-digit',
      omitZeroMinute: false,
      hour12: true
    },
    // Horario de inicio y fin para las vistas de día y semana
    slotMinTime: '07:00:00',
    slotMaxTime: '19:00:00',
    // Formato de eventos
    eventTimeFormat: {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    },
    // Manejadores de eventos
    dateClick: handleDateClick,
    eventClick: handleEventClick,
    datesSet: handleDatesSet
  };
});

// Métodos
function changeView(viewName) {
  currentView.value = viewName;
  if (calendar.value) {
    calendar.value.changeView(viewName);
  }
}

function goToToday() {
  if (calendar.value) {
    calendar.value.today();
  }
}

function handleDateClick(info) {
  // Al hacer clic en una fecha/hora
  console.log('Date click:', info.dateStr);
}

function handleEventClick(info) {
  // Al hacer clic en un evento (cita)
  const appointment = info.event.extendedProps.fullAppointment;
  emit('appointment-click', appointment);
}

function handleDatesSet(dateInfo) {
  // Cuando cambia el rango de fechas mostrado
  const calendarApi = dateInfo.view.calendar;
  
  // Actualizar título del calendario
  updateCalendarTitle(dateInfo);
  
  // Emitir cambio de fecha para que se carguen nuevas citas si es necesario
  const startDate = new Date(dateInfo.startStr);
  emit('date-change', {
    year: startDate.getFullYear().toString(),
    month: (startDate.getMonth() + 1).toString().padStart(2, '0')
  });
}

function updateCalendarTitle(dateInfo) {
  const view = dateInfo.view;
  let title = '';
  
  if (view.type === 'timeGridDay') {
    // Formato: "Lunes, 13 Mayo 2025"
    title = format(new Date(dateInfo.startStr), 'EEEE, d MMMM yyyy', { locale: es });
    // Capitalizar primera letra
    title = title.charAt(0).toUpperCase() + title.slice(1);
  } 
  else if (view.type === 'timeGridWeek') {
    // Formato: "13 - 19 Mayo 2025"
    const start = new Date(dateInfo.startStr);
    const end = new Date(dateInfo.endStr);
    end.setDate(end.getDate() - 1); // Ajustar porque endStr es exclusivo
    
    if (start.getMonth() === end.getMonth()) {
      title = `${start.getDate()} - ${end.getDate()} ${format(start, 'MMMM yyyy', { locale: es })}`;
    } else {
      title = `${format(start, 'd MMM', { locale: es })} - ${format(end, 'd MMM yyyy', { locale: es })}`;
    }
  } 
  else if (view.type === 'dayGridMonth') {
    // Formato: "Mayo 2025"
    title = format(new Date(dateInfo.startStr), 'MMMM yyyy', { locale: es });
    // Capitalizar primera letra
    title = title.charAt(0).toUpperCase() + title.slice(1);
  }
  
  calendarTitle.value = title;
}

function getAppointmentTypeColor(typeId) {
  const appointmentType = props.appointmentTypes.find(type => type.id === typeId);
  return appointmentType?.color || '#4CAF50'; // Color por defecto (verde naturalbio)
}

// Observar cambios en las citas
watch(() => props.appointments, () => {
  if (calendar.value) {
    calendar.value.refetchEvents();
  }
}, { deep: true });

// Observar cambios en la vista inicial
watch(() => props.initialView, (newView) => {
  currentView.value = newView;
  if (calendar.value) {
    calendar.value.changeView(newView);
  }
});

// Al montar el componente
onMounted(() => {
  // Asegurarse de que el calendario está renderizado
  setTimeout(() => {
    if (calendar.value) {
      // Inicializar vista con la propiedad
      calendar.value.changeView(currentView.value);
      // Obtener el rango de fechas actual
      const dateInfo = calendar.value.currentData;
      if (dateInfo) {
        updateCalendarTitle(dateInfo);
      }
    }
  }, 100);
});
</script>

<style>
/* Estilos para FullCalendar */
.appointment-calendar {
  @apply w-full;
}

/* Personalización de FullCalendar */
.fc .fc-daygrid-day-number, 
.fc .fc-col-header-cell-cushion {
  @apply text-naturalbio-gris;
}

.fc .fc-daygrid-day.fc-day-today {
  @apply bg-naturalbio-verde-50;
}

.fc .fc-timegrid-now-indicator-line {
  @apply border-naturalbio-verde;
}

.fc .fc-timegrid-now-indicator-arrow {
  @apply border-naturalbio-verde;
}

.fc-theme-standard .fc-list-day-cushion {
  @apply bg-naturalbio-verde-50;
}

/* Vista diaria y semanal - horario */
.fc-timegrid-event .fc-event-main {
  @apply p-1;
}

.fc-timegrid-event .fc-event-title {
  @apply font-medium text-sm;
}

/* Vista mensual - eventos */
.fc-daygrid-event {
  @apply rounded-md text-xs;
}

.fc .fc-button-primary {
  @apply bg-naturalbio-verde border-naturalbio-verde;
}

.fc .fc-button-primary:hover {
  @apply bg-naturalbio-verde-700 border-naturalbio-verde-700;
}

.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
  @apply bg-naturalbio-verde-700 border-naturalbio-verde-700;
}

.fc .fc-highlight {
  @apply bg-naturalbio-verde-100;
}
</style>