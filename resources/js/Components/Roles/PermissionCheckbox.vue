<template>
    <div class="flex items-center">
      <input
        :id="`permission-${permission.id}`"
        type="checkbox"
        :value="permission.id"
        v-model="isChecked"
        class="focus:ring-naturalbio-verde h-4 w-4 text-naturalbio-verde border-gray-300 rounded"
      />
      <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-900">
        {{ permission.name }}
      </label>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      permission: {
        type: Object,
        required: true
      },
      modelValue: {
        type: Array,
        default: () => []
      }
    },
    emits: ['update:modelValue'],
    computed: {
      isChecked: {
        get() {
          return this.modelValue.includes(this.permission.id);
        },
        set(checked) {
          let newValue = [...this.modelValue];
          if (checked) {
            if (!newValue.includes(this.permission.id)) {
              newValue.push(this.permission.id);
            }
          } else {
            newValue = newValue.filter(id => id !== this.permission.id);
          }
          this.$emit('update:modelValue', newValue);
        }
      }
    }
  }
  </script>