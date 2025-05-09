<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    columns: {
        type: Array,
        required: true,
        // Formato esperado: [{ key: 'id', label: 'ID', sortable: true }, ...]
    },
    data: {
        type: Array,
        required: true
    },
    loading: {
        type: Boolean,
        default: false
    },
    currentPage: {
        type: Number,
        default: 1
    },
    lastPage: {
        type: Number,
        default: 1
    },
    perPage: {
        type: Number,
        default: 10
    },
    total: {
        type: Number,
        default: 0
    },
    actionRoute: {
        type: String,
        default: null
    },
    actionParams: {
        type: Object,
        default: () => ({})
    },
    actions: {
        type: Array,
        default: () => []
        // Formato esperado: [{ label: 'Ver', route: 'patients.show', icon: 'eye' }]
    },
    linkColumn: {
        type: String,
        default: null
    },
    emptyText: {
        type: String,
        default: 'No hay datos para mostrar'
    }
});

const emit = defineEmits(['sort', 'row-click', 'action']);

// Configuración de ordenamiento
const sortKey = ref('');
const sortOrder = ref('asc');

const handleSort = (column) => {
    if (!column.sortable) return;
    
    if (sortKey.value === column.key) {
        // Cambiar dirección si ya estamos ordenando por esta columna
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        // Nueva columna para ordenar
        sortKey.value = column.key;
        sortOrder.value = 'asc';
    }
    
    emit('sort', { key: sortKey.value, order: sortOrder.value });
};

// Paginación
const pageNumbers = computed(() => {
    const range = [];
    const maxVisiblePages = 5;
    const ellipsis = '...';
    
    if (props.lastPage <= maxVisiblePages) {
        // Mostrar todas las páginas si son menos que el máximo visible
        for (let i = 1; i <= props.lastPage; i++) {
            range.push(i);
        }
    } else {
        // Lógica para mostrar páginas con elipsis
        let start = Math.max(1, props.currentPage - 1);
        let end = Math.min(props.lastPage, start + maxVisiblePages - 1);
        
        // Ajustar si estamos al final
        if (end === props.lastPage) {
            start = Math.max(1, end - maxVisiblePages + 1);
        }
        
        // Añadir primera página
        if (start > 1) {
            range.push(1);
            if (start > 2) range.push(ellipsis);
        }
        
        // Añadir páginas del rango
        for (let i = start; i <= end; i++) {
            range.push(i);
        }
        
        // Añadir última página
        if (end < props.lastPage) {
            if (end < props.lastPage - 1) range.push(ellipsis);
            range.push(props.lastPage);
        }
    }
    
    return range;
});

const handleRowClick = (row, index) => {
    if (props.linkColumn && row[props.linkColumn]) {
        // Si hay un link en esta fila, navegar a él
        router.visit(row[props.linkColumn]);
    } else {
        // De lo contrario, emitir el evento para que el padre lo maneje
        emit('row-click', { row, index });
    }
};

const handleAction = (action, row, index) => {
    emit('action', { action, row, index });
};

// Iconos para acciones
const getActionIcon = (icon) => {
    switch (icon) {
        case 'eye':
            return `<path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />`;
        case 'edit':
            return `<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />`;
        case 'trash':
            return `<path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />`;
        case 'calendar':
            return `<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />`;
        case 'print':
            return `<path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />`;
        default:
            return `<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />`;
    }
};

// Verificar si es dispositivo móvil para UI adaptada
const isMobile = ref(false);

onMounted(() => {
    const checkMobile = () => {
        isMobile.value = window.innerWidth < 768;
    };
    
    checkMobile();
    window.addEventListener('resize', checkMobile);
    
    // Limpieza
    return () => {
        window.removeEventListener('resize', checkMobile);
    };
});
</script>

<template>
    <div>
        <!-- Loader -->
        <div v-if="loading" class="py-8 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-naturalbio-verde"></div>
            <p class="mt-2 text-gray-500">Cargando datos...</p>
        </div>
        
        <!-- Mensaje de tabla vacía -->
        <div v-else-if="data.length === 0" class="py-8 text-center">
            <p class="text-gray-500">{{ emptyText }}</p>
        </div>
        
        <div v-else>
            <!-- Tabla para Desktop -->
            <div class="hidden md:block">
                <div class="table-responsive rounded-lg shadow-sm overflow-hidden border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th 
                                    v-for="column in columns" 
                                    :key="column.key"
                                    :class="[
                                        'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider', 
                                        column.sortable ? 'cursor-pointer hover:bg-gray-100' : '',
                                        column.width ? column.width : ''
                                    ]"
                                    @click="column.sortable && handleSort(column)"
                                >
                                    <div class="flex items-center">
                                        {{ column.label }}
                                        
                                        <span v-if="column.sortable" class="ml-1">
                                            <template v-if="sortKey === column.key">
                                                <svg v-if="sortOrder === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </template>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-0 group-hover:opacity-50" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                </th>
                                
                                <!-- Columna de acciones si hay acciones definidas -->
                                <th v-if="actions.length > 0" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr 
                                v-for="(row, index) in data" 
                                :key="index"
                                class="hover:bg-gray-50 transition-colors"
                                :class="{'cursor-pointer': linkColumn || $listeners && $listeners['row-click']}"
                                @click="linkColumn || ($listeners && $listeners['row-click']) ? handleRowClick(row, index) : null"
                            >
                                <td 
                                    v-for="column in columns" 
                                    :key="`${index}-${column.key}`"
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    <!-- Slot para contenido personalizado de celda -->
                                    <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                        {{ row[column.key] }}
                                    </slot>
                                </td>
                                
                                <!-- Acciones -->
                                <td v-if="actions.length > 0" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                                    <div class="flex justify-end space-x-2">
                                        <template v-for="(action, actionIndex) in actions">
                                            <!-- Link con ruta dinámica -->
                                            <Link 
                                                v-if="action.route" 
                                                :key="`action-link-${actionIndex}`"
                                                :href="route(action.route, { ...actionParams, id: row.id })"
                                                class="text-gray-500 hover:text-naturalbio-verde p-1 rounded hover:bg-naturalbio-verde-50"
                                                :title="action.label"
                                            >
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    class="h-5 w-5" 
                                                    viewBox="0 0 20 20" 
                                                    fill="currentColor"
                                                    v-html="getActionIcon(action.icon)"
                                                ></svg>
                                            </Link>
                                            
                                            <!-- Botón para acciones personalizadas -->
                                            <button 
                                                v-else
                                                :key="`action-button-${actionIndex}`"
                                                @click="handleAction(action, row, index)"
                                                class="text-gray-500 hover:text-naturalbio-verde p-1 rounded hover:bg-naturalbio-verde-50"
                                                :title="action.label"
                                            >
                                                <svg 
                                                    xmlns="http://www.w3.org/2000/svg" 
                                                    class="h-5 w-5" 
                                                    viewBox="0 0 20 20" 
                                                    fill="currentColor"
                                                    v-html="getActionIcon(action.icon)"
                                                ></svg>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Vista Móvil: Tarjetas en lugar de Tabla -->
            <div class="md:hidden">
                <div class="space-y-3">
                    <div 
                        v-for="(row, index) in data" 
                        :key="index"
                        class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
                        :class="{'cursor-pointer hover:shadow-md transition-shadow': linkColumn || $listeners && $listeners['row-click']}"
                        @click="linkColumn || ($listeners && $listeners['row-click']) ? handleRowClick(row, index) : null"
                    >
                        <div class="p-4 space-y-2">
                            <div 
                                v-for="column in columns.filter(col => !col.hideOnMobile)" 
                                :key="`mobile-${index}-${column.key}`"
                                class="flex justify-between items-center"
                            >
                                <span class="text-sm font-medium text-gray-600">{{ column.label }}:</span>
                                <div class="text-sm text-gray-900 text-right flex-1 ml-4">
                                    <!-- Slot para contenido personalizado de celda -->
                                    <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                        {{ row[column.key] }}
                                    </slot>
                                </div>
                            </div>
                            
                            <!-- Acciones en móvil -->
                            <div v-if="actions.length > 0" class="mt-4 flex justify-end space-x-3 border-t border-gray-200 pt-3" @click.stop>
                                <template v-for="(action, actionIndex) in actions">
                                    <!-- Link con ruta dinámica -->
                                    <Link 
                                        v-if="action.route" 
                                        :key="`mobile-action-link-${actionIndex}`"
                                        :href="route(action.route, { ...actionParams, id: row.id })"
                                        class="text-gray-500 hover:text-naturalbio-verde inline-flex items-center text-sm"
                                        :title="action.label"
                                    >
                                        <svg 
                                            xmlns="http://www.w3.org/2000/svg" 
                                            class="h-4 w-4 mr-1" 
                                            viewBox="0 0 20 20" 
                                            fill="currentColor"
                                            v-html="getActionIcon(action.icon)"
                                        ></svg>
                                        {{ action.label }}
                                    </Link>
                                    
                                    <!-- Botón para acciones personalizadas -->
                                    <button 
                                        v-else
                                        :key="`mobile-action-button-${actionIndex}`"
                                        @click="handleAction(action, row, index)"
                                        class="text-gray-500 hover:text-naturalbio-verde inline-flex items-center text-sm"
                                        :title="action.label"
                                    >
                                        <svg 
                                            xmlns="http://www.w3.org/2000/svg" 
                                            class="h-4 w-4 mr-1" 
                                            viewBox="0 0 20 20" 
                                            fill="currentColor"
                                            v-html="getActionIcon(action.icon)"
                                        ></svg>
                                        {{ action.label }}
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Paginación -->
            <div v-if="lastPage > 1" class="py-3 flex items-center justify-between border-t border-gray-200 mt-4">
                <div class="flex-1 flex justify-between sm:hidden">
                    <Link 
                        :href="actionRoute ? route(actionRoute, { ...actionParams, page: currentPage - 1 }) : '#'"
                        :class="[
                            'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md',
                            currentPage === 1 
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                        :disabled="currentPage === 1"
                        :preserve-scroll="true"
                    >
                        Anterior
                    </Link>
                    <Link 
                        :href="actionRoute ? route(actionRoute, { ...actionParams, page: currentPage + 1 }) : '#'"
                        :class="[
                            'ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md',
                            currentPage === lastPage 
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                : 'bg-white text-gray-700 hover:bg-gray-50'
                        ]"
                        :disabled="currentPage === lastPage"
                        :preserve-scroll="true"
                    >
                        Siguiente
                    </Link>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando 
                            <span class="font-medium">{{ ((currentPage - 1) * perPage) + 1 }}</span>
                            a 
                            <span class="font-medium">{{ Math.min(currentPage * perPage, total) }}</span>
                            de 
                            <span class="font-medium">{{ total }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <Link 
                                :href="actionRoute ? route(actionRoute, { ...actionParams, page: currentPage - 1 }) : '#'"
                                :class="[
                                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium',
                                    currentPage === 1 
                                        ? 'text-gray-300 cursor-not-allowed' 
                                        : 'text-gray-500 hover:bg-gray-50'
                                ]"
                                :disabled="currentPage === 1"
                                :preserve-scroll="true"
                            >
                                <span class="sr-only">Anterior</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                            
                            <template v-for="(page, pageIndex) in pageNumbers">
                                <span
                                    v-if="page === '...'"
                                    :key="`ellipsis-${pageIndex}`"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                                >
                                    ...
                                </span>
                                
                                <Link
                                    v-else
                                    :key="`page-${page}`"
                                    :href="actionRoute ? route(actionRoute, { ...actionParams, page }) : '#'"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        page === currentPage
                                            ? 'z-10 bg-naturalbio-verde-50 border-naturalbio-verde-300 text-naturalbio-verde-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                    ]"
                                    :preserve-scroll="true"
                                >
                                    {{ page }}
                                </Link>
                            </template>
                            
                            <Link 
                                :href="actionRoute ? route(actionRoute, { ...actionParams, page: currentPage + 1 }) : '#'"
                                :class="[
                                    'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium',
                                    currentPage === lastPage 
                                        ? 'text-gray-300 cursor-not-allowed' 
                                        : 'text-gray-500 hover:bg-gray-50'
                                ]"
                                :disabled="currentPage === lastPage"
                                :preserve-scroll="true"
                            >
                                <span class="sr-only">Siguiente</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-responsive {
    @apply w-full overflow-x-auto;
}

/* Scrollbar personalizado */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    @apply bg-transparent;
}

.table-responsive::-webkit-scrollbar-thumb {
    @apply bg-gray-300 rounded-full;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400;
}

/* Correcciones para pantallas grandes */
@media (min-width: 1280px) {
    .table-responsive {
        overflow-x: visible;
    }
}

/* Animación sutil para hover en filas */
tr {
    transition: background-color 0.15s ease-in-out;
}
</style>