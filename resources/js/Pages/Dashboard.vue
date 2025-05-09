<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import NaturalBioButton from '@/Components/NaturalBioButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Panel de Control" />

    <AppLayout>
        <template #header>
            <h2 class="font-title font-semibold text-xl text-gray-800 leading-tight">
                Panel de Control
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white">
                        <h1 class="font-title text-2xl font-medium text-naturalbio-verde">
                            ¡Bienvenido a NaturalBIO Solutions!
                        </h1>
                        <p class="mt-6 text-gray-600 leading-relaxed">
                            El sistema integral para la gestión de clínicas naturistas. Desde aquí puedes acceder a todas las funcionalidades según tu rol en el sistema.
                        </p>
                    </div>

                    <div class="bg-gray-50 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
                        <!-- Cards for different roles -->
                        <div v-if="$page.props.auth.user.permissions.includes('view patients')" class="p-6 border rounded-lg bg-white shadow-sm hover:shadow-md transition">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-naturalbio-verde">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <h2 class="ml-3 text-xl font-title font-semibold text-gray-800">
                                    Pacientes
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-600 text-sm leading-relaxed">
                                Gestiona registros de pacientes, visualiza su historial clínico y registra signos vitales.
                            </p>

                            <p class="mt-4 text-sm">
                                <NaturalBioButton variant="primary" as="a" href="/patients">
                                    Ver Pacientes
                                </NaturalBioButton>
                            </p>
                        </div>

                        <div v-if="$page.props.auth.user.permissions.includes('view appointments')" class="p-6 border rounded-lg bg-white shadow-sm hover:shadow-md transition">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-naturalbio-azul">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <h2 class="ml-3 text-xl font-title font-semibold text-gray-800">
                                    Citas
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-600 text-sm leading-relaxed">
                                Programa, consulta y gestiona citas para pacientes con doctores y terapistas.
                            </p>

                            <p class="mt-4 text-sm">
                                <NaturalBioButton variant="secondary" as="a" href="/appointments">
                                    Ver Citas
                                </NaturalBioButton>
                            </p>
                        </div>

                        <div v-if="$page.props.auth.user.roles.includes('doctor')" class="p-6 border rounded-lg bg-white shadow-sm hover:shadow-md transition">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-naturalbio-dorado">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                                </svg>
                                <h2 class="ml-3 text-xl font-title font-semibold text-gray-800">
                                    Recetas
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-600 text-sm leading-relaxed">
                                Crea y administra recetas para tus pacientes con suplementos y recomendaciones.
                            </p>

                            <p class="mt-4 text-sm">
                                <NaturalBioButton variant="premium" as="a" href="/prescriptions">
                                    Ver Recetas
                                </NaturalBioButton>
                            </p>
                        </div>

                        <!-- Tarjeta de Configuración General -->
                        <div v-if="$page.props.auth.user.roles.includes('owner')" class="p-6 border rounded-lg bg-white shadow-sm hover:shadow-md transition">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-naturalbio-gris">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <h2 class="ml-3 text-xl font-title font-semibold text-gray-800">
                                    Configuración
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-600 text-sm leading-relaxed">
                                Administra módulos y la configuración general del sistema.
                            </p>

                            <p class="mt-4 text-sm">
                                <NaturalBioButton variant="outline" as="a" href="/settings">
                                    Ir a Configuración
                                </NaturalBioButton>
                            </p>
                        </div>
                        
                        <!-- Nueva tarjeta para Roles y Permisos -->
                        <div v-if="$page.props.auth.user.roles.includes('owner')" class="p-6 border rounded-lg bg-white shadow-sm hover:shadow-md transition">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-purple-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                </svg>
                                <h2 class="ml-3 text-xl font-title font-semibold text-gray-800">
                                    Roles y Permisos
                                </h2>
                            </div>

                            <p class="mt-4 text-gray-600 text-sm leading-relaxed">
                                Gestiona los roles del sistema y asigna permisos específicos para controlar el acceso de usuarios.
                            </p>

                            <p class="mt-4 text-sm">
                                <NaturalBioButton variant="primary" as="a" href="/settings/roles">
                                    Gestionar Roles
                                </NaturalBioButton>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>