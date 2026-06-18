import './bootstrap'; // helpers de Laravel (axios)

// Framework Bootstrap (JS: dropdowns, modales, etc.)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Chart.js para dashboards
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Alpine.js para reactividad ligera
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// PWA: registro del service worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
