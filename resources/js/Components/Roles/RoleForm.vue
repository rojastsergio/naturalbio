<script setup>
import { ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import RolePermissionMatrix from './RolePermissionMatrix.vue';

const props = defineProps({
    permissions: Array,
    roleData: Object,
    errors: Object,
    submitText: {
        type: String,
        default: 'Guardar'
    }
});

const emit = defineEmits(['submit']);

const form = ref({
    name: props.roleData?.name || '',
    permissions: props.roleData?.permissions?.map(p => p.id) || []
});

function submit() {
    emit('submit', form.value);
}
</script>

<template>
    <div>
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Rol</label>
            <input 
                type="text" 
                id="name" 
                v-model="form.name" 
                class="mt-1 focus:ring-naturalbio-verde focus:border-naturalbio-verde block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            >
            <div v-if="errors && errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</div>
        </div>
        
        <div class="mb-4">
            <h3 class="font-medium text-gray-700 mb-2">Permisos</h3>
            <RolePermissionMatrix 
                :all-permissions="permissions" 
                v-model="form.permissions"
            />
            <div v-if="errors && errors.permissions" class="text-red-500 text-sm mt-1">{{ errors.permissions }}</div>
        </div>
        
        <div class="flex justify-end">
            <Link 
                :href="route('settings.roles.index')" 
                class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 mr-3"
            >
                Cancelar
            </Link>
            <button 
                type="submit" 
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-naturalbio-verde hover:bg-green-700"
                @click="submit"
            >
                {{ submitText }}
            </button>
        </div>
    </div>
</template>