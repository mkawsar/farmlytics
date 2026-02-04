<template>
    <button
        type="button"
        @click="toggleTheme"
        class="flex h-9 w-9 items-center justify-center rounded-lg text-stone-500 transition hover:bg-stone-100 hover:text-stone-700 focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-200"
        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <span v-if="isDark" class="text-lg" aria-hidden="true">☀️</span>
        <span v-else class="text-lg" aria-hidden="true">🌙</span>
    </button>
</template>

<script setup>
import { useTheme } from '../../composables/useTheme';

const { isDark, toggleTheme } = useTheme();
</script>
