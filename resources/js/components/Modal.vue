<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-show="show"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title"
                @keydown.escape="close"
            >
                <div
                    class="fixed inset-0 bg-stone-900/50 dark:bg-stone-950/70"
                    aria-hidden="true"
                    @click="close"
                />
                <div
                    class="relative max-h-[90vh] w-full max-w-md overflow-auto rounded-2xl border border-stone-200/80 bg-white shadow-xl dark:border-stone-700 dark:bg-stone-800"
                    @click.stop
                >
                    <slot />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

function close() {
    emit('close');
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.2s ease;
}
.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
}
</style>
