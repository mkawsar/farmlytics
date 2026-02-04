<template>
    <Head title="Home" />
    <AppLayout>
        <div class="p-6 sm:p-8">
            <div class="max-w-4xl">
                <h1 class="text-2xl font-bold text-stone-900 sm:text-3xl dark:text-stone-100">Dashboard</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">Welcome to Farmlytics. Manage your farm with confidence.</p>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <h2 class="font-semibold text-stone-900 dark:text-stone-100">Overview</h2>
                        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Track yields, weather, and insights in one place.</p>
                    </div>
                    <div class="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <h2 class="font-semibold text-stone-900 dark:text-stone-100">Reports</h2>
                        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">View and export your farm reports.</p>
                    </div>
                    <div class="rounded-xl border border-stone-200/80 bg-white p-5 shadow-sm dark:border-stone-700 dark:bg-stone-800">
                        <h2 class="font-semibold text-stone-900 dark:text-stone-100">Settings</h2>
                        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Configure your account and preferences.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '../../layouts/AppLayout.vue';
</script>
