<template>
    <Head :title="farm.name" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="max-w-3xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Link
                            href="/farms"
                            class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                        >
                            ← Back to farms
                        </Link>
                        <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">{{ farm.name }}</h1>
                        <p class="mt-1 text-stone-500 dark:text-stone-400">Farm details</p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="`/farms/${farm.id}/edit`"
                            class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            @click="confirmDelete"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <div class="mt-8 rounded-xl border border-stone-200/80 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <dl class="divide-y divide-stone-200 dark:divide-stone-700">
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Name</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ farm.name }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Location</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ farm.location || '—' }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Type</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ farmTypeLabel(farm.type) }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Capacity</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ farm.capacity != null ? farm.capacity : '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <Modal v-if="showDeleteModal" :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">Delete farm</h3>
                <p class="mt-2 text-stone-600 dark:text-stone-400">
                    Are you sure you want to delete <strong>{{ farm.name }}</strong>? This can be undone later.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                        @click="showDeleteModal = false"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        :disabled="deleting"
                        @click="performDelete"
                    >
                        {{ deleting ? 'Deleting…' : 'Delete' }}
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
import Modal from '../../components/Modal.vue';

const props = defineProps({
    farm: {
        type: Object,
        required: true,
    },
});

const showDeleteModal = ref(false);
const deleting = ref(false);

const FARM_TYPES = {
    dairy: 'Dairy',
    fattening: 'Fattening',
    mixed: 'Mixed',
};

function farmTypeLabel(type) {
    return type ? (FARM_TYPES[type] ?? type) : '—';
}

function confirmDelete() {
    showDeleteModal.value = true;
}

function performDelete() {
    deleting.value = true;
    router.delete(`/farms/${props.farm.id}`, {
        onFinish: () => {
            deleting.value = false;
            showDeleteModal.value = false;
        },
    });
}
</script>
