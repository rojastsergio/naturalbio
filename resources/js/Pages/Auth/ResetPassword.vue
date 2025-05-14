<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticationCardEnhanced from '@/Components/AuthenticationCardEnhanced.vue';
import NaturalBioLogo from '@/Components/NaturalBioLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Restablecer contraseña" />

    <AuthenticationCardEnhanced>
        <template #logo>
            <NaturalBioLogo />
        </template>

        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Restablecer contraseña</h2>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full auth-input"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Nueva contraseña" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full auth-input"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full auth-input"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <Link :href="route('login')" class="text-sm auth-link rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Volver al inicio de sesión
                </Link>
                
                <button
                    type="submit"
                    class="px-5 py-2 bg-green-700 text-white font-medium rounded-md border-0 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 auth-button"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Restablecer contraseña
                </button>
            </div>
        </form>
    </AuthenticationCardEnhanced>
</template>