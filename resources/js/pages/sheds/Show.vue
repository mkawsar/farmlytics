<template>
    <Head :title="shed.name" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Link
                            v-if="shed.farm"
                            :href="`/farms/${shed.farm.id}/sheds`"
                            class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                        >
                            ← Back to sheds
                        </Link>
                        <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">{{ shed.name }}</h1>
                        <p class="mt-1 text-stone-500 dark:text-stone-400">
                            Shed details
                            <template v-if="shed.farm">
                                · <Link :href="`/farms/${shed.farm.id}`" class="text-green-600 hover:underline dark:text-green-500">{{ shed.farm.name }}</Link>
                            </template>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="`/sheds/${shed.id}/edit`"
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
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ shed.name }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Type</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ shedTypeLabel(shed.type) }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Capacity</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ shed.capacity != null ? shed.capacity : '—' }}</dd>
                        </div>
                        <div v-if="shed.farm" class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Farm</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">
                                <Link :href="`/farms/${shed.farm.id}`" class="text-green-600 hover:underline dark:text-green-500">{{ shed.farm.name }}</Link>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { shedTypeLabel } from '../../constants/shedTypes';
import { useConfirmDelete } from '../../composables/useConfirmDelete';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    shed: {
        type: Object,
        required: true,
    },
});

const { open: openConfirmDelete } = useConfirmDelete();

function confirmDelete() {
    openConfirmDelete({
        title: 'Delete shed',
        message: `Are you sure you want to delete <strong>${props.shed.name}</strong>? This can be undone later.`,
        onConfirm: () => {
            router.delete(`/sheds/${props.shed.id}`);
        },
    });
}
</script>
