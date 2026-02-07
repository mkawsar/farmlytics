<template>
    <Head title="Add animal – Select shed" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    href="/animals"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to animals
                </Link>
                <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Add animal</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Select a shed to add the animal to.</p>

                <div class="mt-8 rounded-xl border border-stone-200/80 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <Table>
                        <template #head>
                            <FwbTableHeadCell>Farm</FwbTableHeadCell>
                            <FwbTableHeadCell>Shed</FwbTableHeadCell>
                            <FwbTableHeadCell>
                                <span class="sr-only">Action</span>
                            </FwbTableHeadCell>
                        </template>
                        <FwbTableRow v-for="shed in sheds" :key="shed.id">
                            <FwbTableCell class="font-medium text-gray-900 dark:text-white">
                                <template v-if="shed.farm">
                                    <Link
                                        :href="`/farms/${shed.farm.id}`"
                                        class="text-green-600 hover:underline dark:text-green-500"
                                    >
                                        {{ shed.farm.name }}
                                    </Link>
                                </template>
                                <template v-else>—</template>
                            </FwbTableCell>
                            <FwbTableCell>{{ shed.name }}</FwbTableCell>
                            <FwbTableCell class="text-right">
                                <Link
                                    v-if="shed.farm"
                                    :href="`/farms/${shed.farm.id}/sheds/${shed.id}/animals/create`"
                                    class="inline-flex items-center rounded-lg bg-[#2d5016] px-3 py-2 text-sm font-medium text-white transition hover:bg-[#244012]"
                                >
                                    Add animal here
                                </Link>
                            </FwbTableCell>
                        </FwbTableRow>
                        <FwbTableRow v-if="!sheds || sheds.length === 0">
                            <FwbTableCell
                                colspan="3"
                                class="px-6 py-16 text-center text-gray-500 dark:text-gray-400"
                            >
                                <div class="flex min-h-[200px] flex-col items-center justify-center gap-1">
                                    <span>No sheds yet.</span>
                                    <Link href="/farms" class="font-medium text-green-600 hover:underline dark:text-green-500">Create a farm and shed first</Link>
                                </div>
                            </FwbTableCell>
                        </FwbTableRow>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { FwbTableCell, FwbTableHeadCell, FwbTableRow } from 'flowbite-vue';
import AppLayout from '../../layouts/AppLayout.vue';
import Table from '../../components/Table.vue';

defineProps({
    sheds: {
        type: Array,
        default: () => [],
    },
});
</script>
