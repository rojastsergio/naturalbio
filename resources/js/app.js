import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    // Agregar la base URL para el subdirectorio
    base: '/naturalbiosolutions',
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
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});