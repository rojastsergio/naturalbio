<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-blue-50 auth-gradient-bg">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-6 bg-white shadow-xl overflow-hidden sm:rounded-lg border border-green-100 auth-card">
            <slot />
        </div>
    </div>
</template>