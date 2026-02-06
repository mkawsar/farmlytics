<template>
    <div
        v-if="show"
        class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-200 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800"
    >
        <p class="text-sm text-gray-700 dark:text-gray-400">
            <template v-if="paginator.total === 0">
                No records
            </template>
            <template v-else>
                Showing
                <span class="font-semibold text-gray-900 dark:text-stone-100">{{ fromToText }}</span>
                of <span class="font-semibold text-gray-900 dark:text-stone-100">{{ paginator.total }}</span>
            </template>
        </p>
        <div class="flex gap-1">
            <template v-for="(link, index) in paginator.links" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    :class="linkClasses(link)"
                    v-html="link.label"
                />
                <span
                    v-else
                    :class="linkClasses(link)"
                    class="cursor-not-allowed opacity-50"
                    v-html="link.label"
                />
            </template>
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

const fromToText = computed(() => {
    const from = props.paginator.from;
    const to = props.paginator.to;
    if (from != null && to != null) {
        return `${from}–${to}`;
    }
    return '0–0';
});

function linkClasses(link) {
    const base = 'inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-lg border px-3 text-sm font-medium transition';
    if (link.active) {
        return `${base} border-green-500 bg-green-50 text-green-600 dark:border-green-500 dark:bg-green-900/20 dark:text-green-500`;
    }
    return `${base} border-gray-300 bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white`;
}
</script>
