<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    allPermissions: {
        type: Array,
        required: true
    },
    modelValue: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedPermissions = ref(props.modelValue || []);

const groupedPermissions = computed(() => {
    const groups = {};
    
    props.allPermissions.forEach(permission => {
        // Extrae el módulo del nombre del permiso (ej: "create patients" -> "patients")
        const parts = permission.name.split(' ');
        if (parts.length >= 2) {
            const action = parts[0]; // "create", "view", etc.
            const module = parts.slice(1).join(' '); // "patients", "appointments", etc.
            
            if (!groups[module]) {
                groups[module] = [];
            }
            
            groups[module].push(permission);
        } else {
            // Si el formato no coincide, agrúpalo en "otros"
            if (!groups['otros']) {
                groups['otros'] = [];
            }
            groups['otros'].push(permission);
        }
    });
    
    // Ordenar cada grupo por acción (create, view, update, delete)
    Object.keys(groups).forEach(module => {
        groups[module].sort((a, b) => {
            const actionsOrder = { 'create': 1, 'view': 2, 'update': 3, 'delete': 4 };
            const actionA = a.name.split(' ')[0];
            const actionB = b.name.split(' ')[0];
            
            return (actionsOrder[actionA] || 99) - (actionsOrder[actionB] || 99);
        });
    });
    
    return groups;
});

function formatModuleName(name) {
    // Formatea "patients" como "Pacientes", etc.
    const moduleNames = {
        'patients': 'Pacientes',
        'appointments': 'Citas',
        'doctors': 'Doctores',
        'therapies': 'Terapias',
        'prescriptions': 'Recetas',
        'supplements': 'Suplementos',
        'vitals': 'Signos Vitales',
        'otros': 'Otros Permisos'
    };
    
    return moduleNames[name] || capitalizeFirstLetter(name);
}

function formatPermissionName(name) {
    // Formatea "create patients" como "Crear pacientes", etc.
    const actions = {
        'create': 'Crear',
        'view': 'Ver',
        'update': 'Editar',
        'delete': 'Eliminar'
    };
    
    const parts = name.split(' ');
    if (parts.length >= 2) {
        const action = parts[0];
        const module = parts.slice(1).join(' ');
        
        return `${actions[action] || action} ${module}`;
    }
    
    return name;
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

watch(selectedPermissions, (newVal) => {
    emit('update:modelValue', newVal);
});

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        selectedPermissions.value = newVal;
    }
});
</script>

<template>
    <div class="permission-matrix">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Agrupar permisos por módulo -->
            <div 
                v-for="(modulePermissions, module) in groupedPermissions" 
                :key="module" 
                class="bg-gray-50 p-4 rounded-lg"
            >
                <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ formatModuleName(module) }}</h4>
                
                <div class="space-y-2">
                    <div 
                        v-for="permission in modulePermissions" 
                        :key="permission.id" 
                        class="flex items-start"
                    >
                        <div class="flex items-center h-5">
                            <input
                                :id="`permission-${permission.id}`"
                                type="checkbox"
                                :value="permission.id"
                                v-model="selectedPermissions"
                                class="focus:ring-naturalbio-verde h-4 w-4 text-naturalbio-verde border-gray-300 rounded"
                            />
                        </div>
                        <div class="ml-3 text-sm">
                            <label :for="`permission-${permission.id}`" class="font-medium text-gray-700">
                                {{ formatPermissionName(permission.name) }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>