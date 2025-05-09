<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['close']);

const closeSidebar = () => {
  emit('close');
};
</script>

<template>
  <div class="mobile-sidebar h-full flex flex-col bg-white border-r border-gray-200 shadow-lg">
    <!-- Header del sidebar móvil -->
    <div class="bg-naturalbio-verde text-white h-16 flex items-center justify-between px-4">
      <div class="flex items-center">
        <img src="/img/logo-small.png" alt="NaturalBIO" class="h-8 w-8" />
        <span class="ml-2 text-xl font-title font-semibold">NaturalBIO</span>
      </div>
      <button 
        @click="closeSidebar" 
        class="p-1 rounded-md hover:bg-naturalbio-verde-600 focus:outline-none"
      >
        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    
    <!-- Lista de enlaces móviles -->
    <div class="flex-1 overflow-y-auto py-4 px-2">
      <div v-for="(section, index) in links" :key="index" class="mb-4">
        <h3 class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
          {{ section.title }}
        </h3>
        <div class="space-y-1 mt-1">
          <Link
            v-for="(link, i) in section.links"
            :key="i"
            :href="link.url"
            :class="[
              'flex items-center px-3 py-3 text-sm font-medium rounded-md transition-colors',
              link.active 
                ? 'bg-naturalbio-verde-50 text-naturalbio-verde border-r-4 border-naturalbio-verde' 
                : 'text-gray-700 hover:bg-naturalbio-verde-50 hover:text-naturalbio-verde'
            ]"
            @click="closeSidebar"
          >
            <span class="mr-3" v-html="link.icon"></span>
            {{ link.text }}
          </Link>
        </div>
      </div>
    </div>
    
    <!-- Footer con versión -->
    <div class="px-4 py-2 text-xs text-gray-500 border-t border-gray-200">
      v1.0.0
    </div>
  </div>
</template>

<style scoped>
.mobile-sidebar {
  width: 85%;
  max-width: 320px;
}
</style>