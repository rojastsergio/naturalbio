<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCardEnhanced from '@/Components/AuthenticationCardEnhanced.vue';
import NaturalBioLogo from '@/Components/NaturalBioLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <AuthenticationCardEnhanced>
        <template #logo>
            <NaturalBioLogo />
        </template>

        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Bienvenido a NaturalBio</h2>

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
                    class="mt-1 block w-full auth-input"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full auth-input"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">Recordarme</span>
                </label>
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-end mb-2">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm auth-link rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        ¿Olvidaste tu contraseña?
                    </Link>

                    <button
                        type="submit"
                        class="ms-4 px-5 py-2 bg-green-700 text-white font-medium rounded-md border-0 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 auth-button"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Iniciar sesión
                    </button>
                </div>

                <div class="text-center pt-4 mt-4 border-t border-gray-200">
                    <Link :href="route('register')" class="text-sm auth-link hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        ¿No tienes cuenta? Regístrate aquí
                    </Link>
                </div>
            </div>
        </form>
    </AuthenticationCardEnhanced>
</template>