<script setup>
import { ref, provide, computed, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SidebarSection from '@/Components/Layout/SidebarSection.vue';
import SidebarItem from '@/Components/Layout/SidebarItem.vue';

const props = defineProps({
    collapsed: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['toggle']);

const page = usePage();
const isCollapsed = ref(props.collapsed);
const isMobile = ref(false);
const isHovered = ref(false);

// Determinar la sección activa basada en la URL
const determineActiveSection = () => {
    const url = window.location.pathname;
    
    if (url.includes('/dashboard')) {
        return 'general';
    } else if (url.includes('/patients') || url.includes('/vitals')) {
        return 'patients';
    } else if (url.includes('/appointments') || url.includes('/appointment-types')) {
        return 'appointments';
    } else if (url.includes('/doctors') || url.includes('/doctor')) {
        return 'doctors';
    } else if (url.includes('/therapies') || url.includes('/therapy-')) {
        return 'therapies';
    } else if (url.includes('/prescriptions')) {
        return 'prescriptions';
    } else if (url.includes('/supplements')) {
        return 'supplements';
    } else if (url.includes('/recommendations')) {
        return 'recommendations';
    } else if (url.includes('/settings')) {
        return 'settings';
    } else {
        return 'general';
    }
};

// Estado para la sección activa
const activeSectionKey = ref(determineActiveSection());

// Observar cambios en la URL para actualizar la sección activa
watch(
    () => page.url,
    () => {
        activeSectionKey.value = determineActiveSection();
    }
);

// Proporcionar el estado de la sección activa a los componentes hijos
provide('activeSectionKey', activeSectionKey);

// Detectar cambios en el prop collapsed
watch(() => props.collapsed, (newValue) => {
    isCollapsed.value = newValue;
});

// Comprobar si es un dispositivo móvil
onMounted(() => {
    checkIfMobile();
    window.addEventListener('resize', checkIfMobile);
    
    return () => {
        window.removeEventListener('resize', checkIfMobile);
    };
});

const checkIfMobile = () => {
    isMobile.value = window.innerWidth < 768;
};

// Emitir evento cuando cambia el estado
watch(isCollapsed, (newValue) => {
    emit('toggle', newValue);
});

function toggleSidebar() {
    isCollapsed.value = !isCollapsed.value;
}
</script>

<template>
    <aside
        :class="[
            isCollapsed && !isHovered ? 'w-16' : 'w-64',
            'fixed left-0 top-0 h-full bg-white shadow-sm border-r border-gray-200 transition-all duration-300 ease-in-out z-20 sidebar-wrapper'
        ]"
        style="box-shadow: 0 0 10px rgba(0,0,0,0.07);"
        @mouseenter="isHovered = !isMobile && isCollapsed"
        @mouseleave="isHovered = false"
    >
        <!-- Logo y botón de toggle - Mejora con accesibilidad -->
        <div class="flex items-center justify-between h-16 px-4 bg-naturalbio-verde text-white shadow-md">
            <div class="flex items-center overflow-hidden">
                <img
                    src="/images/logo-icon.svg"
                    alt="NaturalBIO"
                    class="h-8 w-8 flex-shrink-0"
                    onerror="this.src='/favicon.ico'; this.onerror=null;"
                />
                <span
                    v-if="!isCollapsed || isHovered"
                    class="ml-3 text-xl font-title font-semibold truncate transition-opacity duration-300"
                >
                    NaturalBIO
                </span>
            </div>
            <button
                @click="toggleSidebar"
                class="p-2 rounded-md hover:bg-naturalbio-verde-600 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 transition-colors"
                :aria-label="isCollapsed ? 'Expandir menú' : 'Colapsar menú'"
            >
                <svg
                    class="w-5 h-5 transition-transform"
                    :class="{ 'rotate-180': isCollapsed && !isHovered }"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Menú de navegación - Mejora con scrollbar personalizado -->
        <nav class="px-2 py-3 overflow-y-auto h-[calc(100vh-4rem)] custom-scrollbar">
            <!-- GENERAL -->
            <SidebarSection :collapsed="isCollapsed" title="GENERAL" section-key="general">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="dashboard"
                    text="Panel de Control"
                    to="dashboard"
                    section-key="general"
                />
            </SidebarSection>

            <!-- PACIENTES -->
            <SidebarSection :collapsed="isCollapsed" title="PACIENTES" section-key="patients">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="patients"
                    text="Pacientes"
                    to="patients.index"
                    section-key="patients"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="add"
                    text="Nuevo Paciente"
                    to="patients.create"
                    section-key="patients"
                />
            </SidebarSection>

            <!-- CITAS -->
            <SidebarSection :collapsed="isCollapsed" title="CITAS" section-key="appointments">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="calendar"
                    text="Citas"
                    to="appointments.index"
                    section-key="appointments"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="add"
                    text="Nueva Cita"
                    to="appointments.create"
                    section-key="appointments"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="list"
                    text="Tipos de Citas"
                    to="appointment-types.index"
                    section-key="appointments"
                />
            </SidebarSection>

            <!-- DOCTORES -->
            <SidebarSection :collapsed="isCollapsed" title="DOCTORES" section-key="doctors">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="doctors"
                    text="Doctores"
                    to="doctors.index"
                    section-key="doctors"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="dashboard"
                    text="Panel Médico"
                    to="doctor.dashboard"
                    section-key="doctors"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="availability"
                    text="Disponibilidad"
                    to="doctor.availability"
                    section-key="doctors"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="instructions"
                    text="Instrucciones Terapéuticas"
                    to="doctor.therapy-instructions"
                    section-key="doctors"
                />
            </SidebarSection>

            <!-- TERAPIAS -->
            <SidebarSection :collapsed="isCollapsed" title="TERAPIAS" section-key="therapies">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="therapy"
                    text="Terapias"
                    to="therapies.index"
                    section-key="therapies"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="add"
                    text="Nueva Terapia"
                    to="therapies.create"
                    section-key="therapies"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="sessions"
                    text="Asignaciones"
                    to="therapy-assignments.index"
                    section-key="therapies"
                />
            </SidebarSection>

            <!-- RECETAS -->
            <SidebarSection :collapsed="isCollapsed" title="RECETAS" section-key="prescriptions">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="prescription"
                    text="Recetas"
                    to="prescriptions.index"
                    section-key="prescriptions"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="add"
                    text="Nueva Receta"
                    to="prescriptions.create"
                    section-key="prescriptions"
                />
            </SidebarSection>

            <!-- SUPLEMENTOS -->
            <SidebarSection :collapsed="isCollapsed" title="SUPLEMENTOS" section-key="supplements">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="supplements"
                    text="Suplementos"
                    to="supplements.index"
                    section-key="supplements"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="add"
                    text="Nuevo Suplemento"
                    to="supplements.create"
                    section-key="supplements"
                />
            </SidebarSection>

            <!-- RECOMENDACIONES -->
            <SidebarSection :collapsed="isCollapsed" title="RECOMENDACIONES" section-key="recommendations">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="recommendations"
                    text="Recomendaciones"
                    to="recommendations.index"
                    section-key="recommendations"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="add"
                    text="Nueva Recomendación"
                    to="recommendations.create"
                    section-key="recommendations"
                />
            </SidebarSection>

            <!-- ADMINISTRACIÓN -->
            <SidebarSection :collapsed="isCollapsed" title="ADMINISTRACIÓN" section-key="settings">
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="config"
                    text="Configuración"
                    to="settings.index"
                    section-key="settings"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="modules"
                    text="Módulos"
                    to="settings.modules"
                    section-key="settings"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="roles"
                    text="Roles"
                    to="settings.roles.index"
                    section-key="settings"
                />
                <SidebarItem
                    :collapsed="isCollapsed"
                    icon="forms"
                    text="Formularios"
                    to="settings.forms"
                    section-key="settings"
                />
            </SidebarSection>

            <!-- Footer con acciones rápidas -->
            <div
                :class="[
                    'mt-auto pt-4 border-t border-gray-200 transition-opacity',
                    isCollapsed && !isHovered ? 'opacity-0' : 'opacity-100'
                ]"
                v-if="!isCollapsed || isHovered"
            >
                <div class="px-3 pb-2 text-xs text-gray-500">
                    <div class="flex justify-between items-center">
                        <span>v1.0.0</span>
                        <div class="flex space-x-2">
                            <a href="#" class="text-gray-400 hover:text-naturalbio-verde" title="Ayuda">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-naturalbio-verde" title="Configuración">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </aside>
</template>

<style scoped>
/* Mejoras para el scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background-color: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 9999px;
}

/* Transición suave del texto al expandir/colapsar */
.truncate {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Asegurar que los iconos se centren cuando está colapsado */
.w-16 .mr-0 {
    margin-left: auto;
    margin-right: auto;
}

/* Transición para la rotación del botón toggle */
.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Estilo para el cursor en las secciones desplegables */
.cursor-pointer:hover {
    background-color: rgba(76, 175, 80, 0.05);
    border-radius: 0.375rem;
}
</style>