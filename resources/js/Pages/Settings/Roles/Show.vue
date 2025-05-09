<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    role: Object
});

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-GT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}
</script>

<template>
    <Head :title="`Detalles del Rol: ${role.name}`" />

    <AppLayout>
        <template #header>
            <h2 class="font-title font-semibold text-xl text-gray-800 leading-tight">
                Detalles del Rol: {{ role.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Información del Rol</h3>
                        <div class="mt-2 max-w-xl text-sm text-gray-500">
                            <p>Nombre: {{ role.name }}</p>
                            <p>Guard Name: {{ role.guard_name }}</p>
                            <p>Creado: {{ formatDate(role.created_at) }}</p>
                            <p>Actualizado: {{ formatDate(role.updated_at) }}</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900">Permisos Asignados</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span
                                v-for="permission in role.permissions"
                                :key="permission.id"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
                            >
                                {{ permission.name }}
                            </span>
                            <p v-if="role.permissions.length === 0" class="text-sm text-gray-500">
                                Este rol no tiene permisos asignados.
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <Link
                            :href="route('settings.roles.index')"
                            class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 mr-3"
                        >
                            Volver a la Lista
                        </Link>
                        <Link
                            :href="route('settings.roles.edit', role.id)"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-naturalbio-verde hover:bg-green-700"
                        >
                            Editar Rol
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>