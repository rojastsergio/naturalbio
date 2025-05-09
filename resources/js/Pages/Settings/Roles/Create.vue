<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import RoleForm from '@/Components/Roles/RoleForm.vue';

defineProps({
    permissions: Array,
    errors: Object
});

function createRole(formData) {
    router.post(route('settings.roles.store'), formData);
}
</script>

<template>
    <Head title="Crear Rol" />

    <AppLayout>
        <template #header>
            <h2 class="font-title font-semibold text-xl text-gray-800 leading-tight">
                Crear Nuevo Rol
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <RoleForm
                        :permissions="permissions"
                        :errors="errors"
                        submit-text="Crear Rol"
                        @submit="createRole"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>