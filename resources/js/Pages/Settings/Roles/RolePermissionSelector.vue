<template>
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">
        Roles
      </label>
      <div class="mt-1 flex flex-wrap gap-2">
        <div 
          v-for="role in roles" 
          :key="role.id" 
          class="inline-flex items-center"
        >
          <input
            :id="`role-${role.id}`"
            type="checkbox"
            :value="role.id"
            v-model="selectedRoleIds"
            class="focus:ring-naturalbio-verde h-4 w-4 text-naturalbio-verde border-gray-300 rounded"
            @change="updateSelection"
          />
          <label :for="`role-${role.id}`" class="ml-2 text-sm text-gray-700">
            {{ role.name }}
          </label>
        </div>
      </div>
    </div>
    
    <div v-if="showDirectPermissions">
      <label class="block text-sm font-medium text-gray-700">
        Permisos Adicionales
      </label>
      <p class="text-xs text-gray-500 mb-2">
        Estos permisos se asignarán directamente al usuario, además de los que tiene por sus roles.
      </p>
      
      <div class="mt-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
        <div 
          v-for="permission in directPermissions" 
          :key="permission.id" 
          class="inline-flex items-center"
        >
          <input
            :id="`permission-${permission.id}`"
            type="checkbox"
            :value="permission.id"
            v-model="selectedPermissionIds"
            class="focus:ring-naturalbio-verde h-4 w-4 text-naturalbio-verde border-gray-300 rounded"
            @change="updateSelection"
          />
          <label :for="`permission-${permission.id}`" class="ml-2 text-sm text-gray-700">
            {{ permission.name }}
          </label>
        </div>
      </div>
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
    permissions: {
      type: Array,
      required: true
    },
    modelValue: {
      type: Object,
      default: () => ({ roles: [], permissions: [] })
    },
    showDirectPermissions: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      selectedRoleIds: [],
      selectedPermissionIds: [],
      directPermissions: []
    }
  },
  created() {
    // Inicializar selecciones basadas en el modelValue
    this.selectedRoleIds = this.modelValue.roles?.map(r => r.id) || [];
    this.selectedPermissionIds = this.modelValue.permissions?.map(p => p.id) || [];
    this.updateDirectPermissions();
  },
  watch: {
    modelValue: {
      handler(newVal) {
        this.selectedRoleIds = newVal.roles?.map(r => r.id) || [];
        this.selectedPermissionIds = newVal.permissions?.map(p => p.id) || [];
        this.updateDirectPermissions();
      },
      deep: true
    },
    'roles': {
      handler() {
        this.updateDirectPermissions();
      },
      deep: true
    }
  },
  methods: {
    updateSelection() {
      const selectedRoles = this.roles.filter(role => 
        this.selectedRoleIds.includes(role.id)
      );
      
      const selectedPermissions = this.permissions.filter(permission => 
        this.selectedPermissionIds.includes(permission.id)
      );
      
      this.$emit('update:modelValue', {
        roles: selectedRoles,
        permissions: selectedPermissions
      });
    },
    updateDirectPermissions() {
      // Obtener todos los permisos ya incluidos en los roles seleccionados
      const rolePermissionIds = new Set();
      this.roles
        .filter(role => this.selectedRoleIds.includes(role.id))
        .forEach(role => {
          (role.permissions || []).forEach(permission => {
            rolePermissionIds.add(permission.id);
          });
        });
      
      // Filtrar permisos que no están ya incluidos en los roles
      this.directPermissions = this.permissions.filter(permission => 
        !rolePermissionIds.has(permission.id)
      );
    }
  }
}
</script>