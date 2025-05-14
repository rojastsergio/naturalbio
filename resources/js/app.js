import './bootstrap';
import '../css/app.css';
import '../css/auth-custom.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Importar Ziggy correctamente
// La versión antigua de Ziggy no incluye vue.m.js, usar import directo de Ziggy
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Ziggy global fallback para evitar errores en producción
window.Ziggy = window.Ziggy || {
    url: 'http://52.14.251.198',
    port: null,
    defaults: {},
    routes: {
        'login': {uri: 'login', methods: ['GET', 'HEAD']},
        'dashboard': {uri: 'dashboard', methods: ['GET', 'HEAD']}
        // Puedes añadir más rutas básicas aquí
    }
};

window.route = window.route || function(name, params, absolute) {
    const base = window.Ziggy.url;
    if (name === 'login') return base + '/login';
    if (name === 'dashboard') return base + '/dashboard';
    return base + '/' + name;
};

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'NaturalBIO';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        // Comprueba si es un componente de un módulo
        const parts = name.split('/');
        
        if (parts.length > 1) {
            // Módulo Patients
            if (parts[0] === 'Patients') {
                return resolvePageComponent(
                    `../../Modules/Patients/resources/js/Pages/${parts.slice(1).join('/')}.vue`,
                    import.meta.glob('../../Modules/Patients/resources/js/Pages/**/*.vue')
                );
            }
            
            // Módulo Appointments
            if (parts[0] === 'Appointments') {
                return resolvePageComponent(
                    `../../Modules/Appointments/Resources/js/Pages/${parts.slice(1).join('/')}.vue`,
                    import.meta.glob('../../Modules/Appointments/Resources/js/Pages/**/*.vue')
                );
            }
            
            // Módulo Doctors
            if (parts[0] === 'Doctors') {
                return resolvePageComponent(
                    `../../Modules/Doctors/Resources/js/Pages/${parts.slice(1).join('/')}.vue`,
                    import.meta.glob('../../Modules/Doctors/Resources/js/Pages/**/*.vue')
                );
            }
            
            // Módulo Therapies
            if (parts[0] === 'Therapies') {
                return resolvePageComponent(
                    `../../Modules/Therapies/Resources/js/Pages/${parts.slice(1).join('/')}.vue`,
                    import.meta.glob('../../Modules/Therapies/Resources/js/Pages/**/*.vue')
                );
            }

            // Módulo Prescriptions
            if (parts[0] === 'Prescriptions') {
                return resolvePageComponent(
                    `../../Modules/Prescriptions/Resources/js/Pages/${parts.slice(1).join('/')}.vue`,
                    import.meta.glob('../../Modules/Prescriptions/Resources/js/Pages/**/*.vue')
                );
            }

            // Módulo Supplements
            if (parts[0] === 'Supplements') {
                return resolvePageComponent(
                    `../../Modules/Supplements/Resources/js/Pages/${parts.slice(1).join('/')}.vue`,
                    import.meta.glob('../../Modules/Supplements/Resources/js/Pages/**/*.vue')
                );
            }

            // Módulo Recommendations
            if (parts[0] === 'Recommendations') {
                return resolvePageComponent(
                    `../../Modules/Recommendations/Resources/js/Pages/${parts.slice(1).join('/')}.vue`,
                    import.meta.glob('../../Modules/Recommendations/Resources/js/Pages/**/*.vue')
                );
            }
        }
        
        // Si no es un componente de módulo, usa la ruta normal
        return resolvePageComponent(
            `./Pages/${name}.vue`, 
            import.meta.glob('./Pages/**/*.vue')
        );
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy) // Pasar Ziggy explícitamente
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});