<template>
    <div class="availability-calendar">
      <div class="mb-4 flex flex-col sm:flex-row justify-between items-center">
        <div class="flex items-center mb-4 sm:mb-0">
          <button
            class="bg-naturalbio-verde text-white p-2 rounded"
            @click="previousMonth"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
          <span class="mx-4 text-lg font-semibold">{{ currentMonthName }} {{ currentYear }}</span>
          <button
            class="bg-naturalbio-verde text-white p-2 rounded"
            @click="nextMonth"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        </div>
        
        <button
          class="bg-naturalbio-verde text-white px-4 py-2 rounded"
          @click="$emit('new-availability')"
        >
          Nueva Disponibilidad
        </button>
      </div>
      
      <div class="grid grid-cols-7 text-center font-medium bg-gray-100 dark:bg-gray-700 rounded-t-md">
        <div v-for="day in weekDays" :key="day" class="py-2">
          {{ day }}
        </div>
      </div>
      
      <div class="grid grid-cols-7 border border-gray-200 dark:border-gray-600 rounded-b-md">
        <div
          v-for="(day, index) in days"
          :key="index"
          class="border border-gray-200 dark:border-gray-600 min-h-[100px] p-1"
          :class="{
            'bg-gray-100 dark:bg-gray-700': !day.isCurrentMonth,
            'bg-naturalbio-verde bg-opacity-10': day.isToday,
          }"
        >
          <div class="flex justify-between items-center">
            <span
              class="text-sm font-semibold"
              :class="{
                'text-naturalbio-verde': day.isToday,
                'text-gray-400 dark:text-gray-500': !day.isCurrentMonth,
              }"
            >
              {{ day.number }}
            </span>
            <button
              v-if="day.isCurrentMonth"
              class="text-naturalbio-verde hover:text-naturalbio-verde-dark text-xs p-1"
              @click="$emit('add-time-slot', day.date)"
            >
              +
            </button>
          </div>
          
          <div v-if="day.availabilities && day.availabilities.length > 0" class="mt-1">
            <div
              v-for="(availability, i) in day.availabilities"
              :key="i"
              class="bg-naturalbio-verde text-white text-xs p-1 rounded mb-1 truncate"
              @click="$emit('edit-availability', availability)"
            >
              {{ formatTime(availability.start_time) }} - {{ formatTime(availability.end_time) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      availabilities: {
        type: Array,
        required: true
      }
    },
    data() {
      return {
        currentDate: new Date(),
        weekDays: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']
      }
    },
    computed: {
      currentYear() {
        return this.currentDate.getFullYear();
      },
      currentMonth() {
        return this.currentDate.getMonth();
      },
      currentMonthName() {
        return new Date(this.currentYear, this.currentMonth, 1).toLocaleString('es', { month: 'long' });
      },
      days() {
        const days = [];
        
        // Primer día del mes actual
        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        // Último día del mes actual
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        
        // Días del mes anterior para completar la primera semana
        const prevMonthLastDay = new Date(this.currentYear, this.currentMonth, 0).getDate();
        const firstDayOfWeek = firstDay.getDay(); // 0 = Sunday, 1 = Monday, ...
        
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
          const date = new Date(this.currentYear, this.currentMonth - 1, prevMonthLastDay - i);
          days.push({
            number: prevMonthLastDay - i,
            isCurrentMonth: false,
            isToday: this.isToday(date),
            date: this.formatDate(date),
            availabilities: this.getAvailabilitiesForDate(date)
          });
        }
        
        // Días del mes actual
        for (let i = 1; i <= lastDay.getDate(); i++) {
          const date = new Date(this.currentYear, this.currentMonth, i);
          days.push({
            number: i,
            isCurrentMonth: true,
            isToday: this.isToday(date),
            date: this.formatDate(date),
            availabilities: this.getAvailabilitiesForDate(date)
          });
        }
        
        // Días del próximo mes para completar la última semana
        const remainingDays = 7 - (days.length % 7);
        if (remainingDays < 7) {
          for (let i = 1; i <= remainingDays; i++) {
            const date = new Date(this.currentYear, this.currentMonth + 1, i);
            days.push({
              number: i,
              isCurrentMonth: false,
              isToday: this.isToday(date),
              date: this.formatDate(date),
              availabilities: this.getAvailabilitiesForDate(date)
            });
          }
        }
        
        return days;
      }
    },
    methods: {
      previousMonth() {
        this.currentDate = new Date(this.currentYear, this.currentMonth - 1, 1);
        this.$emit('month-changed', this.formatDate(this.currentDate));
      },
      nextMonth() {
        this.currentDate = new Date(this.currentYear, this.currentMonth + 1, 1);
        this.$emit('month-changed', this.formatDate(this.currentDate));
      },
      isToday(date) {
        const today = new Date();
        return date.getDate() === today.getDate() &&
          date.getMonth() === today.getMonth() &&
          date.getFullYear() === today.getFullYear();
      },
      formatDate(date) {
        return date.toISOString().split('T')[0]; // YYYY-MM-DD
      },
      formatTime(time) {
        if (!time) return '';
        
        // Si es un objeto Date o un string ISO
        if (time instanceof Date || (typeof time === 'string' && time.includes('T'))) {
          const date = new Date(time);
          return date.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
        }
        
        // Si es solo un string de hora (HH:MM:SS)
        if (typeof time === 'string' && time.includes(':')) {
          return time.substring(0, 5); // HH:MM
        }
        
        return time;
      },
      getAvailabilitiesForDate(date) {
        const dateStr = this.formatDate(date);
        return this.availabilities.filter(a => {
          const availabilityDate = a.date instanceof Date 
            ? this.formatDate(a.date) 
            : a.date;
          return availabilityDate === dateStr;
        });
      }
    }
  }
  </script>