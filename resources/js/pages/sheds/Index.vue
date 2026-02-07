<template>
    <Head title="Sheds" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Link
                            :href="`/farms/${farm.id}`"
                            class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                        >
                            ← Back to {{ farm.name }}
                        </Link>
                        <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Sheds</h1>
                        <p class="mt-1 text-stone-500 dark:text-stone-400">Manage sheds for {{ farm.name }}.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            v-if="selectedIds.length > 0"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:bg-stone-800 dark:text-red-400 dark:hover:bg-red-900/30"
                            @click="confirmBulkDelete"
                        >
                            Delete selected ({{ selectedIds.length }})
                        </button>
                        <FwbButton
                            :href="`/farms/${farm.id}/sheds/create`"
                            color="green"
                            size="sm"
                            class="w-fit"
                        >
                            <template #prefix>
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                            </template>
                            Add shed
                        </FwbButton>
                    </div>
                </div>

                <form
                    class="mt-6 flex gap-2"
                    @submit.prevent="submitSearch"
                >
                    <input
                        v-model="searchQuery"
                        type="search"
                        name="search"
                        placeholder="Search by name..."
                        class="block w-full max-w-sm rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400"
                        aria-label="Search sheds"
                    />
                    <button
                        type="submit"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Search
                    </button>
                    <Link
                        v-if="filters.search"
                        :href="`/farms/${farm.id}/sheds`"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Clear
                    </Link>
                </form>

                <DataTable class="mt-6">
                    <Table>
                        <template #head>
                            <FwbTableHeadCell class="w-10">
                                <input
                                    v-if="sheds.data && sheds.data.length > 0"
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    :indeterminate.prop="isIndeterminate"
                                    aria-label="Select all sheds on this page"
                                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700"
                                    @change="toggleSelectAll"
                                />
                            </FwbTableHeadCell>
                            <FwbTableHeadCell>Name</FwbTableHeadCell>
                            <FwbTableHeadCell>Type</FwbTableHeadCell>
                            <FwbTableHeadCell>Capacity</FwbTableHeadCell>
                            <FwbTableHeadCell>
                                <span class="sr-only">Actions</span>
                            </FwbTableHeadCell>
                        </template>
                        <FwbTableRow v-for="shed in sheds.data" :key="shed.id">
                            <FwbTableCell class="w-10">
                                <input
                                    type="checkbox"
                                    :checked="selectedIds.includes(shed.id)"
                                    :aria-label="`Select ${shed.name}`"
                                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700"
                                    @change="toggleSelect(shed.id)"
                                />
                            </FwbTableCell>
                            <FwbTableCell class="font-medium text-gray-900 dark:text-white">
                                <Link
                                    :href="`/sheds/${shed.id}`"
                                    class="text-green-600 hover:underline dark:text-green-500"
                                >
                                    {{ shed.name }}
                                </Link>
                            </FwbTableCell>
                            <FwbTableCell>{{ shedTypeLabel(shed.type) }}</FwbTableCell>
                            <FwbTableCell>{{ shed.capacity != null ? shed.capacity : '—' }}</FwbTableCell>
                            <FwbTableCell class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="`/sheds/${shed.id}/edit`"
                                        class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="font-medium text-red-600 hover:underline dark:text-red-500"
                                        @click="confirmDelete(shed)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </FwbTableCell>
                        </FwbTableRow>
                        <FwbTableRow v-if="!sheds.data || sheds.data.length === 0">
                            <FwbTableCell
                                colspan="5"
                                class="px-6 py-16 text-center text-gray-500 dark:text-gray-400"
                            >
                                <div class="flex min-h-[280px] flex-col items-center justify-center gap-1">
                                    <span>No sheds yet.</span>
                                    <Link :href="`/farms/${farm.id}/sheds/create`" class="font-medium text-green-600 hover:underline dark:text-green-500">Add your first shed</Link>
                                </div>
                            </FwbTableCell>
                        </FwbTableRow>
                    </Table>
                    <template #pagination>
                        <Pagination :paginator="sheds" />
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import {
    FwbButton,
    FwbTableCell,
    FwbTableHeadCell,
    FwbTableRow,
} from 'flowbite-vue';
import { shedTypeLabel } from '../../constants/shedTypes';
import { useConfirmDelete } from '../../composables/useConfirmDelete';
import AppLayout from '../../layouts/AppLayout.vue';
import DataTable from '../../components/DataTable.vue';
import Pagination from '../../components/Pagination.vue';
import Table from '../../components/Table.vue';

const props = defineProps({
    farm: {
        type: Object,
        required: true,
    },
    sheds: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const searchQuery = ref(props.filters?.search ?? '');
const selectedIds = ref([]);

watch(
    () => props.filters?.search,
    (value) => { searchQuery.value = value ?? ''; },
    { immediate: true }
);

function submitSearch() {
    router.get(`/farms/${props.farm.id}/sheds`, { search: searchQuery.value || undefined }, { preserveState: false });
}

const { open: openConfirmDelete } = useConfirmDelete();

const pageIds = computed(() => (props.sheds.data || []).map((s) => s.id));
const isAllSelected = computed(
    () => pageIds.value.length > 0 && pageIds.value.every((id) => selectedIds.value.includes(id))
);
const isIndeterminate = computed(
    () => selectedIds.value.length > 0 && selectedIds.value.length < pageIds.value.length
);

function toggleSelectAll() {
    if (isAllSelected.value) {
        selectedIds.value = selectedIds.value.filter((id) => !pageIds.value.includes(id));
    } else {
        const merged = new Set([...selectedIds.value, ...pageIds.value]);
        selectedIds.value = [...merged];
    }
}

function toggleSelect(id) {
    const i = selectedIds.value.indexOf(id);
    if (i === -1) {
        selectedIds.value = [...selectedIds.value, id];
    } else {
        selectedIds.value = selectedIds.value.filter((_, index) => index !== i);
    }
}

function confirmDelete(shed) {
    openConfirmDelete({
        title: 'Delete shed',
        message: `Are you sure you want to delete <strong>${shed.name}</strong>? This can be undone later.`,
        onConfirm: () => router.delete(`/sheds/${shed.id}`, { preserveScroll: true }),
    });
}

function confirmBulkDelete() {
    const count = selectedIds.value.length;
    openConfirmDelete({
        title: 'Delete selected sheds',
        message: `Are you sure you want to delete <strong>${count}</strong> ${count === 1 ? 'shed' : 'sheds'}? This can be undone later.`,
        onConfirm: () => {
            router.post(`/farms/${props.farm.id}/sheds/bulk-destroy`, { ids: selectedIds.value }, { preserveScroll: true });
            selectedIds.value = [];
        },
    });
}
</script>
