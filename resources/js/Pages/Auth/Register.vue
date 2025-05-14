<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCardEnhanced from '@/Components/AuthenticationCardEnhanced.vue';
import NaturalBioLogo from '@/Components/NaturalBioLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registro" />

    <AuthenticationCardEnhanced>
        <template #logo>
            <NaturalBioLogo />
        </template>

        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Crear cuenta en NaturalBio</h2>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nombre" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full auth-input"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full auth-input"
                    required
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

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2 text-sm text-gray-600">
                            Acepto los <a target="_blank" :href="route('terms.show')" class="text-green-700 hover:text-green-900 hover:underline">Términos de servicio</a> y <a target="_blank" :href="route('policy.show')" class="text-green-700 hover:text-green-900 hover:underline">Política de privacidad</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-6">
                <Link :href="route('login')" class="text-sm auth-link rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    ¿Ya tienes cuenta?
                </Link>

                <button
                    type="submit"
                    class="ms-4 px-5 py-2 bg-green-700 text-white font-medium rounded-md border-0 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 auth-button"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Registrarse
                </button>
            </div>
        </form>
    </AuthenticationCardEnhanced>
</template>