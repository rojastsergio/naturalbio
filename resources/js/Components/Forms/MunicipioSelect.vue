<script setup>
import { ref, watch, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    municipioId: {
        type: [Number, String],
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:municipioId', 'municipioSelected', 'departamentoChanged']);

const municipioId = ref(props.municipioId);
const searchQuery = ref('');
const municipios = ref([]);
const departamento = ref(null);
const isLoading = ref(false);
const showDropdown = ref(false);
const selectedMunicipio = ref(null);

// Vigilar cambios en el ID de municipio seleccionado
watch(municipioId, (newValue) => {
    emit('update:municipioId', newValue);
    
    // Si el ID cambia y tenemos un municipio seleccionado, emitir evento
    if (newValue && selectedMunicipio.value) {
        emit('municipioSelected', selectedMunicipio.value);
        
        // Emitir el departamento si hay un cambio
        if (departamento.value) {
            emit('departamentoChanged', departamento.value);
        }
    }
});

// Buscar municipios cuando cambia la consulta de búsqueda
watch(searchQuery, async (newQuery) => {
    if (newQuery.length < 2) {
        municipios.value = [];
        showDropdown.value = false;
        return;
    }
    
    isLoading.value = true;
    try {
        const response = await router.visit(route('api.municipios.search', { q: newQuery }), {
            only: ['municipios'],
            preserveState: true,
            preserveScroll: true,
            method: 'get'
        });
        
        municipios.value = response?.props?.municipios || [];
        showDropdown.value = municipios.value.length > 0;
    } catch (error) {
        console.error('Error al buscar municipios:', error);
        municipios.value = [];
    } finally {
        isLoading.value = false;
    }
});

// Cargar el municipio seleccionado al montar el componente
onMounted(async () => {
    if (municipioId.value) {
        try {
            const response = await router.visit(route('api.municipios.show', { id: municipioId.value }), {
                only: ['municipio'],
                preserveState: true,
                preserveScroll: true,
                method: 'get'
            });
            
            if (response?.props?.municipio) {
                selectedMunicipio.value = response.props.municipio;
                searchQuery.value = selectedMunicipio.value.nombre;
                departamento.value = selectedMunicipio.value.departamento;
                
                // Emitir el departamento
                emit('departamentoChanged', departamento.value);
            }
        } catch (error) {
            console.error('Error al cargar el municipio:', error);
        }
    }
});

// Seleccionar un municipio
const selectMunicipio = (municipio) => {
    municipioId.value = municipio.id;
    selectedMunicipio.value = municipio;
    searchQuery.value = municipio.nombre;
    departamento.value = municipio.departamento;
    showDropdown.value = false;
    
    // Emitir eventos
    emit('municipioSelected', municipio);
    emit('departamentoChanged', municipio.departamento);
};
</script>

<template>
    <div class="relative w-full">
        <label :for="'municipio-select'" class="block text-sm font-medium text-gray-700 mb-1">
            Municipio <span v-if="required" class="text-red-600">*</span>
        </label>
        
        <div class="relative">
            <!-- Campo de búsqueda -->
            <input
                :id="'municipio-select'"
                type="text"
                v-model="searchQuery"
                :disabled="disabled"
                :required="required"
                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring-naturalbio-verde focus:border-naturalbio-verde"
                :class="{ 'border-red-500': error }"
                placeholder="Buscar municipio..."
                autocomplete="off"
                @focus="showDropdown = searchQuery.length >= 2 && municipios.length > 0"
                @blur="setTimeout(() => showDropdown = false, 200)"
            />
            
            <!-- Icono de carga -->
            <div v-if="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="animate-spin h-5 w-5 text-naturalbio-verde" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <!-- Icono de búsqueda -->
            <div v-else class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
        
        <!-- Lista de resultados -->
        <div v-if="showDropdown" class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg max-h-60 overflow-auto">
            <ul class="py-1">
                <li 
                    v-for="municipio in municipios" 
                    :key="municipio.id"
                    @mousedown="selectMunicipio(municipio)"
                    class="px-3 py-2 cursor-pointer hover:bg-gray-100"
                >
                    <div class="font-medium">{{ municipio.nombre }}</div>
                    <div class="text-sm text-gray-500">{{ municipio.departamento.nombre }}</div>
                </li>
            </ul>
        </div>
        
        <!-- Mensaje de error -->
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>