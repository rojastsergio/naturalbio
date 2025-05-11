<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
  links: {
    type: Array,
    required: true
  }
});

const page = usePage();
const emit = defineEmits(['close']);

// Estado para secciones expandidas en acordeón
const expandedSections = ref({});

// Alternar sección de acordeón (modo único: solo una sección abierta a la vez)
const toggleSection = (event, sectionTitle) => {
  // Evitar que el evento se propague y cause cierres no deseados
  event.preventDefault();
  event.stopPropagation();

  // Si la sección ya está expandida, la cerramos
  if (expandedSections.value[sectionTitle]) {
    expandedSections.value = {
      ...expandedSections.value,
      [sectionTitle]: false
    };
  } else {
    // Cerrar todas las secciones y expandir solo la seleccionada
    const resetSections = {};
    props.links.forEach(section => {
      resetSections[section.title] = false;
    });

    expandedSections.value = {
      ...resetSections,
      [sectionTitle]: true
    };
  }
};

// Verificar si una sección está expandida
const isSectionExpanded = (sectionTitle) => {
  // Si la sección tiene enlaces activos, expandirla automáticamente la primera vez
  if (expandedSections.value[sectionTitle] === undefined) {
    const hasActiveLink = props.links.find(section =>
      section.title === sectionTitle &&
      section.links.some(link => link.active)
    );

    if (hasActiveLink) {
      expandedSections.value[sectionTitle] = true;
      return true;
    }
  }

  return !!expandedSections.value[sectionTitle];
};

// Verificar si una sección contiene enlaces activos (para mostrar indicador visual)
const sectionHasActiveLink = (sectionTitle) => {
  return props.links.find(section =>
    section.title === sectionTitle &&
    section.links.some(link => link.active)
  ) !== undefined;
};

// Función para navegar a una URL y cerrar el sidebar
const navigateAndClose = (event, url) => {
  // Evitar que el evento se propague
  event.stopPropagation();
  // Primero cerramos el sidebar
  emit('close');
  // Luego navegamos después de un breve retraso
  setTimeout(() => {
    window.location.href = url;
  }, 10);
  return false;
};

// Manejador para cerrar el sidebar
const closeSidebar = () => {
  emit('close');
};

// Manejador para cerrar sesión
const logout = () => {
  if (route().has('logout')) {
    router.post(route('logout'));
    closeSidebar();
  }
};
</script>

<template>
  <div id="mobile-sidebar" class="sidebar-mobile w-full max-w-xs h-full flex flex-col bg-white border-r border-gray-200 shadow-lg" style="max-width: 280px; width: 85%;">
    <!-- Header del sidebar móvil - Mejora con accesibilidad y consistencia -->
    <div class="bg-naturalbio-verde text-white h-16 flex items-center justify-between px-4">
      <div class="flex items-center overflow-hidden">
        <img
          src="/img/logo-small.png"
          alt="Logo NaturalBIO"
          class="h-8 w-8 flex-shrink-0"
          onerror="this.src='/favicon.ico'; this.onerror=null;"
        />
        <span id="mobile-sidebar-title" class="ml-2 text-xl font-title font-semibold truncate">NaturalBIO</span>
      </div>
      <button
        @click="closeSidebar"
        class="p-2 rounded-md hover:bg-naturalbio-verde-600 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-30 transition-colors"
        aria-label="Cerrar menú"
      >
        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Lista de enlaces móviles con funcionalidad de acordeón -->
    <div class="flex-1 overflow-y-auto py-2 px-2 custom-scrollbar">
      <div v-for="(section, index) in links" :key="index" class="mb-2 border-b border-gray-100 last:border-0 pb-2">
        <!-- Cabecera de sección expandible -->
        <button
          :class="[
            'section-header w-full flex items-center justify-between px-3 py-3 text-left text-xs font-bold hover:bg-gray-50 rounded-md transition-colors uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-naturalbio-verde focus:ring-opacity-50',
            sectionHasActiveLink(section.title) ? 'text-naturalbio-verde' : 'text-gray-600',
            isSectionExpanded(section.title) ? 'bg-naturalbio-verde-50' : ''
          ]"
          @click="(event) => toggleSection(event, section.title)"
          :aria-expanded="isSectionExpanded(section.title)"
          :aria-controls="`section-${index}`"
        >
          <div class="flex items-center">
            <span class="relative">
              {{ section.title }}
              <!-- Indicador de sección activa -->
              <span v-if="sectionHasActiveLink(section.title)" class="absolute -right-3 top-0 w-2 h-2 bg-naturalbio-verde rounded-full"></span>
            </span>
          </div>
          <svg
            class="h-4 w-4 text-gray-500 transition-transform duration-200"
            :class="{ 'transform rotate-180': isSectionExpanded(section.title) }"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Contenido de sección (enlaces) con transición animada -->
        <transition name="section-content">
          <div
            v-show="isSectionExpanded(section.title)"
            :id="`section-${index}`"
            class="mt-1 space-y-1 pl-2 overflow-hidden"
          >
          <div
            v-for="(link, i) in section.links"
            :key="i"
            class="mb-1"
          >
            <a
              href="javascript:void(0);"
              @click="(event) => navigateAndClose(event, link.url)"
              :class="[
                'flex items-center px-3 py-3 text-sm font-medium rounded-md transition-colors',
                link.active
                  ? 'bg-naturalbio-verde-50 text-naturalbio-verde border-r-4 border-naturalbio-verde'
                  : 'text-gray-700 hover:bg-naturalbio-verde-50 hover:text-naturalbio-verde'
              ]"
            >
              <span class="mr-3 flex-shrink-0" v-html="link.icon"></span>
              <span class="truncate">{{ link.text }}</span>
            </a>
          </div>
          </div>
        </transition>
      </div>
    </div>

    <!-- Área de perfil de usuario - Componente compartido para mejor consistencia -->
    <div class="border-t border-gray-200 px-4 py-4">
      <div class="flex items-center">
        <div v-if="$page && $page.props && $page.props.jetstream && $page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
          <img
            class="h-10 w-10 rounded-full object-cover border border-gray-200"
            :src="$page.props.auth.user.profile_photo_url"
            :alt="$page.props.auth.user.name"
          />
        </div>
        <div v-else class="w-10 h-10 rounded-full bg-naturalbio-verde-50 text-naturalbio-verde flex items-center justify-center mr-3">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-medium text-gray-900 truncate" v-if="$page && $page.props && $page.props.auth">
            {{ $page.props.auth.user.name }}
          </div>
          <div class="text-xs text-gray-500 truncate" v-if="$page && $page.props && $page.props.auth">
            {{ $page.props.auth.user.email }}
          </div>
        </div>
      </div>

      <!-- Enlace rápido al perfil y cerrar sesión -->
      <div class="mt-3 flex space-x-2">
        <a
          href="javascript:void(0);"
          @click="(event) => navigateAndClose(event, route('profile.show'))"
          class="flex-1 py-2 px-3 text-xs font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-center"
        >
          Perfil
        </a>
        <button
          @click="logout"
          class="flex-1 py-2 px-3 text-xs font-medium rounded bg-red-100 text-red-700 hover:bg-red-200 text-center"
        >
          Cerrar Sesión
        </button>
      </div>
    </div>

    <!-- Footer con versión -->
    <div class="px-4 py-2 text-xs text-gray-500 border-t border-gray-200">
      v1.0.0 • NaturalBIO Solutions
    </div>
  </div>
</template>

<style scoped>
/* Mejoras para scrollbar personalizado */
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

/* Transición suave para elementos internos */
.sidebar-mobile {
  width: 85%;
  max-width: 320px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateX(-100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Mejora estados hover */
.sidebar-mobile a:hover,
.sidebar-mobile button:hover {
  transition: all 0.2s ease;
}

/* Estilos para acordeón */
.section-content-enter-active,
.section-content-leave-active {
  transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
  max-height: 1000px;
  overflow: hidden;
}

.section-content-enter-from,
.section-content-leave-to {
  max-height: 0;
  opacity: 0;
}

/* Indicador de sección activa */
button[aria-expanded="true"] {
  background-color: rgba(76, 175, 80, 0.05);
  font-weight: 700;
}

/* Mejora visual para secciones */
.section-header {
  border-left: 3px solid transparent;
  /* Mejoras para dispositivos táctiles */
  -webkit-tap-highlight-color: transparent;
  user-select: none;
  touch-action: manipulation;
}

.section-header[aria-expanded="true"] {
  border-left-color: #4CAF50;
  background-color: rgba(76, 175, 80, 0.05);
}

/* Indicador de sección con enlaces activos */
.section-active {
  color: #4CAF50 !important;
}

/* Animación para expansión/colapso suave del acordeón */
@keyframes fadeDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.section-content-enter-active > * {
  animation: fadeDown 0.3s ease-out forwards;
}

/* Mejorar comportamiento táctil y transiciones */
.section-content-enter-active,
.section-content-leave-active {
  overflow: hidden;
  transition: max-height 0.5s cubic-bezier(0.23, 1, 0.32, 1), opacity 0.3s ease;
}

.section-content-enter-from,
.section-content-leave-to {
  max-height: 0;
  opacity: 0;
}
</style>