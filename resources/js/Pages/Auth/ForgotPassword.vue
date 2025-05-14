<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticationCardEnhanced from '@/Components/AuthenticationCardEnhanced.vue';
import NaturalBioLogo from '@/Components/NaturalBioLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar contraseña" />

    <AuthenticationCardEnhanced>
        <template #logo>
            <NaturalBioLogo />
        </template>

        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Recuperación de contraseña</h2>

        <div class="mb-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">
            ¿Olvidaste tu contraseña? No hay problema. Simplemente indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm bg-green-50 text-green-700 p-3 rounded-lg">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <Link :href="route('login')" class="text-sm text-green-700 hover:text-green-900 hover:underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Volver al inicio de sesión
                </Link>
                
                <button
                    type="submit"
                    class="px-5 py-2 bg-green-700 text-white font-medium rounded-md border-0 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Enviar enlace
                </button>
            </div>
        </form>
    </AuthenticationCardEnhanced>
</template>