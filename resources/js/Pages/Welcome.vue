<template>
    <Head title="Bienvenido" />

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-white selection:bg-naturalbio-verde/25 selection:text-naturalbio-verde">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="font-title font-semibold text-naturalbio-verde hover:text-naturalbio-azul focus:outline-none focus:underline transition ease-in-out duration-150">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" class="font-title font-semibold text-naturalbio-verde hover:text-naturalbio-azul focus:outline-none focus:underline transition ease-in-out duration-150">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 font-title font-semibold text-naturalbio-verde hover:text-naturalbio-azul focus:outline-none focus:underline transition ease-in-out duration-150">
                    Register
                </Link>
            </template>
        </div>

        <NaturalBioHeroSection>
            <template #cta-primary>
                <NaturalBioButton variant="primary">Explorar Productos</NaturalBioButton>
            </template>
            <template #cta-secondary>
                <NaturalBioButton variant="outline">Agendar Consulta</NaturalBioButton>
            </template>
            <template #image>
                <div class="rounded-xl overflow-hidden shadow-xl">
                    <img src="/placeholder-natural-products.jpg" alt="Productos Naturales" class="w-full h-auto" />
                </div>
            </template>
            <template #catalog-cards>
                <NaturalBioServiceCard 
                    title="Suplementos" 
                    description="Nutrición viva para tu bienestar diario"
                    type="product">
                    <template #icon>
                        <div class="w-12 h-12 rounded-full bg-naturalbio-dorado/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-naturalbio-dorado" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                    </template>
                    <template #action>
                        <NaturalBioButton variant="premium" size="sm">Ver Catálogo</NaturalBioButton>
                    </template>
                </NaturalBioServiceCard>

                <NaturalBioServiceCard 
                    title="Cuidado de la Piel" 
                    description="Productos orgánicos para una piel radiante"
                    type="product">
                    <template #icon>
                        <div class="w-12 h-12 rounded-full bg-naturalbio-dorado/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-naturalbio-dorado" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                    </template>
                    <template #action>
                        <NaturalBioButton variant="premium" size="sm">Ver Productos</NaturalBioButton>
                    </template>
                </NaturalBioServiceCard>

                <NaturalBioServiceCard 
                    title="Aromaterapia" 
                    description="Esencias naturales para equilibrar cuerpo y mente"
                    type="product">
                    <template #icon>
                        <div class="w-12 h-12 rounded-full bg-naturalbio-dorado/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-naturalbio-dorado" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                    </template>
                    <template #action>
                        <NaturalBioButton variant="premium" size="sm">Ver Aceites</NaturalBioButton>
                    </template>
                </NaturalBioServiceCard>
            </template>
        </NaturalBioHeroSection>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import NaturalBioButton from '@/Components/NaturalBio/Button.vue';
import NaturalBioServiceCard from '@/Components/NaturalBio/ServiceCard.vue';
import NaturalBioHeroSection from '@/Components/NaturalBio/HeroSection.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
    },
    phpVersion: {
        type: String,
    },
});
</script>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(76, 175, 80, 0.07)'/%3E%3C/svg%3E");
}
</style>