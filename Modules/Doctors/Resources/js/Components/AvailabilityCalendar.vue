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
    'month-changed',
    'appointment-click'
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
          backgroundColor: '#8BC34A', // Verde claro - Disponible (diferente del verde de las citas programadas) (diferente del verde de las citas)
          borderColor: '#7CB342',
          textColor: '#FFFFFF',
          extendedProps: {
            type: 'availability',
            originalData: availability
          }
        };
      });
      
      // Convertir citas al formato de eventos de FullCalendar (si se proporcionan)
      const appointmentEvents = this.appointments.map(appointment => {
        // Determinar color según el estado de la cita
        let backgroundColor, borderColor;
        
        switch (appointment.status) {
          case 'scheduled':
            backgroundColor = '#4CAF50'; // Verde (más oscuro) - Programada
            borderColor = '#4CAF50';
            break;
          case 'completed':
            backgroundColor = '#2196F3'; // Azul - Completada
            borderColor = '#2196F3';
            break;
          case 'cancelled':
            backgroundColor = '#9E9E9E'; // Gris - Cancelada
            borderColor = '#9E9E9E';
            break;
          case 'no-show':
            backgroundColor = '#F44336'; // Rojo - No asistió
            borderColor = '#F44336';
            break;
          default:
            backgroundColor = '#FBC02D'; // Dorado - Por defecto
            borderColor = '#FBC02D';
        }
        
        return {
          id: `appt_${appointment.id}`,
          title: appointment.patient ? `${appointment.patient.name}` : 'Sin paciente',
          start: appointment.start_time,
          end: appointment.end_time,
          backgroundColor: backgroundColor,
          borderColor: borderColor,
          textColor: '#FFFFFF',
          extendedProps: {
            type: 'appointment',
            originalData: appointment,
            status: appointment.status
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
        slotMinTime: '07:30:00',
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
        eventContent: this.renderEventContent,
        
        // Configuración de mensajes en español
        buttonText: {
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          day: 'Día',
          list: 'Lista'
        },
        
        // Configuración para vista mensual
        dayMaxEvents: 3, // Mostrar +más cuando hay más de 3 eventos
        
        // Asegurar que los colores se muestren correctamente en todas las vistas
        eventDidMount: (info) => {
          // Asegurar que el evento tiene el fondo de color correcto
          if (info.event.backgroundColor) {
            info.el.style.backgroundColor = info.event.backgroundColor;
            info.el.style.borderColor = info.event.borderColor;
          }
          
          // Manejo especial para vista de mes
          if (this.currentView === 'dayGridMonth') {
            // Texto visible y con buen contraste
            const eventEl = info.el.querySelector('.fc-event-title');
            if (eventEl) {
              eventEl.style.color = '#FFFFFF';
              eventEl.style.fontWeight = '600';
              eventEl.style.padding = '2px 4px';
            }
            
            // Si es una disponibilidad, añadir icono
            if (info.event.extendedProps.type === 'availability') {
              const titleEl = info.el.querySelector('.fc-event-title');
              if (titleEl) {
                titleEl.innerHTML = '<span class="inline-flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Disponible</span>';
              }
            }
            
            // Si es una cita, adaptamos según su estado
            if (info.event.extendedProps.type === 'appointment') {
              const status = info.event.extendedProps.status || 'scheduled';
              let icon = '';
              
              // Icono según estado
              if (status === 'completed') {
                icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
              } else if (status === 'cancelled' || status === 'no-show') {
                icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
              } else {
                icon = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
              }
              
              // Recortar el texto si es muy largo para la vista mensual
              let eventTitle = info.event.title;
              if (eventTitle.length > 18) {
                eventTitle = eventTitle.substring(0, 16) + '...';
              }
              
              const titleEl = info.el.querySelector('.fc-event-title');
              if (titleEl) {
                titleEl.innerHTML = `<span class="inline-flex items-center">${icon}${eventTitle}</span>`;
              }
            }
          }
        }
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
        
        // Forzar actualización de estilos para la nueva vista
        setTimeout(() => {
          const events = this.calendar.getEvents();
          events.forEach(event => {
            const eventEl = this.calendar.getEventById(event.id).el;
            if (eventEl) {
              eventEl.style.backgroundColor = event.backgroundColor;
              eventEl.style.borderColor = event.borderColor;
              
              // Asegurar contraste del texto para vista de mes
              if (viewName === 'dayGridMonth') {
                const titleEl = eventEl.querySelector('.fc-event-title');
                if (titleEl) {
                  titleEl.style.color = '#FFFFFF';
                  titleEl.style.padding = '2px 4px';
                }
              }
            }
          });
        }, 100);
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
      } else if (eventType === 'appointment') {
        // Emitir evento para ver detalles de la cita
        this.$emit('appointment-click', eventData);
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
      
      // Personalizar el aspecto de los eventos según su tipo y vista actual
      if (this.currentView === 'dayGridMonth') {
        // Vista mensual compacta similar a AppointmentCalendar
        if (eventType === 'availability') {
          // Formatear hora en formato corto para vista mensual
          const startTime = info.event.start.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
          
          return {
            html: `<div class="fc-daygrid-event-dot" style="border-color: ${info.event.backgroundColor}"></div>
                   <div class="fc-event-time">${startTime}</div>
                   <div class="fc-event-title">Disponible</div>`
          };
        } else if (eventType === 'appointment') {
          // Formatear hora en formato corto para vista mensual
          const startTime = info.event.start.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
          
          // Recortar el nombre del paciente si es largo
          let patientName = info.event.title;
          if (patientName.length > 10) {
            patientName = patientName.substring(0, 9) + '...';
          }
          
          return {
            html: `<div class="fc-daygrid-event-dot" style="border-color: ${info.event.backgroundColor}"></div>
                   <div class="fc-event-time">${startTime}</div>
                   <div class="fc-event-title">${patientName}</div>`
          };
        }
      } else {
        // Vista diaria y semanal con más detalles
        if (eventType === 'availability') {
          return {
            html: `
              <div class="fc-event-main-content">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="font-medium">${this.formatTimeRange(info.event.start, info.event.end)}</span>
                  <span class="ml-1 text-xs opacity-90">Disponible</span>
                </div>
              </div>
            `
          };
        } else if (eventType === 'appointment') {
          // Obtener el estado en español
          let statusText = "Programada";
          const status = info.event.extendedProps.status;
          
          switch (status) {
            case 'scheduled':
              statusText = "Programada";
              break;
            case 'completed':
              statusText = "Completada";
              break;
            case 'cancelled':
              statusText = "Cancelada";
              break;
            case 'no-show':
              statusText = "No asistió";
              break;
          }
          
          // Icono apropiado según el estado
          let icon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
          
          if (status === 'completed') {
            icon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
          } else if (status === 'cancelled' || status === 'no-show') {
            icon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>`;
          }
          
          return {
            html: `
              <div class="fc-event-main-content">
                <div class="flex flex-col">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      ${icon}
                    </svg>
                    <span class="font-medium truncate">${info.event.title}</span>
                  </div>
                  <div class="flex items-center text-xs mt-1">
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-white bg-opacity-25">
                      ${statusText}
                    </span>
                    <span class="ml-2">${this.formatTimeRange(info.event.start, info.event.end)}</span>
                  </div>
                </div>
              </div>
            `
          };
        }
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

/* Mejoras para los eventos del calendario */
:deep(.fc-event) {
  border-radius: 4px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
  overflow: hidden;
  border: none !important;
}

:deep(.fc-event-main-content) {
  padding: 4px;
}

:deep(.fc-event:hover) {
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
  transform: translateY(-1px);
  transition: all 0.2s;
}

:deep(.fc-daygrid-event) {
  margin-top: 3px;
  margin-bottom: 3px;
}

/* Mejor contraste para el texto */
:deep(.fc-event-title) {
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Mejora estilo de cabeceras de días */
:deep(.fc-col-header-cell-cushion) {
  padding: 8px;
  font-weight: 600;
}

:deep(.fc-col-header-cell) {
  background-color: rgba(76, 175, 80, 0.05);
}

/* Celdas de hora */
:deep(.fc-timegrid-slot) {
  height: 40px !important;
}

/* Mejoras para vista mensual */
:deep(.fc-daygrid-event) {
  padding: 3px 6px;
  border-radius: 4px;
  margin-top: 2px;
  margin-bottom: 2px;
  min-height: 20px;
  display: flex;
  align-items: center;
}

:deep(.fc-daygrid-day-events) {
  min-height: 2em;
  padding: 2px;
}

:deep(.fc-daygrid-day-number) {
  font-weight: 500;
}

:deep(.fc-daygrid-event-dot) {
  display: none;
}

:deep(.fc-daygrid-day.fc-day-today) {
  background-color: rgba(76, 175, 80, 0.1);
}

:deep(.fc-daygrid-day) {
  transition: background-color 0.15s;
}

:deep(.fc-daygrid-day:hover) {
  background-color: rgba(0, 0, 0, 0.02);
}

/* Estilos para la vista de tiempo */
:deep(.fc-timegrid-event) {
  min-height: 24px;
  border-radius: 0 4px 4px 0 !important;
  border-left-width: 3px !important;
}

:deep(.fc-timegrid-now-indicator-line) {
  border-color: #FF9800 !important;
  border-width: 2px;
}
</style>