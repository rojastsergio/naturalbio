<template>
    <div 
      class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4"
      :class="borderColorClass"
    >
      <div class="flex justify-between items-start">
        <div>
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">{{ title }}</h3>
          <div class="mt-2">
            <template v-if="value">
              <div class="text-2xl font-bold" :class="valueColorClass">{{ formattedValue }}</div>
            </template>
            <slot></slot>
          </div>
        </div>
        <div class="text-naturalbio-verde">
          <slot name="icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
              />
            </svg>
          </slot>
        </div>
      </div>
      <div v-if="description" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        {{ description }}
      </div>
      <div v-if="$slots.default" class="mt-3">
        <slot name="default"></slot>
      </div>
      <div v-if="$slots.footer" class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
        <slot name="footer"></slot>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      title: {
        type: String,
        required: true
      },
      value: {
        type: [String, Number],
        default: null
      },
      description: {
        type: String,
        default: null
      },
      type: {
        type: String,
        default: 'default',
        validator: value => ['default', 'success', 'warning', 'danger', 'info'].includes(value)
      },
      prefix: {
        type: String,
        default: ''
      },
      suffix: {
        type: String,
        default: ''
      },
      isCurrency: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      borderColorClass() {
        const colors = {
          default: 'border-naturalbio-verde',
          success: 'border-green-500',
          warning: 'border-yellow-500',
          danger: 'border-red-500',
          info: 'border-blue-500'
        };
        return colors[this.type] || colors.default;
      },
      valueColorClass() {
        const colors = {
          default: 'text-naturalbio-verde',
          success: 'text-green-500',
          warning: 'text-yellow-500',
          danger: 'text-red-500',
          info: 'text-blue-500'
        };
        return colors[this.type] || colors.default;
      },
      formattedValue() {
        if (this.value === null) return null;
        
        let formatted = this.value;
        
        if (this.isCurrency) {
          formatted = new Intl.NumberFormat('es-GT', {
            style: 'currency',
            currency: 'GTQ',
            minimumFractionDigits: 2
          }).format(this.value);
        } else if (typeof this.value === 'number') {
          formatted = this.value.toLocaleString('es-GT');
        }
        
        return `${this.prefix}${formatted}${this.suffix}`;
      }
    }
  }
  </script>