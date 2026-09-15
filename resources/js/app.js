import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const el = document.getElementById('app');
const initialPage = el?.dataset?.page ? JSON.parse(el.dataset.page) : undefined;

createInertiaApp({
    page: initialPage,
    title: (title) => title ? `${title} - Guess-X` : 'Guess-X: Ultimate Word Game',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`];
        return page?.default || page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
