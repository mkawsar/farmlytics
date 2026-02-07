<template>
    <Head title="Expense" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Expense</h1>
                        <p class="mt-1 text-stone-500 dark:text-stone-400">All expense transactions. Add expense for any animal from here.</p>
                    </div>
                    <Link
                        href="/expense/select-animal"
                        class="inline-flex cursor-pointer items-center justify-center rounded-xl bg-[#2d5016] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#244012]"
                    >
                        Add expense
                    </Link>
                </div>

                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200/80 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                        <thead class="bg-stone-50 dark:bg-stone-800/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Date</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Animal</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Type</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Amount</th>
                                <th scope="col" class="relative px-4 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                            <tr v-for="item in expense.data" :key="item.id" class="bg-white dark:bg-stone-800">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-stone-900 dark:text-stone-100">{{ formatDate(item.transaction_date) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <Link
                                        v-if="item.animal"
                                        :href="`/animals/${item.animal.id}`"
                                        class="font-medium text-[#2d5016] hover:underline dark:text-emerald-400"
                                    >
                                        {{ item.animal.animal_id }}
                                    </Link>
                                    <span v-else class="text-stone-500 dark:text-stone-400">—</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-stone-600 dark:text-stone-300">{{ expenseTypeLabel(item.expense_type) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(item.amount) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                    <Link
                                        v-if="item.animal"
                                        :href="`/animals/${item.animal.id}/expense/${item.id}/edit`"
                                        class="font-medium text-[#2d5016] hover:underline dark:text-emerald-400"
                                    >
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!expense.data || expense.data.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-stone-500 dark:text-stone-400">No expense recorded yet. Click “Add expense” to get started.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="expense.links && expense.links.length > 3" class="border-t border-stone-200 px-4 py-2 dark:border-stone-700">
                        <Pagination :paginator="expense" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { expenseTypeLabel } from '../../constants/expenseTypes';
import AppLayout from '../../layouts/AppLayout.vue';
import Pagination from '../../components/Pagination.vue';

defineProps({
    expense: { type: Object, required: true },
});

function formatDate(value) {
    if (!value) return '—';
    return new Date(value).toLocaleDateString();
}

function formatMoney(value) {
    if (value == null) return '—';
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'BDT' }).format(Number(value));
}
</script>
