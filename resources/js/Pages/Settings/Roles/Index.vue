<template>
    <div>
        <h1 class="text-2xl font-semibold mb-6">Gestión de Roles</h1>
        
        <div class="mb-4">
            <!-- Cambiar inertia-link por Link -->
            <Link :href="route('settings.roles.index')" class="bg-naturalbio-verde hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                Crear Nuevo Rol
            </Link>
        </div>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permisos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="role in roles" :key="role.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ role.name }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <span 
                                    v-for="permission in role.permissions" 
                                    :key="permission.id"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                >
                                    {{ permission.name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <!-- Cambiar inertia-link por Link -->
                            <Link 
                                :href="route('settings.roles.edit', role.id)" 
                                class="text-naturalbio-verde hover:text-green-700 mr-3"
                            >
                                Editar
                            </Link>
                            <button 
                                v-if="role.name !== 'owner'" 
                                @click="deleteRole(role)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'; // Importar Link

export default {
    components: {
        Link // Registrar el componente
    },
    props: {
        roles: Array
    },
    methods: {
        deleteRole(role) {
            if (confirm(`¿Estás seguro de eliminar el rol ${role.name}?`)) {
                this.$inertia.delete(route('settings.roles.destroy', role.id));
            }
        }
    }
}
</script>