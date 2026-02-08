<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Dashboard</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Income and expense at a glance.</p>

                <!-- Income & Expense summary: Today | This month | Total -->
                <div class="mt-8 grid gap-6 sm:grid-cols-3">
                    <div class="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <h2 class="text-sm font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Today</h2>
                        <dl class="mt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <dt class="text-stone-600 dark:text-stone-300">Income</dt>
                                <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(totals.today_income) }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-stone-600 dark:text-stone-300">Expense</dt>
                                <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(totals.today_expense) }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                <dt class="font-medium text-stone-700 dark:text-stone-300">Profit</dt>
                                <dd :class="totals.today_profit >= 0 ? 'font-semibold text-green-600 dark:text-green-500' : 'font-semibold text-red-600 dark:text-red-500'">
                                    {{ formatMoney(totals.today_profit) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div class="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <h2 class="text-sm font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">This month</h2>
                        <dl class="mt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <dt class="text-stone-600 dark:text-stone-300">Income</dt>
                                <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(totals.month_income) }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-stone-600 dark:text-stone-300">Expense</dt>
                                <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(totals.month_expense) }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                <dt class="font-medium text-stone-700 dark:text-stone-300">Profit</dt>
                                <dd :class="totals.month_profit >= 0 ? 'font-semibold text-green-600 dark:text-green-500' : 'font-semibold text-red-600 dark:text-red-500'">
                                    {{ formatMoney(totals.month_profit) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div class="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <h2 class="text-sm font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Total (all time)</h2>
                        <dl class="mt-3 space-y-2">
                            <div class="flex justify-between text-sm">
                                <dt class="text-stone-600 dark:text-stone-300">Income</dt>
                                <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(totals.total_income) }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-stone-600 dark:text-stone-300">Expense</dt>
                                <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(totals.total_expense) }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                <dt class="font-medium text-stone-700 dark:text-stone-300">Profit</dt>
                                <dd :class="totals.total_profit >= 0 ? 'font-semibold text-green-600 dark:text-green-500' : 'font-semibold text-red-600 dark:text-red-500'">
                                    {{ formatMoney(totals.total_profit) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Day-wise income & expense with calendar and sorting -->
                <div class="mt-10">
                    <h2 class="text-lg font-semibold text-stone-900 dark:text-stone-100">Day-wise income & expense</h2>
                    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Pick a month and sort by date.</p>
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <label class="flex items-center gap-2 text-sm font-medium text-stone-700 dark:text-stone-300">
                            <span>Month</span>
                            <input
                                v-model="monthValue"
                                type="month"
                                class="rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-1 focus:ring-stone-500 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                                @change="onMonthChange"
                            />
                        </label>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-stone-200/80 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                            <thead class="bg-stone-50 dark:bg-stone-800/80">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">
                                        <button type="button" class="inline-flex items-center gap-1 hover:text-stone-700 dark:hover:text-stone-200" @click="toggleDateSort">
                                            Date
                                            <span v-if="dateSort === 'asc'" class="text-stone-400">↑</span>
                                            <span v-else-if="dateSort === 'desc'" class="text-stone-400">↓</span>
                                            <span v-else class="text-stone-300">↕</span>
                                        </button>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Income</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Expense</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                                <tr v-for="row in sortedDayWise" :key="row.date" class="hover:bg-stone-50/80 dark:hover:bg-stone-700/30">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-stone-700 dark:text-stone-300">{{ formatDate(row.date) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(row.income) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(row.expense) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium" :class="row.profit >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500'">
                                        {{ formatMoney(row.profit) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <Link
                        href="/income"
                        class="inline-flex items-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                    >
                        View income
                    </Link>
                    <Link
                        href="/expense"
                        class="inline-flex items-center rounded-xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                    >
                        View expense
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    totals: {
        type: Object,
        default: () => ({
            today_income: 0,
            today_expense: 0,
            today_profit: 0,
            month_income: 0,
            month_expense: 0,
            month_profit: 0,
            total_income: 0,
            total_expense: 0,
            total_profit: 0,
        }),
    },
    dayWise: {
        type: Array,
        default: () => [],
    },
    selectedMonth: {
        type: String,
        default: () => new Date().toISOString().slice(0, 7),
    },
});

const monthValue = ref(props.selectedMonth);
const dateSort = ref('desc');

watch(() => props.selectedMonth, (v) => { monthValue.value = v; });

const sortedDayWise = computed(() => {
    const list = [...props.dayWise];
    if (dateSort.value === 'asc') {
        list.sort((a, b) => (a.date < b.date ? -1 : a.date > b.date ? 1 : 0));
    } else if (dateSort.value === 'desc') {
        list.sort((a, b) => (a.date > b.date ? -1 : a.date < b.date ? 1 : 0));
    }
    return list;
});

function toggleDateSort() {
    dateSort.value = dateSort.value === 'desc' ? 'asc' : 'desc';
}

function onMonthChange() {
    const month = monthValue.value;
    if (!month) return;
    router.get('/', { month }, { preserveState: true });
}

function formatMoney(value) {
    if (value == null) return '৳0';
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'BDT' }).format(Number(value));
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>
