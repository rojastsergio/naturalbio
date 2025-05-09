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
};

const toggleMobileSidebar = () => {
  sidebarMobileOpen.value = !sidebarMobileOpen.value;
};

// Cerrar sidebar móvil cuando cambie la ruta
watch(() => route().current(), () => {
  sidebarMobileOpen.value = false;
});

// Configuración inicial y responsividad
onMounted(() => {
  // Verificar si es dispositivo móvil
  const isMobile = window.innerWidth < 768;
  
  // Establecer el estado inicial del sidebar
  sidebarCollapsed.value = isMobile;
  
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
    if (sidebarMobileOpen.value && !e.target.closest('.sidebar') && !e.target.closest('.mobile-menu-button')) {
      sidebarMobileOpen.value = false;
    }
  });
});

// Función para cerrar el overlay
const closeMobileSidebar = () => {
  sidebarMobileOpen.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 overflow-y-auto relative">
        <Head :title="title" />

        <Banner />

        <!-- Overlay cuando el sidebar móvil está abierto -->
        <div 
            v-if="sidebarMobileOpen" 
            class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 md:hidden"
            @click="closeMobileSidebar"
        ></div>

        <!-- Sidebar Desktop -->
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

        <!-- Sidebar Mobile -->
        <div 
            v-if="sidebarMobileOpen" 
            class="fixed inset-y-0 left-0 z-50 md:hidden"
        >
            <MobileSidebar 
                :links="mobileLinks" 
                @close="closeMobileSidebar" 
            />
        </div>
        
        <!-- Contenido principal con z-index para estar por encima de cualquier overlay -->
        <div 
            :class="[
                'transition-all duration-300 min-h-screen overflow-y-auto main-content relative z-30',
                sidebarCollapsed ? 'md:pl-16' : 'md:pl-64',
                'pl-0' // Siempre inicia sin padding en móviles
            ]"
        >
            <nav class="bg-naturalbio-verde border-b border-naturalbio-verde-600 shadow z-10">
                <!-- Primary Navigation Menu -->
                <div class="max-w-full mx-auto px-2 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Hamburger para Sidebar Móvil -->
                        <div class="flex items-center">
                            <button 
                                class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-naturalbio-verde-600 focus:outline-none md:hidden"
                                @click="toggleMobileSidebar"
                                aria-label="Menú"
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
                            
                            <!-- Título de la página (visible solo en móvil) -->
                            <h1 class="ml-2 text-white font-title text-lg md:hidden mobile-page-title">
                                {{ title }}
                            </h1>
                        </div>
                        
                        <!-- Elementos de navegación derecha -->
                        <div class="hidden sm:flex sm:items-center">
                            <!-- Teams Dropdown -->
                            <div class="ml-3 relative" v-if="$page.props.jetstream.hasTeamFeatures">
                                <Dropdown align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-naturalbio-verde hover:bg-naturalbio-verde-700 focus:outline-none focus:bg-naturalbio-verde-700 active:bg-naturalbio-verde-700 transition ease-in-out duration-150">
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
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-white/90 focus:outline-none transition ease-in-out duration-150">
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

                        <!-- Hamburger para menú de usuario -->
                        <div class="flex items-center sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-100 hover:bg-naturalbio-verde-700 focus:outline-none focus:bg-naturalbio-verde-700 focus:text-gray-100 transition duration-150 ease-in-out" @click="showingNavigationDropdown = !showingNavigationDropdown">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden">
                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-naturalbio-verde-700">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
                            </div>

                            <div>
                                <div class="font-medium text-base text-white">{{ $page.props.auth.user.name }}</div>
                                <div class="font-medium text-sm text-white/80">{{ $page.props.auth.user.email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
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
                                <div class="border-t border-naturalbio-verde-700" />

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
                                    <div class="border-t border-naturalbio-verde-700" />

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

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-3 px-4 sm:py-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="py-3 sm:py-4 md:py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Contenido principal mejorado -->
                        <slot />
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white py-3 sm:py-4 px-4 border-t border-gray-200">
                <div class="max-w-7xl mx-auto text-center text-gray-600 text-sm">
                    <p>&copy; {{ new Date().getFullYear() }} NaturalBIO Solutions. Todos los derechos reservados.</p>
                </div>
            </footer>
        </div>
    </div>
</template>

<style>
/* Base styling */
html, body {
    height: 100%;
    font-family: 'Poppins', sans-serif;
    overflow-y: auto;
}

/* Sidebar styles */
.sidebar {
    position: fixed;
    height: 100%;
    background-color: white;
    border-right: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: width 0.3s ease-in-out;
    overflow-y: auto;
    z-index: 50;
}

/* Contenido principal */
.main-content {
    position: relative;
    z-index: 1;
    background-color: #f3f4f6;
}

/* Asegurar que no hay elementos que bloquean la interacción */
.main-content * {
    pointer-events: auto;
}

/* Responsive adjustments */
@media (min-width: 768px) {
    .md\:pl-64 {
        padding-left: 16rem;
    }
    
    .md\:pl-16 {
        padding-left: 4rem;
    }
}

/* Ajustes adicionales para corregir el scroll horizontal */
.max-w-7xl {
    max-width: 100%;
}

@media (min-width: 1280px) {
    .max-w-7xl {
        max-width: 80rem;
    }
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
}

/* Mejoras para dispositivos móviles */
.mobile-page-title {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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

/* Correcciones para z-index y overlay */
.relative {
    position: relative;
}

.z-10 {
    z-index: 10;
}

.z-20 {
    z-index: 20;
}

.z-30 {
    z-index: 30;
}

.z-40 {
    z-index: 40;
}

.z-50 {
    z-index: 50;
}
</style>