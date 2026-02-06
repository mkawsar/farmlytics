<template>
    <Head title="Farms" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Farms</h1>
                        <p class="mt-1 text-stone-500 dark:text-stone-400">Manage your farms and their details.</p>
                    </div>
                    <FwbButton
                        href="/farms/create"
                        color="green"
                        size="sm"
                        class="w-fit"
                    >
                        <template #prefix>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                        </template>
                        Add farm
                    </FwbButton>
                </div>

                <div class="mt-8 overflow-hidden shadow-md sm:rounded-lg">
                    <FwbTable hoverable>
                        <FwbTableHead>
                            <FwbTableHeadCell>Name</FwbTableHeadCell>
                            <FwbTableHeadCell>Location</FwbTableHeadCell>
                            <FwbTableHeadCell>Type</FwbTableHeadCell>
                            <FwbTableHeadCell>Capacity</FwbTableHeadCell>
                            <FwbTableHeadCell>
                                <span class="sr-only">Actions</span>
                            </FwbTableHeadCell>
                        </FwbTableHead>
                        <FwbTableBody>
                            <FwbTableRow v-for="farm in farms.data" :key="farm.id">
                                <FwbTableCell class="font-medium text-gray-900 dark:text-white">
                                    <Link
                                        :href="`/farms/${farm.id}`"
                                        class="text-green-600 hover:underline dark:text-green-500"
                                    >
                                        {{ farm.name }}
                                    </Link>
                                </FwbTableCell>
                                <FwbTableCell>{{ farm.location || '—' }}</FwbTableCell>
                                <FwbTableCell>{{ farmTypeLabel(farm.type) }}</FwbTableCell>
                                <FwbTableCell>{{ farm.capacity != null ? farm.capacity : '—' }}</FwbTableCell>
                                <FwbTableCell class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="`/farms/${farm.id}/edit`"
                                            class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            type="button"
                                            class="font-medium text-red-600 hover:underline dark:text-red-500"
                                            @click="confirmDelete(farm)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </FwbTableCell>
                            </FwbTableRow>
                            <FwbTableRow v-if="!farms.data || farms.data.length === 0">
                                <FwbTableCell
                                    colspan="5"
                                    class="px-6 py-16 text-center text-gray-500 dark:text-gray-400"
                                >
                                    <div class="flex min-h-[280px] flex-col items-center justify-center gap-1">
                                        <span>No farms yet.</span>
                                        <Link href="/farms/create" class="font-medium text-green-600 hover:underline dark:text-green-500">Add your first farm</Link>
                                    </div>
                                </FwbTableCell>
                            </FwbTableRow>
                        </FwbTableBody>
                    </FwbTable>

                    <div
                        v-if="farms.last_page > 1"
                        class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-200 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <p class="text-sm text-gray-700 dark:text-gray-400">
                            Showing {{ farms.from }}–{{ farms.to }} of {{ farms.total }}
                        </p>
                        <div class="flex gap-1">
                            <Link
                                v-for="link in farms.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white',
                                    link.active ? 'border-green-500 bg-green-50 text-green-600 dark:border-green-500 dark:bg-green-900/20 dark:text-green-500' : '',
                                    !link.url ? 'pointer-events-none cursor-not-allowed opacity-50' : ''
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal v-if="farmToDelete" :show="!!farmToDelete" @close="farmToDelete = null">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">Delete farm</h3>
                <p class="mt-2 text-stone-600 dark:text-stone-400">
                    Are you sure you want to delete <strong>{{ farmToDelete.name }}</strong>? This can be undone later.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        class="rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                        @click="farmToDelete = null"
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
import {
    FwbButton,
    FwbTable,
    FwbTableBody,
    FwbTableCell,
    FwbTableHead,
    FwbTableHeadCell,
    FwbTableRow,
} from 'flowbite-vue';
import AppLayout from '../../layouts/AppLayout.vue';
import Modal from '../../components/Modal.vue';

const props = defineProps({
    farms: {
        type: Object,
        required: true,
    },
});

const farmToDelete = ref(null);
const deleting = ref(false);

const FARM_TYPES = {
    dairy: 'Dairy',
    fattening: 'Fattening',
    mixed: 'Mixed',
};

function farmTypeLabel(type) {
    return type ? (FARM_TYPES[type] ?? type) : '—';
}

function confirmDelete(farm) {
    farmToDelete.value = farm;
}

function performDelete() {
    if (!farmToDelete.value) return;
    deleting.value = true;
    router.delete(`/farms/${farmToDelete.value.id}`, {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            farmToDelete.value = null;
        },
    });
}
</script>
