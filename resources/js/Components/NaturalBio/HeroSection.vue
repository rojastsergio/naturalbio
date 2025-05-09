<template>
    <div class="bg-gradient-to-r from-naturalbio-verde/10 to-naturalbio-azul/10 py-16">
        <div class="container-naturalbio">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-title font-bold text-naturalbio-gris mb-4">
                        <span class="text-naturalbio-verde">Vitalidad Natural</span> para una Vida Sana
                    </h1>
                    <p class="text-lg text-naturalbio-gris mb-6">
                        Soluciones avanzadas en cuidado natural y sostenible de la salud.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <slot name="cta-primary"></slot>
                        <slot name="cta-secondary"></slot>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <slot name="image"></slot>
                </div>
            </div>
            <div class="text-center mt-16">
                <h2 class="heading-secondary text-2xl mb-3">Nuestro Catálogo</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <slot name="catalog-cards"></slot>
                </div>
            </div>
        </div>
    </div>
</template>