import { ref } from 'vue';

// Singleton state so the global modal and any page share the same instance
const visible = ref(false);
const title = ref('Delete');
const message = ref('');
const loading = ref(false);
let onConfirmFn = null;

/**
 * Global delete confirmation. Use from any page when user clicks Delete.
 * The global ConfirmDeleteModal component (in app.js) shows the dialog.
 *
 * @param {Object} options
 * @param {string} options.title - Modal title (e.g. 'Delete farm')
 * @param {string} options.message - Body message (HTML allowed, e.g. `Are you sure you want to delete <strong>${name}</strong>?`)
 * @param {() => void | Promise<void>} options.onConfirm - Called when user clicks Delete (sync or async)
 */
export function useConfirmDelete() {
    function open(options = {}) {
        title.value = options.title ?? 'Delete';
        message.value = options.message ?? 'Are you sure?';
        onConfirmFn = typeof options.onConfirm === 'function' ? options.onConfirm : null;
        loading.value = false;
        visible.value = true;
    }

    function close() {
        if (loading.value) return;
        visible.value = false;
        onConfirmFn = null;
    }

    async function confirm() {
        if (!onConfirmFn) {
            close();
            return;
        }
        loading.value = true;
        try {
            const result = onConfirmFn();
            if (result && typeof result.then === 'function') {
                await result;
            }
        } finally {
            loading.value = false;
            close();
        }
    }

    return {
        visible,
        title,
        message,
        loading,
        open,
        close,
        confirm,
    };
}
