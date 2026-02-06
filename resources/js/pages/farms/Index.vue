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

                <DataTable class="mt-8">
                    <Table>
                        <template #head>
                            <FwbTableHeadCell>Name</FwbTableHeadCell>
                            <FwbTableHeadCell>Location</FwbTableHeadCell>
                            <FwbTableHeadCell>Type</FwbTableHeadCell>
                            <FwbTableHeadCell>Capacity</FwbTableHeadCell>
                            <FwbTableHeadCell>
                                <span class="sr-only">Actions</span>
                            </FwbTableHeadCell>
                        </template>
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
                    </Table>
                    <template #pagination>
                        <Pagination :paginator="farms" />
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import {
    FwbButton,
    FwbTableCell,
    FwbTableHeadCell,
    FwbTableRow,
} from 'flowbite-vue';
import { useConfirmDelete } from '../../composables/useConfirmDelete';
import AppLayout from '../../layouts/AppLayout.vue';
import DataTable from '../../components/DataTable.vue';
import Pagination from '../../components/Pagination.vue';
import Table from '../../components/Table.vue';

defineProps({
    farms: {
        type: Object,
        required: true,
    },
});

const { open: openConfirmDelete } = useConfirmDelete();

const FARM_TYPES = {
    dairy: 'Dairy',
    fattening: 'Fattening',
    mixed: 'Mixed',
};

function farmTypeLabel(type) {
    return type ? (FARM_TYPES[type] ?? type) : '—';
}

function confirmDelete(farm) {
    openConfirmDelete({
        title: 'Delete farm',
        message: `Are you sure you want to delete <strong>${farm.name}</strong>? This can be undone later.`,
        onConfirm: () => router.delete(`/farms/${farm.id}`, { preserveScroll: true }),
    });
}
</script>
