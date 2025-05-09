<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import RoleForm from '@/Components/Roles/RoleForm.vue';

const props = defineProps({
    role: Object,
    permissions: Array,
    errors: Object
});

function updateRole(formData) {
    router.put(route('settings.roles.update', props.role.id), formData);
}
</script>

<template>
    <Head :title="`Editar Rol: ${role.name}`" />

    <AppLayout>
        <template #header>
            <h2 class="font-title font-semibold text-xl text-gray-800 leading-tight">
                Editar Rol: {{ role.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <RoleForm
                        :permissions="permissions"
                        :role-data="role"
                        :errors="errors"
                        submit-text="Actualizar Rol"
                        @submit="updateRole"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>