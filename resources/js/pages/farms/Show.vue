<template>
    <Head :title="farm.name" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
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

                <div class="mt-8 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-stone-900 dark:text-stone-100">Sheds</h2>
                    <Link
                        :href="`/farms/${farm.id}/sheds/create`"
                        class="inline-flex items-center gap-2 rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                    >
                        Add shed
                    </Link>
                </div>
                <div class="mt-4 rounded-xl border border-stone-200/80 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <Link
                        :href="`/farms/${farm.id}/sheds`"
                        class="block px-4 py-3 text-sm font-medium text-green-600 hover:bg-stone-50 dark:hover:bg-stone-700/50 dark:text-green-500 sm:px-6"
                    >
                        Manage sheds ({{ farm.sheds ? farm.sheds.length : 0 }})
                    </Link>
                    <ul v-if="farm.sheds && farm.sheds.length > 0" class="divide-y divide-stone-200 dark:divide-stone-700">
                        <li v-for="shed in farm.sheds" :key="shed.id">
                            <Link
                                :href="`/sheds/${shed.id}`"
                                class="flex items-center justify-between px-4 py-3 text-sm text-stone-700 transition hover:bg-stone-50 dark:text-stone-300 dark:hover:bg-stone-700/50 sm:px-6"
                            >
                                <span class="font-medium">{{ shed.name }}</span>
                                <span class="text-stone-500 dark:text-stone-400">{{ shedTypeLabel(shed.type) }} · {{ shed.capacity != null ? shed.capacity : '—' }} capacity</span>
                            </Link>
                        </li>
                    </ul>
                    <p v-else class="px-4 py-6 text-center text-sm text-stone-500 dark:text-stone-400 sm:px-6">
                        No sheds yet. <Link :href="`/farms/${farm.id}/sheds/create`" class="font-medium text-green-600 hover:underline dark:text-green-500">Add a shed</Link>
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { farmTypeLabel } from '../../constants/farmTypes';
import { shedTypeLabel } from '../../constants/shedTypes';
import { useConfirmDelete } from '../../composables/useConfirmDelete';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    farm: {
        type: Object,
        required: true,
    },
});

const { open: openConfirmDelete } = useConfirmDelete();

function confirmDelete() {
    openConfirmDelete({
        title: 'Delete farm',
        message: `Are you sure you want to delete <strong>${props.farm.name}</strong>? This can be undone later.`,
        onConfirm: () => {
            router.delete(`/farms/${props.farm.id}`);
        },
    });
}
</script>
