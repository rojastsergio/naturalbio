import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Asegurarse de que Ziggy siempre esté disponible
try {
    window.Ziggy = window.Ziggy || {
        url: 'http://52.14.251.198',
        port: null,
        defaults: {},
        routes: {
            'login': {uri: 'login', methods: ['GET', 'HEAD']},
            'dashboard': {uri: 'dashboard', methods: ['GET', 'HEAD']}
        }
    };

    window.route = window.route || function(name, params, absolute) {
        const base = window.Ziggy.url;
        if (name === 'login') return base + '/login';
        if (name === 'dashboard') return base + '/dashboard';
        return base + '/' + name;
    };
} catch (e) {
    console.error('Error initializing Ziggy:', e);
}