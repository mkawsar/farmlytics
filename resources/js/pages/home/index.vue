<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="max-w-5xl">
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
import { Head, Link } from '@inertiajs/vue3';
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
});

function formatMoney(value) {
    if (value == null) return '৳0';
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'BDT' }).format(Number(value));
}
</script>
