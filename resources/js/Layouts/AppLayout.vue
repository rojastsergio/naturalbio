<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import MobileSidebar from '@/Components/Layout/MobileSidebar.vue';

const props = defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const sidebarCollapsed = ref(false);
const sidebarMobileOpen = ref(false);

// Agregar estructura de datos para MobileSidebar (versión simplificada)
const mobileLinks = computed(() => {
    try {
        return [
            {
                title: 'GENERAL',
                links: [
                    {
                        text: 'Panel de Control',
                        url: route('dashboard'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>',
                        active: route().current('dashboard')
                    }
                ]
            },
            {
                title: 'PACIENTES',
                links: [
                    {
                        text: 'Pacientes',
                        url: route('patients.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" /></svg>',
                        active: route().current('patients.index')
                    },
                    {
                        text: 'Nuevo Paciente',
                        url: route('patients.create'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>',
                        active: route().current('patients.create')
                    }
                ]
            },
            {
                title: 'CITAS',
                links: [
                    {
                        text: 'Citas',
                        url: route('appointments.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>',
                        active: route().current('appointments.index')
                    },
                    {
                        text: 'Nueva Cita',
                        url: route('appointments.create'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>',
                        active: route().current('appointments.create')
                    },
                    {
                        text: 'Tipos de Citas',
                        url: route('appointment-types.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>',
                        active: route().current('appointment-types.index')
                    }
                ]
            },
            {
                title: 'DOCTORES',
                links: [
                    {
                        text: 'Doctores',
                        url: route('doctors.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>',
                        active: route().current('doctors.index')
                    },
                    {
                        text: 'Panel Médico',
                        url: route('doctor.dashboard'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>',
                        active: route().current('doctor.dashboard')
                    },
                    {
                        text: 'Disponibilidad',
                        url: route('doctor.availability'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>',
                        active: route().current('doctor.availability')
                    }
                ]
            },
            {
                title: 'TERAPIAS',
                links: [
                    {
                        text: 'Terapias',
                        url: route('therapies.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>',
                        active: route().current('therapies.index')
                    },
                    {
                        text: 'Nueva Terapia',
                        url: route('therapies.create'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>',
                        active: route().current('therapies.create')
                    },
                    {
                        text: 'Asignaciones',
                        url: route('therapy-assignments.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1.586L7.707 3.293A1 1 0 106.293 4.707L9 7.414V10a1 1 0 001 1h.586l1.293 1.293a1 1 0 101.414-1.414L12 9.586V7a1 1 0 00-1-1h-.586l-1.293-1.293a1 1 0 00-1.414 0L6.414 6H4a1 1 0 00-1 1v.586l-1.293 1.293a1 1 0 001.414 1.414L4.414 9H6a1 1 0 001-1v-.586l1.293 1.293a1 1 0 001.414-1.414L8.414 7H10a1 1 0 001-1V4a1 1 0 00-1-1h-1V2a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>',
                        active: route().current('therapy-assignments.index')
                    }
                ]
            },
            {
                title: 'RECETAS',
                links: [
                    {
                        text: 'Recetas',
                        url: route('prescriptions.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>',
                        active: route().current('prescriptions.index')
                    },
                    {
                        text: 'Nueva Receta',
                        url: route('prescriptions.create'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>',
                        active: route().current('prescriptions.create')
                    }
                ]
            },
            {
                title: 'SUPLEMENTOS',
                links: [
                    {
                        text: 'Suplementos',
                        url: route('supplements.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>',
                        active: route().current('supplements.index')
                    },
                    {
                        text: 'Nuevo Suplemento',
                        url: route('supplements.create'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>',
                        active: route().current('supplements.create')
                    }
                ]
            },
            {
                title: 'RECOMENDACIONES',
                links: [
                    {
                        text: 'Recomendaciones',
                        url: route('recommendations.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>',
                        active: route().current('recommendations.index')
                    },
                    {
                        text: 'Nueva Recomendación',
                        url: route('recommendations.create'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>',
                        active: route().current('recommendations.create')
                    }
                ]
            },
            {
                title: 'ADMINISTRACIÓN',
                links: [
                    {
                        text: 'Configuración',
                        url: route('settings.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>',
                        active: route().current('settings.index')
                    },
                    {
                        text: 'Roles',
                        url: route('settings.roles.index'),
                        icon: '<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" /></svg>',
                        active: route().current('settings.roles.index')
                    }
                ]
            }
        ];
    } catch (e) {
        console.error('Error generando mobileLinks:', e);
        return [];
    }
});

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

const handleSidebarToggle = (collapsed) => {
  sidebarCollapsed.value = collapsed;

  // Actualizar CSS variable para el ancho del sidebar
  if (typeof document !== 'undefined') {
    document.documentElement.style.setProperty(
      '--sidebar-width',
      collapsed ? 'var(--sidebar-width-collapsed)' : 'var(--sidebar-width-expanded)'
    );
  }
};

const toggleMobileSidebar = () => {
  sidebarMobileOpen.value = !sidebarMobileOpen.value;
};

// Cerrar sidebar móvil cuando cambie la ruta
watch(() => route().current(), () => {
  // Cierre después de la navegación
  sidebarMobileOpen.value = false;
});

// Configuración inicial y responsividad
onMounted(() => {
  // Verificar si es dispositivo móvil
  const isMobile = window.innerWidth < 768;

  // Establecer el estado inicial del sidebar
  sidebarCollapsed.value = isMobile;

  // Establecer variable CSS inicial
  document.documentElement.style.setProperty(
    '--sidebar-width',
    isMobile ? 'var(--sidebar-width-collapsed)' : 'var(--sidebar-width-expanded)'
  );
  
  // Agregar detector de cambio de tamaño de ventana
  window.addEventListener('resize', () => {
    const isMobileNow = window.innerWidth < 768;
    if (isMobileNow) {
      if (!sidebarCollapsed.value) {
        sidebarCollapsed.value = true;
      }
    }
  });
  
  document.addEventListener('click', (e) => {
    // Verificar si el clic fue en un enlace del sidebar
    const isLinkClick = e.target.closest('a') && e.target.closest('.sidebar-mobile');

    // Solo cerramos el sidebar si no se hizo clic en un enlace
    if (sidebarMobileOpen.value && !e.target.closest('.sidebar') && !e.target.closest('.mobile-menu-button') && !isLinkClick) {
      sidebarMobileOpen.value = false;
    }
  });
});

// Función para cerrar el overlay con un pequeño retraso para permitir navegación
const closeMobileSidebar = () => {
  // Retraso mínimo para permitir que eventos de navegación ocurran primero
  setTimeout(() => {
    sidebarMobileOpen.value = false;
  }, 50);
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 overflow-y-auto relative">
        <Head :title="title" />

        <Banner />

        <!-- Overlay cuando el sidebar móvil está abierto - con transición mejorada -->
        <transition
            enter-active-class="transition-opacity duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarMobileOpen"
                class="fixed inset-0 bg-gray-900 bg-opacity-70 z-40 md:hidden backdrop-blur-sm"
                @click="closeMobileSidebar"
                aria-hidden="true"
            ></div>
        </transition>

        <!-- Sidebar Desktop - Implementación mejorada con posición basada en grid -->
        <div
            :class="[
                'sidebar transition-all duration-300 z-50',
                'hidden md:block', // Siempre visible en desktop
            ]"
        >
            <Sidebar
                :collapsed="sidebarCollapsed"
                @toggle="handleSidebarToggle"
            />
        </div>

        <!-- Sidebar Mobile - Con transición mejorada y mejor accesibilidad -->
        <transition
            enter-active-class="transform transition ease-out duration-300"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transform transition ease-in duration-300"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <div
                v-if="sidebarMobileOpen"
                class="fixed inset-y-0 left-0 z-50 md:hidden"
                role="dialog"
                aria-modal="true"
                aria-labelledby="mobile-sidebar-title"
            >
                <MobileSidebar
                    :links="mobileLinks"
                    @close="closeMobileSidebar"
                />
            </div>
        </transition>

        <!-- Contenido principal optimizado para todos los tamaños de pantalla -->
        <div
            :class="[
                'transition-all duration-300 min-h-screen overflow-y-auto overflow-x-hidden main-content relative z-30',
                sidebarCollapsed ? 'md:ml-16 lg:ml-16 xl:ml-16 2xl:ml-16' : 'md:ml-64 lg:ml-64 xl:ml-64 2xl:ml-64',
                'ml-0 pl-0', // Sin márgenes ni padding en móviles
            ]"
        >
            <!-- Navbar mejorado con mejor soporte para móviles -->
            <nav class="bg-naturalbio-verde border-b border-naturalbio-verde-600 shadow-md sticky top-0 z-40">
                <div class="container-app">
                    <div class="flex justify-between items-center h-16">
                        <!-- Lado izquierdo: Hamburger para Sidebar Móvil y Título -->
                        <div class="flex items-center">
                            <button
                                class="mobile-menu-button inline-flex items-center justify-center p-3 h-12 w-12 rounded-md text-white hover:bg-naturalbio-verde-600 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 transition-colors md:hidden"
                                @click="toggleMobileSidebar"
                                aria-label="Menú"
                                :aria-expanded="sidebarMobileOpen"
                                aria-controls="mobile-sidebar"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': sidebarMobileOpen, 'inline-flex': !sidebarMobileOpen }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{ 'hidden': !sidebarMobileOpen, 'inline-flex': sidebarMobileOpen }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>

                            <!-- Título de la página con mejor truncado y legibilidad -->
                            <h1 class="ml-2 text-white font-title text-lg md:text-xl truncate mobile-page-title">
                                {{ title }}
                            </h1>
                        </div>

                        <!-- Elementos de navegación derecha - Mejora de visibilidad en SM -->
                        <div class="hidden sm:flex sm:items-center">
                            <!-- Teams Dropdown -->
                            <div class="ml-3 relative" v-if="$page.props.jetstream.hasTeamFeatures">
                                <Dropdown align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:bg-naturalbio-verde-700 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 transition-all">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Administrar Equipo
                                            </div>

                                            <!-- Team Settings -->
                                            <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                                Configuración del Equipo
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                Crear Nuevo Equipo
                                            </DropdownLink>

                                            <!-- Team Switcher -->
                                            <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                <div class="border-t border-gray-200" />

                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Cambiar de Equipo
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="mr-2 h-5 w-5 text-naturalbio-verde" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:bg-naturalbio-verde-700 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 transition-all">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Administrar Cuenta
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Perfil
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Cerrar Sesión
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Botón de perfil/usuario en móvil -->
                        <div class="flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-naturalbio-verde-700 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 transition-all"
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                aria-label="Menú de usuario"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menú de navegación responsivo - Mejora con animación y estilos consistentes -->
                <div
                    :class="[
                        {'max-h-80 opacity-100 py-3': showingNavigationDropdown, 'max-h-0 opacity-0 py-0': !showingNavigationDropdown},
                        'sm:hidden overflow-hidden transition-all duration-300 ease-in-out'
                    ]"
                >
                    <!-- Opciones de configuración responsivas -->
                    <div class="border-t border-naturalbio-verde-700">
                        <div class="flex items-center px-4 py-3">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
                            </div>

                            <div>
                                <div class="font-medium text-base text-white">{{ $page.props.auth.user.name }}</div>
                                <div class="font-medium text-sm text-white/80">{{ $page.props.auth.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1 px-2">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Perfil
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Cerrar Sesión
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-naturalbio-verde-700 my-2" />

                                <div class="block px-4 py-2 text-xs text-white/70">
                                    Administrar Equipo
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">
                                    Configuración del Equipo
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                                    Crear Nuevo Equipo
                                </ResponsiveNavLink>

                                <!-- Team Switcher -->
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-naturalbio-verde-700 my-2" />

                                    <div class="block px-4 py-2 text-xs text-white/70">
                                        Cambiar de Equipo
                                    </div>

                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <ResponsiveNavLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading - Mejora de espaciado y sombra -->
            <header v-if="$slots.header" class="bg-white shadow-sm">
                <div class="container-app py-3">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content - Restaurando al diseño original -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="py-3 sm:py-4 md:py-5 lg:py-6">
                    <div class="container-app mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Contenido principal -->
                        <slot />
                    </div>
                </div>
            </main>

            <!-- Footer Mejorado -->
            <footer class="bg-white py-3 sm:py-4 px-4 border-t border-gray-200 shadow-inner">
                <div class="w-full text-center text-gray-600 text-sm">
                    <p>&copy; {{ new Date().getFullYear() }} NaturalBIO Solutions. Todos los derechos reservados.</p>
                </div>
            </footer>
        </div>
    </div>
</template>

<style>
/* Base styling */
:root {
    --sidebar-width-expanded: 16rem;
    --sidebar-width-collapsed: 4rem;
}

html, body {
    height: 100%;
    font-family: 'Poppins', sans-serif;
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
}

/* Tipografía */
.font-title {
    font-family: 'Montserrat', 'Poppins', sans-serif;
}

.font-text {
    font-family: 'Poppins', sans-serif;
}

.font-accent {
    font-family: 'Pacifico', cursive;
}

/* Sidebar styles */
.sidebar {
    position: fixed;
    height: 100%;
    background-color: white;
    border-right: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
    overflow-y: auto;
    z-index: 50;
    -webkit-overflow-scrolling: touch;
}

/* Contenido principal */
.main-content {
    position: relative;
    z-index: 1;
    background-color: #f3f4f6;
    transition: padding-left 0.3s ease-in-out, margin-left 0.3s ease-in-out;
    will-change: padding-left, margin-left;
}

/* Asegurar que no hay elementos que bloquean la interacción */
.main-content * {
    pointer-events: auto;
}

/* Estilos de contenedor optimizados para todos los tamaños */
.container-app {
    width: 100%;
    max-width: 1680px;
    margin-left: auto;
    margin-right: auto;
    box-sizing: border-box;
    padding: 0 1rem;
}

@media (max-width: 640px) {
    .container-app {
        padding: 0 0.5rem;
    }
}

@media (min-width: 1280px) {
    .container-app {
        max-width: 90%;
        padding: 0 1.5rem;
    }
}

@media (min-width: 1536px) {
    .container-app {
        max-width: 95%;
        padding: 0 2rem;
    }
}

/* Responsive margin adjustments - eliminados los padding */
@media (min-width: 768px) {
    .md\:pl-64 {
        padding-left: 0 !important;
    }

    .md\:pl-16 {
        padding-left: 0 !important;
    }

    .md\:ml-64 {
        margin-left: var(--sidebar-width-expanded);
    }

    .md\:ml-16 {
        margin-left: var(--sidebar-width-collapsed);
    }
}

/* Ajustes para contenedores */
.container-xl {
    max-width: 1680px;
    margin-left: auto;
    margin-right: auto;
}

/* Correcciones específicas para scroll */
.overflow-hidden {
    overflow-x: hidden;
    overflow-y: auto !important;
}

.min-h-screen {
    min-height: 100vh;
    height: auto;
    overflow-y: auto;
}

main {
    overflow-y: auto !important;
    min-height: calc(100vh - 4rem - 3rem); /* altura del viewport - navbar - footer */
}

/* Mejoras para dispositivos móviles */
.mobile-page-title {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: max-width 0.3s ease;
}

@media (max-width: 480px) {
    .mobile-page-title {
        max-width: 150px;
    }
}

/* Mejoras para accesibilidad */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Transición del overlay */
.overlay-enter-active,
.overlay-leave-active {
    transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
    opacity: 0;
}

/* Mejoras de animación para el sidebar mobile */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease-in-out;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(-100%);
}

/* Scrollbar personalizado */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background-color: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 9999px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.7);
}

/* Correcciones para z-index y overlay */
.relative { position: relative; }
.z-10 { z-index: 10; }
.z-20 { z-index: 20; }
.z-30 { z-index: 30; }
.z-40 { z-index: 40; }
.z-50 { z-index: 50; }

/* Optimizaciones de rendimiento */
.will-change {
    will-change: transform, opacity;
}

/* Navbar sticky con sombra suave en scroll */
.navbar-sticky {
    position: sticky;
    top: 0;
    backdrop-filter: blur(5px);
    transition: box-shadow 0.3s ease;
}

.navbar-shadow {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Footer con contenido centrado */
footer {
    transition: padding 0.3s ease;
}

@media print {
    .no-print {
        display: none !important;
    }

    .sidebar, nav {
        display: none !important;
    }

    .main-content {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
}
</style>