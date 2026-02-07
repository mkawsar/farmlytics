<template>
    <Head :title="`Add milk record – ${animal.animal_id}`" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    :href="`/animals/${animal.id}/milk`"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to milk records
                </Link>
                <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Add milk record</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Record milk production for {{ animal.animal_id }}.</p>

                <form class="mt-8 space-y-6 rounded-xl border border-stone-200/80 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800 sm:p-8" @submit.prevent="submit">
                    <div>
                        <label for="record_date" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Date <span class="text-red-500">*</span></label>
                        <input
                            id="record_date"
                            v-model="form.record_date"
                            type="date"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.record_date" class="mt-1.5 text-sm text-rose-600">{{ form.errors.record_date }}</p>
                    </div>

                    <div>
                        <label for="quantity_liter" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Quantity (liters) <span class="text-red-500">*</span></label>
                        <input
                            id="quantity_liter"
                            v-model="form.quantity_liter"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.quantity_liter" class="mt-1.5 text-sm text-rose-600">{{ form.errors.quantity_liter }}</p>
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
                            {{ form.processing ? 'Saving…' : 'Save record' }}
                        </button>
                        <Link
                            :href="`/animals/${animal.id}/milk`"
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
import AppLayout from '../../../layouts/AppLayout.vue';

const props = defineProps({
    animal: { type: Object, required: true },
});

const form = useForm({
    record_date: new Date().toISOString().slice(0, 10),
    quantity_liter: '',
    notes: '',
});

function submit() {
    form.post(`/animals/${props.animal.id}/milk`);
}
</script>
