<template>
    <Head :title="animal.animal_id" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <Link
                            v-if="animal.shed"
                            :href="`/farms/${animal.shed.farm?.id}/sheds/${animal.shed.id}/animals`"
                            class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                        >
                            ← Back to animals
                        </Link>
                        <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">{{ animal.animal_id }}</h1>
                        <p class="mt-1 text-stone-500 dark:text-stone-400">
                            Animal details
                            <template v-if="animal.shed">
                                · <Link :href="`/sheds/${animal.shed.id}`" class="text-green-600 hover:underline dark:text-green-500">{{ animal.shed.name }}</Link>
                            </template>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="`/animals/${animal.id}/edit`"
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
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Animal ID</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ animal.animal_id }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Breed</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ animal.breed }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Gender</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ genderLabel(animal.gender) }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Status</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ statusLabel(animal.status) }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Group</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ groupLabel(animal.grouping) }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Date of birth</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ formatDate(animal.date_of_birth) }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Purchase date</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ formatDate(animal.purchase_date) }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Purchase price (BDT)</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ animal.purchase_price != null ? formatMoney(animal.purchase_price) : '—' }}</dd>
                        </div>
                        <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Current weight</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">{{ animal.current_weight != null ? `${animal.current_weight} kg` : '—' }}</dd>
                        </div>
                        <div v-if="animal.shed" class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Shed</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">
                                <Link :href="`/sheds/${animal.shed.id}`" class="text-green-600 hover:underline dark:text-green-500">{{ animal.shed.name }}</Link>
                            </dd>
                        </div>
                        <div v-if="animal.shed?.farm" class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-stone-500 dark:text-stone-400">Farm</dt>
                            <dd class="mt-1 text-sm text-stone-900 dark:text-stone-100 sm:col-span-2 sm:mt-0">
                                <Link :href="`/farms/${animal.shed.farm.id}`" class="text-green-600 hover:underline dark:text-green-500">{{ animal.shed.farm.name }}</Link>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Finance: Profit & Loss per cow -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-stone-900 dark:text-stone-100">Profit / Loss for this cow</h2>
                    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                        Profit = (Income + Dung share) − (Labour share + Feed + Purchase). Labour = total labour / total cows; Dung = total dung sell / total cows.
                    </p>
                    <div v-if="animal.purchase_price != null" class="mt-3 flex items-center gap-2 rounded-lg border border-stone-200/80 bg-stone-50 px-4 py-2.5 dark:border-stone-700 dark:bg-stone-800/50">
                        <span class="text-sm font-medium text-stone-600 dark:text-stone-300">Purchase price (this cow)</span>
                        <span class="text-sm font-semibold text-stone-900 dark:text-stone-100">{{ formatMoney(animal.purchase_price) }}</span>
                    </div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl border border-stone-200/80 bg-white p-4 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                            <h3 class="text-sm font-medium text-stone-500 dark:text-stone-400">Last 12 months</h3>
                            <dl class="mt-2 space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Income (milk, etc.)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossYear.income) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Dung sell (allocated)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossYear.dung_allocated) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                    <dt class="text-stone-500 dark:text-stone-400">Labour (total / cows)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossYear.labour_allocated) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Feed</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossYear.feed_expense) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Purchase (cow)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossYear.purchase_expense) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                    <dt class="text-stone-500 dark:text-stone-400">Total income</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossYear.total_income) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Total expense</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossYear.total_expense) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                    <dt class="font-medium text-stone-700 dark:text-stone-300">Profit / Loss</dt>
                                    <dd :class="profitLossYear.profit_loss >= 0 ? 'font-semibold text-green-600 dark:text-green-500' : 'font-semibold text-red-600 dark:text-red-500'">
                                        {{ formatMoney(profitLossYear.profit_loss) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div class="rounded-xl border border-stone-200/80 bg-white p-4 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                            <h3 class="text-sm font-medium text-stone-500 dark:text-stone-400">Lifetime</h3>
                            <dl class="mt-2 space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Income (milk, etc.)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossLifetime.income) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Dung sell (allocated)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossLifetime.dung_allocated) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                    <dt class="text-stone-500 dark:text-stone-400">Labour (total / cows)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossLifetime.labour_allocated) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Feed</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossLifetime.feed_expense) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Purchase (cow)</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossLifetime.purchase_expense) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                    <dt class="text-stone-500 dark:text-stone-400">Total income</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossLifetime.total_income) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-stone-500 dark:text-stone-400">Total expense</dt>
                                    <dd class="font-medium text-stone-900 dark:text-stone-100">{{ formatMoney(profitLossLifetime.total_expense) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-stone-200 pt-2 dark:border-stone-700">
                                    <dt class="font-medium text-stone-700 dark:text-stone-300">Profit / Loss</dt>
                                    <dd :class="profitLossLifetime.profit_loss >= 0 ? 'font-semibold text-green-600 dark:text-green-500' : 'font-semibold text-red-600 dark:text-red-500'">
                                        {{ formatMoney(profitLossLifetime.profit_loss) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <Link
                            :href="`/animals/${animal.id}/income`"
                            class="inline-flex items-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                        >
                            Income
                        </Link>
                        <Link
                            :href="`/animals/${animal.id}/expense`"
                            class="inline-flex items-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                        >
                            Expense
                        </Link>
                        <Link
                            :href="`/animals/${animal.id}/milk`"
                            class="inline-flex items-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                        >
                            Milk records
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { genderLabel } from '../../constants/genders';
import { statusLabel } from '../../constants/statuses';
import { groupLabel } from '../../constants/groups';
import { useConfirmDelete } from '../../composables/useConfirmDelete';
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    animal: {
        type: Object,
        required: true,
    },
    profitLossYear: {
        type: Object,
        default: () => ({
            total_income: 0,
            total_direct_expense: 0,
            allocated_expense: 0,
            purchase_expense: 0,
            total_expense: 0,
            profit_loss: 0,
        }),
    },
    profitLossLifetime: {
        type: Object,
        default: () => ({
            total_income: 0,
            total_direct_expense: 0,
            allocated_expense: 0,
            purchase_expense: 0,
            total_expense: 0,
            profit_loss: 0,
        }),
    },
});

function formatDate(value) {
    if (!value) return '—';
    const d = typeof value === 'string' ? new Date(value) : value;
    return d.toLocaleDateString();
}

function formatMoney(value) {
    if (value == null) return '—';
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'BDT' }).format(Number(value));
}

const { open: openConfirmDelete } = useConfirmDelete();

function confirmDelete() {
    openConfirmDelete({
        title: 'Delete animal',
        message: `Are you sure you want to delete <strong>${props.animal.animal_id}</strong>? This can be undone later.`,
        onConfirm: () => {
            router.delete(`/animals/${props.animal.id}`);
        },
    });
}
</script>
