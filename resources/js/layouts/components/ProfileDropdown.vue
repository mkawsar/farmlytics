<template>
    <div class="group relative pt-2">
        <button
            type="button"
            class="-mt-2 flex h-9 w-9 items-center justify-center rounded-full bg-stone-200 text-stone-600 transition hover:bg-[#2d5016] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 dark:bg-stone-600 dark:text-stone-300 dark:focus:ring-offset-stone-900"
            aria-expanded="false"
            aria-haspopup="true"
            aria-label="Profile menu"
        >
            <span class="text-sm font-medium" aria-hidden="true">
                {{ initial }}
            </span>
        </button>

        <div
            class="absolute right-0 top-full z-50 w-56 origin-top-right scale-95 rounded-xl border border-stone-200/80 bg-white pt-2 opacity-0 shadow-lg transition duration-150 ease-out pointer-events-none group-hover:scale-100 group-hover:opacity-100 group-hover:pointer-events-auto dark:border-stone-600 dark:bg-stone-800"
            role="menu"
        >
            <div class="rounded-t-xl border-b border-stone-100 bg-white px-4 py-3 dark:border-stone-700 dark:bg-stone-800">
                <p class="truncate text-sm font-medium text-stone-900 dark:text-stone-100">{{ user?.name ?? 'User' }}</p>
                <p class="truncate text-xs text-stone-500 dark:text-stone-400">{{ user?.email }}</p>
            </div>
            <div class="rounded-b-xl bg-white py-1 dark:bg-stone-800">
                <Link
                    href="/profile"
                    class="block px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 hover:text-stone-900 dark:text-stone-300 dark:hover:bg-stone-700 dark:hover:text-stone-100"
                    role="menuitem"
                >
                    Profile details
                </Link>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="block w-full px-4 py-2 text-left text-sm text-stone-700 hover:bg-stone-50 hover:text-stone-900 dark:text-stone-300 dark:hover:bg-stone-700 dark:hover:text-stone-100"
                    role="menuitem"
                >
                    Log out
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: { type: Object, default: null },
});

const initial = computed(() => {
    const u = props.user;
    const str = u?.name ?? u?.email ?? '?';
    return String(str).charAt(0).toUpperCase();
});
</script>
