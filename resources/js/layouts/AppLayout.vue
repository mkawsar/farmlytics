<template>
    <div class="min-h-screen bg-stone-50 flex flex-col dark:bg-stone-900">
        <AppHeader />

        <div class="flex flex-1">
            <AppSidebar />
            <main class="flex-1 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import AppHeader from './components/AppHeader.vue';
import AppSidebar from './components/AppSidebar.vue';
</script>
