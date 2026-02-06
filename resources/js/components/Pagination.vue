<template>
    <div
        v-if="show"
        class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-200 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800"
    >
        <p class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-900 dark:text-stone-100">{{ paginator.from }}–{{ paginator.to }}</span>
            of <span class="font-semibold text-gray-900 dark:text-stone-100">{{ paginator.total }}</span>
        </p>
        <div class="flex gap-1">
            <Link
                v-for="link in paginator.links"
                :key="link.label"
                :href="link.url"
                :class="[
                    'inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-lg border px-3 text-sm font-medium transition',
                    link.active
                        ? 'border-green-500 bg-green-50 text-green-600 dark:border-green-500 dark:bg-green-900/20 dark:text-green-500'
                        : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white',
                    !link.url ? 'pointer-events-none cursor-not-allowed opacity-50' : ''
                ]"
                v-html="link.label"
            />
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    /** Laravel paginator object: { links, from, to, total, last_page } */
    paginator: {
        type: Object,
        required: true,
    },
});

const show = computed(() => (props.paginator.last_page ?? 0) > 1);
</script>
