<template>
    <Head :title="`Income – ${animal.animal_id}`" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    :href="`/animals/${animal.id}`"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to {{ animal.animal_id }}
                </Link>
                <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Income</h1>
                        <p class="mt-1 text-stone-500 dark:text-stone-400">Income transactions for {{ animal.animal_id }}.</p>
                    </div>
                    <Link
                        :href="`/animals/${animal.id}/income/create`"
                        class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-[#2d5016] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#244012]"
                    >
                        Add income
                    </Link>
                </div>

                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200/80 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                        <thead class="bg-stone-50 dark:bg-stone-800/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Date</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Type</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Amount</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Payment</th>
                                <th scope="col" class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                            <tr v-for="item in income.data" :key="item.id" class="bg-white dark:bg-stone-800">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-stone-900 dark:text-stone-100">{{ formatDate(item.transaction_date) }}</td>
                                <td class="px-4 py-3 text-sm text-stone-600 dark:text-stone-300">{{ incomeTypeLabel(item.income_type) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(item.amount) }}</td>
                                <td class="px-4 py-3 text-sm text-stone-600 dark:text-stone-300">{{ paymentStatusLabel(item.payment_status) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                    <Link
                                        :href="`/animals/${animal.id}/income/${item.id}/edit`"
                                        class="font-medium text-[#2d5016] hover:underline dark:text-emerald-400"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        class="ml-3 font-medium text-red-600 hover:underline dark:text-red-400"
                                        @click="confirmDelete(item)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!income.data || income.data.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-stone-500 dark:text-stone-400">No income recorded yet.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="income.links && income.links.length > 3" class="border-t border-stone-200 px-4 py-2 dark:border-stone-700">
                        <Pagination :links="income.links" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { incomeTypeLabel } from '../../../constants/incomeTypes';
import { paymentStatusLabel } from '../../../constants/paymentStatuses';
import { useConfirmDelete } from '../../../composables/useConfirmDelete';
import AppLayout from '../../../layouts/AppLayout.vue';
import Pagination from '../../../components/Pagination.vue';

const props = defineProps({
    animal: { type: Object, required: true },
    income: { type: Object, required: true },
});

function formatDate(value) {
    if (!value) return '—';
    return new Date(value).toLocaleDateString();
}

function formatMoney(value) {
    if (value == null) return '—';
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'BDT' }).format(Number(value));
}

const { open: openConfirmDelete } = useConfirmDelete();

function confirmDelete(item) {
    openConfirmDelete({
        title: 'Delete income',
        message: `Delete this income of ${formatMoney(item.amount)}?`,
        onConfirm: () => {
            router.delete(`/animals/${props.animal.id}/income/${item.id}`);
        },
    });
}
</script>
