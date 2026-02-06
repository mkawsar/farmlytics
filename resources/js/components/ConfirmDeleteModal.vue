<template>
    <Modal :show="visible" @close="close">
        <div class="p-6">
            <h3 id="modal-title" class="text-lg font-semibold text-stone-900 dark:text-stone-100">
                {{ title }}
            </h3>
            <p class="mt-2 text-stone-600 dark:text-stone-400" v-html="message" />
            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                    :disabled="loading"
                    @click="close"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-70"
                    :disabled="loading"
                    @click="confirm"
                >
                    {{ loading ? 'Deleting…' : 'Delete' }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { useConfirmDelete } from '../composables/useConfirmDelete';
import Modal from './Modal.vue';

const { visible, title, message, loading, close, confirm } = useConfirmDelete();
</script>
