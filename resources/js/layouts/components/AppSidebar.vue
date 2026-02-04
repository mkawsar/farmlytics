<template>
    <aside class="hidden lg:flex lg:w-56 lg:flex-col lg:border-r lg:border-stone-200/80 lg:bg-white dark:border-stone-700 dark:bg-stone-800">
        <nav class="flex flex-1 flex-col gap-0.5 p-3">
            <Link
                v-for="item in navItems"
                :key="item.href"
                :href="item.href"
                :class="[
                    'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                    isActive(item.href)
                        ? 'bg-[#2d5016]/10 text-[#2d5016] dark:bg-[#2d5016]/20 dark:text-emerald-300'
                        : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-200'
                ]"
            >
                <span class="shrink-0 text-stone-400" aria-hidden="true">{{ item.icon }}</span>
                {{ item.label }}
            </Link>
        </nav>
    </aside>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const navItems = [
    { label: 'Dashboard', href: '/', icon: '◉' },
];

function isActive(href) {
    const url = page.url;
    if (href === '/') return url === '/' || url === '';
    return url.startsWith(href);
}
</script>
