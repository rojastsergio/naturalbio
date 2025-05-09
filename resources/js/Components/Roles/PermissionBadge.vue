<script setup>
const props = defineProps({
    permission: {
        type: Object,
        required: true
    },
    type: {
        type: String,
        default: 'default'
    }
});

const permissionName = computed(() => {
    // Formatear nombre de permiso para mejor visualización
    const actionMap = {
        'create': 'Crear',
        'view': 'Ver',
        'update': 'Editar',
        'delete': 'Eliminar'
    };
    
    const parts = props.permission.name.split(' ');
    if (parts.length >= 2) {
        const action = parts[0];
        const resource = parts.slice(1).join(' ');
        return `${actionMap[action] || action} ${resource}`;
    }
    
    return props.permission.name;
});

const badgeClass = computed(() => {
    // Asignar colores según el tipo de acción
    if (props.permission.name.startsWith('create')) {
        return 'bg-green-100 text-green-800';
    } else if (props.permission.name.startsWith('view')) {
        return 'bg-blue-100 text-blue-800';
    } else if (props.permission.name.startsWith('update')) {
        return 'bg-yellow-100 text-yellow-800';
    } else if (props.permission.name.startsWith('delete')) {
        return 'bg-red-100 text-red-800';
    } else {
        return 'bg-gray-100 text-gray-800';
    }
});
</script>

<template>
    <span 
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
        :class="badgeClass"
    >
        {{ permissionName }}
    </span>
</template>