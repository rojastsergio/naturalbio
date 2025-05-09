<template>
  <div>
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700">Roles de Usuario</label>
      <p class="text-sm text-gray-500 mb-2">Selecciona los roles para este usuario:</p>
      
      <div class="space-y-2">
        <div 
          v-for="role in roles" 
          :key="role.id" 
          class="flex items-center"
        >
          <input
            :id="`role-${role.id}`"
            type="checkbox"
            :value="role.id"
            v-model="selectedRoles"
            class="focus:ring-naturalbio-verde h-4 w-4 text-naturalbio-verde border-gray-300 rounded"
          />
          <label :for="`role-${role.id}`" class="ml-2 block text-sm text-gray-900">
            {{ role.name }}
            <span v-if="role.name === 'owner'" class="text-xs text-red-500 ml-1">
              (Acceso completo)
            </span>
          </label>
        </div>
      </div>
      <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    roles: {
      type: Array,
      required: true
    },
    userRoles: {
      type: Array,
      default: () => []
    },
    error: String
  },
  emits: ['update:userRoles'],
  data() {
    return {
      selectedRoles: []
    }
  },
  created() {
    // Convertir los roles del usuario a IDs para el v-model
    this.selectedRoles = this.userRoles.map(role => role.id);
  },
  watch: {
    userRoles: {
      handler(newRoles) {
        this.selectedRoles = newRoles.map(role => role.id);
      },
      deep: true
    },
    selectedRoles(newSelectedRoles) {
      // Convertir los IDs seleccionados a objetos de rol completos
      const selectedRoleObjects = this.roles.filter(role => 
        newSelectedRoles.includes(role.id)
      );
      this.$emit('update:userRoles', selectedRoleObjects);
    }
  }
}
</script>