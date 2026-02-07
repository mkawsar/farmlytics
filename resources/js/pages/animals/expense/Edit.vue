<template>
    <Head :title="`Edit expense – ${animal.animal_id}`" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    :href="`/animals/${animal.id}/expense`"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to expense
                </Link>
                <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Edit expense</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Update expense for {{ animal.animal_id }}.</p>

                <form class="mt-8 space-y-6 rounded-xl border border-stone-200/80 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800 sm:p-8" @submit.prevent="submit">
                    <div>
                        <label for="expense_type" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Type <span class="text-red-500">*</span></label>
                        <select
                            id="expense_type"
                            v-model="form.expense_type"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        >
                            <option v-for="(label, value) in EXPENSE_TYPES" :key="value" :value="value">{{ label }}</option>
                        </select>
                        <p v-if="form.errors.expense_type" class="mt-1.5 text-sm text-rose-600">{{ form.errors.expense_type }}</p>
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
                            <label for="quantity" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Quantity</label>
                            <input
                                id="quantity"
                                v-model="form.quantity"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                            />
                            <p v-if="form.errors.quantity" class="mt-1.5 text-sm text-rose-600">{{ form.errors.quantity }}</p>
                        </div>
                        <div>
                            <label for="unit_price" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Unit price (BDT)</label>
                            <input
                                id="unit_price"
                                v-model="form.unit_price"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                            />
                            <p v-if="form.errors.unit_price" class="mt-1.5 text-sm text-rose-600">{{ form.errors.unit_price }}</p>
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
                            {{ form.processing ? 'Saving…' : 'Update expense' }}
                        </button>
                        <Link
                            :href="`/animals/${animal.id}/expense`"
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
import { EXPENSE_TYPES } from '../../../constants/expenseTypes';
import AppLayout from '../../../layouts/AppLayout.vue';

const props = defineProps({
    animal: { type: Object, required: true },
    expenseTransaction: { type: Object, required: true },
});

const form = useForm({
    expense_type: props.expenseTransaction.expense_type,
    amount: props.expenseTransaction.amount,
    quantity: props.expenseTransaction.quantity ?? '',
    unit_price: props.expenseTransaction.unit_price ?? '',
    transaction_date: props.expenseTransaction.transaction_date ? new Date(props.expenseTransaction.transaction_date).toISOString().slice(0, 10) : '',
    notes: props.expenseTransaction.notes ?? '',
});

function submit() {
    form.put(`/animals/${props.animal.id}/expense/${props.expenseTransaction.id}`);
}
</script>
