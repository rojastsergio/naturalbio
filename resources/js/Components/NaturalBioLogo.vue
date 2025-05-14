<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
        <div class="flex flex-col items-center">
            <div class="w-32 h-32 overflow-hidden rounded-full border-4 border-green-600 flex items-center justify-center bg-white naturalbio-logo">
                <div class="text-green-700 font-bold text-4xl">NB</div>
            </div>
            <div class="mt-3 text-center">
                <div class="text-2xl font-bold text-green-800">NaturalBio</div>
                <div class="text-sm text-green-600">Soluciones Naturales</div>
            </div>
        </div>
    </Link>
</template>