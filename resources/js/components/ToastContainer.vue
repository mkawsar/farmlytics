<template>
    <div
        class="pointer-events-none fixed bottom-0 right-0 z-[100] flex flex-col gap-3 p-4 sm:bottom-4 sm:right-4 sm:p-0"
        aria-live="polite"
    >
        <TransitionGroup name="toast">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex min-w-[280px] max-w-sm items-start gap-3 rounded-xl border px-4 py-3 shadow-lg transition"
                :class="toastClasses(toast.type)"
                role="alert"
            >
                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-lg font-bold"
                    :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400'"
                    aria-hidden="true"
                >
                    {{ toastIcon(toast.type) }}
                </span>
                <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
                <button
                    type="button"
                    class="shrink-0 rounded p-1 opacity-70 transition hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-2"
                    :class="toastCloseClass(toast.type)"
                    aria-label="Dismiss"
                    @click="remove(toast.id)"
                >
                    ×
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '../composables/useToast';

const { toasts, success, error, remove } = useToast();
const page = usePage();

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;
        if (flash.success) success(flash.success);
        if (flash.error) error(flash.error);
    },
    { immediate: true }
);

function toastIcon(type) {
    return type === 'success' ? '✓' : '!';
}

function toastClasses(type) {
    const base = 'bg-white shadow-lg dark:bg-stone-800';
    if (type === 'success') {
        return `${base} border border-emerald-200/80 text-stone-900 dark:border-emerald-800 dark:text-stone-100`;
    }
    return `${base} border border-red-200/80 text-stone-900 dark:border-red-900/50 dark:text-stone-100`;
}

function toastCloseClass(type) {
    if (type === 'success') {
        return 'text-stone-500 hover:bg-stone-100 focus:ring-stone-300 dark:text-stone-400 dark:hover:bg-stone-700';
    }
    return 'text-stone-500 hover:bg-red-50 focus:ring-red-300 dark:text-stone-400 dark:hover:bg-red-900/30';
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.25s ease;
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
.toast-move {
    transition: transform 0.25s ease;
}
</style>
