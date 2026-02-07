<template>
    <Head :title="type === 'income' ? 'Add income – Select animal' : 'Add expense – Select animal'" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="w-full">
                <Link
                    :href="type === 'income' ? '/income' : '/expense'"
                    class="text-sm font-medium text-stone-500 transition hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-300"
                >
                    ← Back to {{ type === 'income' ? 'income' : 'expense' }}
                </Link>
                <h1 class="mt-2 text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">
                    {{ type === 'income' ? 'Add income' : 'Add expense' }}
                </h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Select the animal to record {{ type === 'income' ? 'income' : 'expense' }} for.</p>

                <form
                    class="mt-6 flex gap-2"
                    @submit.prevent="submitSearch"
                >
                    <input
                        v-model="searchQuery"
                        type="search"
                        name="search"
                        :placeholder="'Search by ID or breed…'"
                        class="block w-full max-w-sm rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm text-stone-900 placeholder-stone-500 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:placeholder-stone-400"
                        aria-label="Search animals"
                    />
                    <button
                        type="submit"
                        class="rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                    >
                        Search
                    </button>
                    <Link
                        v-if="filters.search"
                        :href="type === 'income' ? '/income/select-animal' : '/expense/select-animal'"
                        class="rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-300 dark:hover:bg-stone-700"
                    >
                        Clear
                    </Link>
                </form>

                <div class="mt-6 overflow-hidden rounded-xl border border-stone-200/80 bg-white shadow-sm dark:border-stone-700 dark:bg-stone-800">
                    <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                        <thead class="bg-stone-50 dark:bg-stone-800/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Animal ID</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Breed</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500 dark:text-stone-400">Shed</th>
                                <th scope="col" class="relative px-4 py-3"><span class="sr-only">Action</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                            <tr v-for="animal in animals.data" :key="animal.id" class="bg-white dark:bg-stone-800">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-stone-900 dark:text-stone-100">
                                    <Link :href="`/animals/${animal.id}`" class="text-[#2d5016] hover:underline dark:text-emerald-400">{{ animal.animal_id }}</Link>
                                </td>
                                <td class="px-4 py-3 text-sm text-stone-600 dark:text-stone-300">{{ animal.breed }}</td>
                                <td class="px-4 py-3 text-sm text-stone-600 dark:text-stone-300">
                                    <template v-if="animal.shed">{{ animal.shed.name }}</template>
                                    <span v-else class="text-stone-400">—</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                    <Link
                                        :href="type === 'income' ? `/animals/${animal.id}/income/create` : `/animals/${animal.id}/expense/create`"
                                        class="inline-flex items-center rounded-lg bg-[#2d5016] px-3 py-2 text-sm font-medium text-white transition hover:bg-[#244012]"
                                    >
                                        {{ type === 'income' ? 'Add income here' : 'Add expense here' }}
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!animals.data || animals.data.length === 0">
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-stone-500 dark:text-stone-400">No animals found. Add animals first from Animals.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="animals.links && animals.links.length > 3" class="border-t border-stone-200 px-4 py-2 dark:border-stone-700">
                        <Pagination :paginator="animals" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
import Pagination from '../../components/Pagination.vue';

const props = defineProps({
    animals: { type: Object, required: true },
    type: { type: String, required: true }, // 'income' | 'expense'
    filters: { type: Object, default: () => ({ search: '' }) },
});

const searchQuery = ref(props.filters.search ?? '');

function submitSearch() {
    const url = props.type === 'income' ? '/income/select-animal' : '/expense/select-animal';
    router.get(url, { search: searchQuery.value || undefined }, { preserveState: true });
}
</script>
