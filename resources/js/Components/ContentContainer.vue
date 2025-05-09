<template>
    <AppLayout title="Pacientes">
        <ContentContainer title="Pacientes" :actions="true">
            <template #actions>
                <Link 
                    :href="route('patients.create')" 
                    class="btn btn-primary"
                >
                    Nuevo Paciente
                </Link>
            </template>
            
            <!-- El contenido original se mantiene igual -->
            <div>
                <!-- Aquí va todo tu contenido existente -->
                <input type="text" placeholder="Buscar pacientes..." class="form-input mb-4">
                
                <table class="data-table">
                    <!-- Contenido de la tabla original -->
                </table>
                
                <!-- Paginación original -->
            </div>
        </ContentContainer>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ContentContainer from '@/Components/ContentContainer.vue';

// El script original se mantiene igual
</script>