<template>
    <aside class="hidden lg:flex lg:w-60 lg:flex-col lg:border-r lg:border-stone-200/80 lg:bg-white lg:shadow-[2px_0_12px_-4px_rgba(0,0,0,0.06)] dark:border-stone-700 dark:bg-stone-800 dark:shadow-[2px_0_12px_-4px_rgba(0,0,0,0.2)]">
        <nav class="flex flex-1 flex-col gap-1 overflow-auto p-3">
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
        <div class="shrink-0 border-t border-stone-200/80 p-3 dark:border-stone-700">
            <Link
                href="/logout"
                method="post"
                as="button"
                class="group flex w-full cursor-pointer items-center gap-3 rounded-xl border border-red-200/60 bg-red-50/80 px-3 py-3 text-left text-sm font-semibold text-red-600 shadow-sm transition-all duration-200 hover:border-red-300 hover:bg-red-100 hover:shadow hover:text-red-700 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-400 dark:hover:border-red-800 dark:hover:bg-red-900/40 dark:hover:text-red-300"
            >
                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600 transition-colors group-hover:bg-red-200 group-hover:text-red-700 dark:bg-red-900/50 dark:text-red-400 dark:group-hover:bg-red-800/50 dark:group-hover:text-red-300"
                    aria-hidden="true"
                >
                    →
                </span>
                Log out
            </Link>
        </div>
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
