<template>
    <Head title="Add animal" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    :href="`/farms/${farm.id}/sheds/${shed.id}/animals`"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to animals
                </Link>
                <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Add animal</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Register a new animal in {{ shed.name }}.</p>

                <form class="mt-8 space-y-6 rounded-xl border border-stone-200/80 bg-white p-6 shadow-sm dark:border-stone-700 dark:bg-stone-800 sm:p-8" @submit.prevent="submit">
                    <div>
                        <label for="animal_id" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Animal ID (QR / RFID) <span class="text-red-500">*</span></label>
                        <input
                            id="animal_id"
                            v-model="form.animal_id"
                            type="text"
                            required
                            autocomplete="off"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500"
                            placeholder="e.g. RFID-001"
                        />
                        <p v-if="form.errors.animal_id" class="mt-1.5 text-sm text-rose-600">{{ form.errors.animal_id }}</p>
                    </div>

                    <div>
                        <label for="breed" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Breed <span class="text-red-500">*</span></label>
                        <input
                            id="breed"
                            v-model="form.breed"
                            type="text"
                            required
                            autocomplete="off"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500"
                            placeholder="e.g. Holstein"
                        />
                        <p v-if="form.errors.breed" class="mt-1.5 text-sm text-rose-600">{{ form.errors.breed }}</p>
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Gender <span class="text-red-500">*</span></label>
                        <select
                            id="gender"
                            v-model="form.gender"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        >
                            <option value="" disabled>Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <p v-if="form.errors.gender" class="mt-1.5 text-sm text-rose-600">{{ form.errors.gender }}</p>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Status <span class="text-red-500">*</span></label>
                        <select
                            id="status"
                            v-model="form.status"
                            required
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        >
                            <option value="" disabled>Select status</option>
                            <option value="active">Active</option>
                            <option value="sold">Sold</option>
                            <option value="dead">Dead</option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1.5 text-sm text-rose-600">{{ form.errors.status }}</p>
                    </div>

                    <div>
                        <label for="grouping" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Group</label>
                        <select
                            id="grouping"
                            v-model="form.grouping"
                            class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                        >
                            <option value="">—</option>
                            <option value="lactating">Lactating</option>
                            <option value="dry">Dry</option>
                            <option value="pregnant">Pregnant</option>
                            <option value="fattening">Fattening</option>
                            <option value="calf">Calf</option>
                        </select>
                        <p v-if="form.errors.grouping" class="mt-1.5 text-sm text-rose-600">{{ form.errors.grouping }}</p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Date of birth</label>
                            <input
                                id="date_of_birth"
                                v-model="form.date_of_birth"
                                type="date"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                            />
                            <p v-if="form.errors.date_of_birth" class="mt-1.5 text-sm text-rose-600">{{ form.errors.date_of_birth }}</p>
                        </div>
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Purchase date</label>
                            <input
                                id="purchase_date"
                                v-model="form.purchase_date"
                                type="date"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100"
                            />
                            <p v-if="form.errors.purchase_date" class="mt-1.5 text-sm text-rose-600">{{ form.errors.purchase_date }}</p>
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="purchase_price" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Purchase price (BDT)</label>
                            <input
                                id="purchase_price"
                                v-model="form.purchase_price"
                                type="number"
                                min="0"
                                step="0.01"
                                autocomplete="off"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500"
                                placeholder="0.00"
                            />
                            <p v-if="form.errors.purchase_price" class="mt-1.5 text-sm text-rose-600">{{ form.errors.purchase_price }}</p>
                        </div>
                        <div>
                            <label for="current_weight" class="block text-sm font-medium text-stone-700 dark:text-stone-300">Current weight (kg)</label>
                            <input
                                id="current_weight"
                                v-model="form.current_weight"
                                type="number"
                                min="0"
                                step="0.01"
                                autocomplete="off"
                                class="mt-1.5 w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-500"
                                placeholder="kg"
                            />
                            <p v-if="form.errors.current_weight" class="mt-1.5 text-sm text-rose-600">{{ form.errors.current_weight }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="cursor-pointer rounded-xl bg-[#2d5016] px-4 py-3 font-semibold text-white shadow-lg shadow-[#2d5016]/25 transition hover:bg-[#244012] focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Creating…' : 'Create animal' }}
                        </button>
                        <Link
                            :href="`/farms/${farm.id}/sheds/${shed.id}/animals`"
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
    shed: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    animal_id: '',
    breed: '',
    gender: '',
    status: 'active',
    grouping: '',
    date_of_birth: '',
    purchase_date: '',
    purchase_price: '',
    current_weight: '',
});

function submit() {
    form.post(`/farms/${props.farm.id}/sheds/${props.shed.id}/animals`);
}
</script>
