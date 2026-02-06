import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import ConfirmDeleteModal from './components/ConfirmDeleteModal.vue'
import ToastContainer from './components/ToastContainer.vue'

createInertiaApp({
    title: title => title ? `CowKeeper - ${title}` : 'CowKeeper',
    resolve: name => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
        return pages[`./pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h('div', { class: 'contents' }, [
                h(App, props),
                h(ToastContainer),
                h(ConfirmDeleteModal),
            ]),
        })
        app.use(plugin)
        app.mount(el)
    },
});
