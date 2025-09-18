import '../css/app.css';
import './bootstrap';
import { initAjaxForms } from './forms';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Learn About Health';

// Add loading states
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});

// Add page transition effects
document.addEventListener('DOMContentLoaded', () => {
    // Smooth page transitions for internal links
    const links = document.querySelectorAll('a[href^="/"]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            document.body.classList.add('page-exit');
        });
    });
    
    // Remove loading class after navigation
    document.body.classList.remove('page-exit');
});
document.addEventListener('DOMContentLoaded', () => {
    initAjaxForms();
});
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
