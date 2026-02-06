<template>
    <Head :title="`Edit ${farm.name}`" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    :href="`/farms/${farm.id}`"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to farm
                </Link>
                <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Edit farm</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Update {{ farm.name }}.</p>

                <form class="mt-8 space-y-6 rounded-xl border border-stone-200/80 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800 sm:p-8" @submit.prevent="submit">
                    <div>
                        <label for="name" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Name <span class="text-red-500">*</span></label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            autocomplete="off"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500"
                            placeholder="e.g. North Field"
                        />
                        <p v-if="form.errors.name" class="mt-1.5 text-sm text-rose-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Location</label>
                        <input
                            id="location"
                            v-model="form.location"
                            type="text"
                            autocomplete="off"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500"
                            placeholder="e.g. County Road 12"
                        />
                        <p v-if="form.errors.location" class="mt-1.5 text-sm text-rose-600">{{ form.errors.location }}</p>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Type <span class="text-red-500">*</span></label>
                        <select
                            id="type"
                            v-model="form.type"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        >
                            <option value="dairy">Dairy</option>
                            <option value="fattening">Fattening</option>
                            <option value="mixed">Mixed</option>
                        </select>
                        <p v-if="form.errors.type" class="mt-1.5 text-sm text-rose-600">{{ form.errors.type }}</p>
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Capacity</label>
                        <input
                            id="capacity"
                            v-model.number="form.capacity"
                            type="number"
                            min="0"
                            step="1"
                            autocomplete="off"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500"
                            placeholder="Total animal capacity"
                        />
                        <p v-if="form.errors.capacity" class="mt-1.5 text-sm text-rose-600">{{ form.errors.capacity }}</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="cursor-pointer rounded-xl bg-[#2d5016] px-4 py-3 font-semibold text-white shadow-lg shadow-[#2d5016]/25 transition hover:bg-[#244012] focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Saving…' : 'Save changes' }}
                        </button>
                        <Link
                            :href="`/farms/${farm.id}`"
                            class="inline-flex cursor-pointer items-center rounded-xl border border-stone-300 bg-white px-4 py-3 font-semibold text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
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
import AppLayout from '../../layouts/AppLayout.vue';

const props = defineProps({
    farm: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.farm.name,
    location: props.farm.location ?? '',
    type: props.farm.type ?? 'mixed',
    capacity: props.farm.capacity ?? null,
});

function submit() {
    form.put(`/farms/${props.farm.id}`);
}
</script>
