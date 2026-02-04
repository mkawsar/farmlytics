import { ref } from 'vue';

const toasts = ref([]);
let id = 0;
let timeoutIds = new Map();

const DEFAULT_DURATION = 5000;

function addToast(type, message, duration = DEFAULT_DURATION) {
    const toastId = ++id;
    const toast = {
        id: toastId,
        type,
        message,
    };
    toasts.value = [...toasts.value, toast];

    const timeoutId = setTimeout(() => {
        removeToast(toastId);
        timeoutIds.delete(toastId);
    }, duration);
    timeoutIds.set(toastId, timeoutId);

    return toastId;
}

function removeToast(toastId) {
    const tid = timeoutIds.get(toastId);
    if (tid) {
        clearTimeout(tid);
        timeoutIds.delete(toastId);
    }
    toasts.value = toasts.value.filter((t) => t.id !== toastId);
}

export function useToast() {
    return {
        toasts,
        success(message, duration = DEFAULT_DURATION) {
            return addToast('success', message, duration);
        },
        error(message, duration = DEFAULT_DURATION) {
            return addToast('error', message, duration);
        },
        remove: removeToast,
    };
}
