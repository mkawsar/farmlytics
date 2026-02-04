<template>
    <div class="flex min-h-screen flex-col bg-stone-50 dark:bg-stone-900">
        <AppHeader />

        <div class="flex flex-1">
            <AppSidebar />
            <main class="flex-1 overflow-auto">
                <slot />
            </main>
        </div>

        <footer class="shrink-0 border-t border-stone-200/80 bg-white px-4 py-3 text-center text-sm text-stone-500 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-400">
            © 2026 - {{ year }} Farmlytics
        </footer>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import AppHeader from './components/AppHeader.vue';
import AppSidebar from './components/AppSidebar.vue';

const year = computed(() => new Date().getFullYear());
</script>
