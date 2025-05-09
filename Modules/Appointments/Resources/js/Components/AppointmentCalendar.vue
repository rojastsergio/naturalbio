<template>
  <div class="appointment-calendar bg-white p-4 rounded-lg shadow-sm">
    <div class="mb-4 flex justify-between items-center">
      <h3 class="text-lg font-medium text-naturalbio-verde">Calendario de Citas</h3>
      <div class="bg-gray-100 inline-flex rounded-lg p-1">
        <button 
          @click="changeView('timeGridDay')" 
          :class="[
            'px-3 py-1 text-sm font-medium rounded-md',
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
            'px-3 py-1 text-sm font-medium rounded-md',
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
            'px-3 py-1 text-sm font-medium rounded-md',
            currentView === 'dayGridMonth' 
              ? 'bg-white text-naturalbio-verde shadow-sm' 
              : 'text-gray-500 hover:text-naturalbio-verde'
          ]"
        >
          Mes
        </button>
      </div>
    </div>
    
    <div id="calendar"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

// Importar solo las clases de FullCalendar sin CSS
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

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
    default: 'timeGridDay'
  }
});

const emit = defineEmits(['date-change', 'appointment-click']);

const currentView = ref(props.initialView);
let calendar = null;

// Métodos
function formatEvents() {
  return props.appointments.map(appointment => {
    const color = getAppointmentTypeColor(appointment.appointment_type_id);
    
    return {
      id: appointment.id,
      title: appointment.patient ? appointment.patient.name : 'Sin paciente',
      start: appointment.start_time,
      end: appointment.end_time,
      backgroundColor: color,
      borderColor: color,
      extendedProps: {
        fullAppointment: appointment
      }
    };
  });
}

function getAppointmentTypeColor(typeId) {
  const appointmentType = props.appointmentTypes.find(type => type.id === typeId);
  return appointmentType?.color || '#4CAF50';
}

function initCalendar() {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;
  
  calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: currentView.value,
    locale: 'es',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: '' // Quitamos los selectores de vista predeterminados
    },
    events: formatEvents(),
    nowIndicator: true,
    allDaySlot: false,
    slotDuration: '00:15:00',
    eventClick: (info) => {
      const appointment = info.event.extendedProps.fullAppointment;
      emit('appointment-click', appointment);
    },
    datesSet: (info) => {
      const startDate = new Date(info.start);
      emit('date-change', {
        year: startDate.getFullYear().toString(),
        month: (startDate.getMonth() + 1).toString().padStart(2, '0')
      });
    }
  });
  
  calendar.render();
}

function changeView(viewName) {
  currentView.value = viewName;
  if (calendar) {
    calendar.changeView(viewName);
  }
}

function updateEvents() {
  if (calendar) {
    calendar.removeAllEvents();
    calendar.addEventSource(formatEvents());
  }
}

// Observamos los cambios en las citas
watch(() => props.appointments, () => {
  updateEvents();
}, { deep: true });

// Observamos los cambios en la vista inicial
watch(() => props.initialView, (newView) => {
  currentView.value = newView;
  if (calendar) {
    calendar.changeView(newView);
  }
});

// Inicialización al montar el componente
onMounted(() => {
  // Aseguramos que los CDN de FullCalendar estén cargados
  const links = [
    { rel: 'stylesheet', href: 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' }
  ];
  
  // Añadimos los estilos de FullCalendar desde CDN
  links.forEach(link => {
    const linkEl = document.createElement('link');
    linkEl.rel = link.rel;
    linkEl.href = link.href;
    document.head.appendChild(linkEl);
  });
  
  // Inicializamos el calendario con un pequeño retraso para asegurar que los estilos estén cargados
  setTimeout(() => {
    initCalendar();
  }, 100);
});
</script>

<style scoped>
.appointment-calendar {
  width: 100%;
}

#calendar {
  height: 600px;
}
</style>