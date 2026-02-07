<template>
    <Head :title="`Edit income – ${animal.animal_id}`" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    :href="`/animals/${animal.id}/income`"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to income
                </Link>
                <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Edit income</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Update income for {{ animal.animal_id }}.</p>

                <form class="mt-8 space-y-6 rounded-xl border border-stone-200/80 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800 sm:p-8" @submit.prevent="submit">
                    <div>
                        <label for="income_type" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Type <span class="text-red-500">*</span></label>
                        <select
                            id="income_type"
                            v-model="form.income_type"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        >
                            <option v-for="(label, value) in INCOME_TYPES" :key="value" :value="value">{{ label }}</option>
                        </select>
                        <p v-if="form.errors.income_type" class="mt-1.5 text-sm text-rose-600">{{ form.errors.income_type }}</p>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Amount (BDT) <span class="text-red-500">*</span></label>
                        <input
                            id="amount"
                            v-model="form.amount"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.amount" class="mt-1.5 text-sm text-rose-600">{{ form.errors.amount }}</p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="quantity_liter" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Quantity (L)</label>
                            <input
                                id="quantity_liter"
                                v-model="form.quantity_liter"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                            />
                            <p v-if="form.errors.quantity_liter" class="mt-1.5 text-sm text-rose-600">{{ form.errors.quantity_liter }}</p>
                        </div>
                        <div>
                            <label for="rate_per_liter" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Rate per liter (BDT)</label>
                            <input
                                id="rate_per_liter"
                                v-model="form.rate_per_liter"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                            />
                            <p v-if="form.errors.rate_per_liter" class="mt-1.5 text-sm text-rose-600">{{ form.errors.rate_per_liter }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="transaction_date" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Date <span class="text-red-500">*</span></label>
                        <input
                            id="transaction_date"
                            v-model="form.transaction_date"
                            type="date"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.transaction_date" class="mt-1.5 text-sm text-rose-600">{{ form.errors.transaction_date }}</p>
                    </div>

                    <div>
                        <label for="buyer" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Buyer</label>
                        <input
                            id="buyer"
                            v-model="form.buyer"
                            type="text"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.buyer" class="mt-1.5 text-sm text-rose-600">{{ form.errors.buyer }}</p>
                    </div>

                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Payment status</label>
                        <select
                            id="payment_status"
                            v-model="form.payment_status"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        >
                            <option v-for="(label, value) in PAYMENT_STATUSES" :key="value" :value="value">{{ label }}</option>
                        </select>
                        <p v-if="form.errors.payment_status" class="mt-1.5 text-sm text-rose-600">{{ form.errors.payment_status }}</p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Notes</label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            rows="2"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.notes" class="mt-1.5 text-sm text-rose-600">{{ form.errors.notes }}</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="cursor-pointer rounded-xl bg-[#2d5016] px-4 py-3 font-semibold text-white transition hover:bg-[#244012] focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 disabled:opacity-70"
                        >
                            {{ form.processing ? 'Saving…' : 'Update income' }}
                        </button>
                        <Link
                            :href="`/animals/${animal.id}/income`"
                            class="inline-flex items-center rounded-xl border border-stone-300 bg-white px-4 py-3 font-semibold text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { INCOME_TYPES } from '../../../constants/incomeTypes';
import { PAYMENT_STATUSES } from '../../../constants/paymentStatuses';
import AppLayout from '../../../layouts/AppLayout.vue';

const props = defineProps({
    animal: { type: Object, required: true },
    incomeTransaction: { type: Object, required: true },
});

const form = useForm({
    income_type: props.incomeTransaction.income_type,
    amount: props.incomeTransaction.amount,
    quantity_liter: props.incomeTransaction.quantity_liter ?? '',
    rate_per_liter: props.incomeTransaction.rate_per_liter ?? '',
    transaction_date: props.incomeTransaction.transaction_date ? new Date(props.incomeTransaction.transaction_date).toISOString().slice(0, 10) : '',
    buyer: props.incomeTransaction.buyer ?? '',
    payment_status: props.incomeTransaction.payment_status ?? 'paid',
    notes: props.incomeTransaction.notes ?? '',
});

function submit() {
    form.put(`/animals/${props.animal.id}/income/${props.incomeTransaction.id}`);
}
</script>
