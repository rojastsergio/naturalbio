<template>
  <div class="availability-calendar bg-white p-4 rounded-lg shadow-sm">
    <div class="mb-4 flex justify-between items-center">
      <h3 class="text-lg font-medium text-naturalbio-verde">Calendario de Disponibilidad</h3>
      
      <div class="flex items-center gap-4">
        <!-- Selector de vistas -->
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
        
        <!-- Botón nueva disponibilidad -->
        <button
          class="bg-naturalbio-verde text-white px-4 py-2 rounded-md flex items-center"
          @click="$emit('new-availability')"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Nueva Disponibilidad
        </button>
      </div>
    </div>
    
    <div id="availability-calendar" class="calendar-container"></div>
  </div>
</template>

<script>
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

export default {
  props: {
    availabilities: {
      type: Array,
      required: true
    },
    appointments: {
      type: Array,
      default: () => []
    },
    initialView: {
      type: String,
      default: 'timeGridWeek'
    },
    allowEditing: {
      type: Boolean,
      default: true
    }
  },
  
  emits: [
    'new-availability', 
    'edit-availability', 
    'delete-availability', 
    'availability-create',
    'month-changed'
  ],
  
  data() {
    return {
      calendar: null,
      currentView: this.initialView,
    };
  },
  
  computed: {
    calendarEvents() {
      // Convertir disponibilidades al formato de eventos de FullCalendar
      const availabilityEvents = this.availabilities.map(availability => {
        // Asegurarse de que la fecha y hora están en el formato correcto
        const startStr = this.formatDateTime(availability.date, availability.start_time);
        const endStr = this.formatDateTime(availability.date, availability.end_time);
        
        return {
          id: `avail_${availability.id}`,
          title: 'Disponible',
          start: startStr,
          end: endStr,
          backgroundColor: '#4CAF50', // Verde - Disponible
          borderColor: '#4CAF50',
          textColor: '#FFFFFF',
          extendedProps: {
            type: 'availability',
            originalData: availability
          }
        };
      });
      
      // Convertir citas al formato de eventos de FullCalendar (si se proporcionan)
      const appointmentEvents = this.appointments.map(appointment => {
        return {
          id: `appt_${appointment.id}`,
          title: appointment.patient ? `Cita: ${appointment.patient.name}` : 'Cita sin paciente',
          start: appointment.start_time,
          end: appointment.end_time,
          backgroundColor: '#FBC02D', // Dorado - Ocupado
          borderColor: '#FBC02D',
          textColor: '#333333',
          extendedProps: {
            type: 'appointment',
            originalData: appointment
          }
        };
      });
      
      // Combinar ambos tipos de eventos
      return [...availabilityEvents, ...appointmentEvents];
    }
  },
  
  watch: {
    availabilities: {
      handler() {
        this.updateCalendarEvents();
      },
      deep: true
    },
    
    appointments: {
      handler() {
        this.updateCalendarEvents();
      },
      deep: true
    }
  },
  
  mounted() {
    this.initializeCalendar();
  },
  
  beforeUnmount() {
    if (this.calendar) {
      this.calendar.destroy();
    }
  },
  
  methods: {
    initializeCalendar() {
      // Asegurar que los estilos de FullCalendar estén cargados
      this.loadFullCalendarStyles();
      
      const calendarEl = document.getElementById('availability-calendar');
      if (!calendarEl) return;
      
      this.calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: this.currentView,
        locale: 'es',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: '' // Ocultamos los controles de vista predeterminados
        },
        events: this.calendarEvents,
        nowIndicator: true,
        allDaySlot: false,
        slotDuration: '00:15:00',
        slotLabelFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        },
        eventTimeFormat: {
          hour: '2-digit',
          minute: '2-digit',
          hour12: false
        },
        
        // Manejo de eventos
        eventClick: this.handleEventClick,
        
        // Para creación de disponibilidades mediante selección
        selectable: this.allowEditing,
        select: this.handleDateSelect,
        
        // Permitir arrastrar y redimensionar eventos
        editable: this.allowEditing,
        eventResize: this.handleEventResize,
        eventDrop: this.handleEventDrop,
        
        // Cambio de fecha
        datesSet: (info) => {
          const startDate = new Date(info.start);
          const formattedDate = startDate.toISOString().split('T')[0];
          this.$emit('month-changed', formattedDate);
        },
        
        // Personalizaciones visuales
        eventContent: this.renderEventContent
      });
      
      this.calendar.render();
    },
    
    loadFullCalendarStyles() {
      // Si el CSS de FullCalendar no está ya cargado, lo añadimos
      if (!document.getElementById('fullcalendar-styles')) {
        const link = document.createElement('link');
        link.id = 'fullcalendar-styles';
        link.rel = 'stylesheet';
        link.href = 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css';
        document.head.appendChild(link);
      }
    },
    
    updateCalendarEvents() {
      if (this.calendar) {
        this.calendar.removeAllEvents();
        this.calendar.addEventSource(this.calendarEvents);
      }
    },
    
    changeView(viewName) {
      this.currentView = viewName;
      if (this.calendar) {
        this.calendar.changeView(viewName);
      }
    },
    
    formatDateTime(date, time) {
      // Convertir fecha y hora a un string ISO para FullCalendar
      if (!date || !time) return '';
      
      // Si ya es un objeto Date o un string ISO completo, devolvemos como está
      if (time instanceof Date || (typeof time === 'string' && time.includes('T'))) {
        return time;
      }
      
      // Combinar fecha y hora
      return `${date}T${time}`;
    },
    
    handleEventClick(info) {
      const eventData = info.event.extendedProps.originalData;
      const eventType = info.event.extendedProps.type;
      
      if (eventType === 'availability') {
        // Solo permitimos editar disponibilidades, no citas
        this.$emit('edit-availability', eventData);
      }
    },
    
    handleDateSelect(info) {
      if (!this.allowEditing) return;
      
      // Cuando el usuario selecciona un rango de tiempo, crear una nueva disponibilidad
      const availabilityData = {
        date: info.start.toISOString().split('T')[0],
        start_time: info.start.toISOString().split('T')[1].substring(0, 8),
        end_time: info.end.toISOString().split('T')[1].substring(0, 8)
      };
      
      this.$emit('availability-create', availabilityData);
      this.calendar.unselect(); // Deshacer la selección
    },
    
    handleEventResize(info) {
      if (info.event.extendedProps.type !== 'availability') return;
      
      const updatedAvailability = {
        ...info.event.extendedProps.originalData,
        date: info.event.start.toISOString().split('T')[0],
        start_time: info.event.start.toISOString().split('T')[1].substring(0, 8),
        end_time: info.event.end.toISOString().split('T')[1].substring(0, 8)
      };
      
      this.$emit('edit-availability', updatedAvailability);
    },
    
    handleEventDrop(info) {
      if (info.event.extendedProps.type !== 'availability') return;
      
      const updatedAvailability = {
        ...info.event.extendedProps.originalData,
        date: info.event.start.toISOString().split('T')[0],
        start_time: info.event.start.toISOString().split('T')[1].substring(0, 8),
        end_time: info.event.end.toISOString().split('T')[1].substring(0, 8)
      };
      
      this.$emit('edit-availability', updatedAvailability);
    },
    
    renderEventContent(info) {
      const eventType = info.event.extendedProps.type;
      
      // Personalizar el aspecto de los eventos según su tipo
      if (eventType === 'availability') {
        return {
          html: `
            <div class="fc-event-main-content">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>${this.formatTimeRange(info.event.start, info.event.end)}</span>
              </div>
            </div>
          `
        };
      } else if (eventType === 'appointment') {
        return {
          html: `
            <div class="fc-event-main-content">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>${info.event.title}</span>
              </div>
            </div>
          `
        };
      }
      
      return null;
    },
    
    formatTimeRange(start, end) {
      const startTime = start.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
      const endTime = end.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
      return `${startTime} - ${endTime}`;
    }
  }
};
</script>

<style scoped>
.availability-calendar {
  width: 100%;
}

.calendar-container {
  height: 600px;
}

/* Personalización adicional para adaptarse al estilo de NaturalBIO */
:deep(.fc-button-primary) {
  background-color: #247868 !important;
  border-color: #247868 !important;
}

:deep(.fc-button-primary:hover) {
  background-color: #1d5f54 !important;
  border-color: #1d5f54 !important;
}

:deep(.fc-button-active) {
  background-color: #1d5f54 !important;
  border-color: #1d5f54 !important;
}

:deep(.fc-today-button) {
  background-color: #247868 !important;
  border-color: #247868 !important;
}

:deep(.fc-col-header-cell) {
  background-color: #f3f4f6;
}

:deep(.fc-day-today) {
  background-color: rgba(36, 120, 104, 0.1) !important;
}

:deep(.fc-timegrid-now-indicator-line) {
  border-color: #FBC02D !important;
}

:deep(.fc-timegrid-now-indicator-arrow) {
  border-color: #FBC02D !important;
}
</style>