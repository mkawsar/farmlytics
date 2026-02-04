<template>
    <header class="sticky top-0 z-30 flex h-14 items-center gap-4 border-b border-stone-200/80 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 px-4 sm:px-6 dark:border-stone-700 dark:bg-stone-900/95">
        <div class="flex flex-1 items-center gap-4">
            <Link :href="homeUrl" class="flex items-center gap-2 text-[#2d5016] font-semibold tracking-tight dark:text-emerald-400">
                <span class="text-xl">Farmlytics</span>
            </Link>
        </div>
        <nav class="relative flex items-center gap-2">
            <ThemeToggle />
            <ProfileDropdown :user="user" />
        </nav>
    </header>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ThemeToggle from './ThemeToggle.vue';
import ProfileDropdown from './ProfileDropdown.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const homeUrl = '/';
</script>
