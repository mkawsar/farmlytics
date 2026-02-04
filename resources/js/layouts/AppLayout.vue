<template>
    <div class="min-h-screen bg-stone-50 flex flex-col dark:bg-stone-900">
        <!-- Top nav -->
        <header class="sticky top-0 z-30 flex h-14 items-center gap-4 border-b border-stone-200/80 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 px-4 sm:px-6 dark:border-stone-700 dark:bg-stone-900/95">
            <div class="flex flex-1 items-center gap-4">
                <Link :href="route('home')" class="flex items-center gap-2 text-[#2d5016] font-semibold tracking-tight dark:text-emerald-400">
                    <span class="text-xl">Farmlytics</span>
                </Link>
            </div>
            <nav class="relative flex items-center gap-2">
                <!-- Theme toggle -->
                <button
                    type="button"
                    @click="toggleTheme"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-stone-500 transition hover:bg-stone-100 hover:text-stone-700 focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-200"
                    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <span v-if="isDark" class="text-lg" aria-hidden="true">☀️</span>
                    <span v-else class="text-lg" aria-hidden="true">🌙</span>
                </button>
                <div class="group relative pt-2">
                    <!-- Profile trigger -->
                    <button
                        type="button"
                        class="-mt-2 flex h-9 w-9 items-center justify-center rounded-full bg-stone-200 text-stone-600 transition hover:bg-[#2d5016] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 dark:bg-stone-600 dark:text-stone-300 dark:focus:ring-offset-stone-900"
                        aria-expanded="false"
                        aria-haspopup="true"
                        aria-label="Profile menu"
                    >
                        <span class="text-sm font-medium" aria-hidden="true">
                            {{ (user?.name ?? user?.email ?? '?').charAt(0).toUpperCase() }}
                        </span>
                    </button>

                    <!-- Dropdown (hover); pt-2 bridge so cursor can move to menu without closing -->
                    <div
                        class="pointer-events-none absolute right-0 top-full z-50 w-56 origin-top-right scale-95 rounded-xl border border-stone-200/80 bg-white pt-2 opacity-0 shadow-lg transition duration-150 ease-out group-hover:scale-100 group-hover:opacity-100 group-hover:pointer-events-auto dark:border-stone-600 dark:bg-stone-800"
                        role="menu"
                    >
                    <div class="border-b border-stone-100 bg-white px-4 py-3 rounded-t-xl dark:border-stone-700 dark:bg-stone-800">
                        <p class="truncate text-sm font-medium text-stone-900 dark:text-stone-100">{{ user?.name ?? 'User' }}</p>
                        <p class="truncate text-xs text-stone-500 dark:text-stone-400">{{ user?.email }}</p>
                    </div>
                    <div class="py-1 bg-white rounded-b-xl dark:bg-stone-800">
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
            </nav>
        </header>

        <div class="flex flex-1">
            <!-- Aside nav -->
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

            <!-- Main content -->
            <main class="flex-1 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);

const THEME_KEY = 'farmlytics-theme';
const isDark = ref(false);

function applyTheme(dark) {
    isDark.value = dark;
    if (typeof document !== 'undefined') {
        if (dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        try {
            localStorage.setItem(THEME_KEY, dark ? 'dark' : 'light');
        } catch (_) {}
    }
}

function toggleTheme() {
    applyTheme(!isDark.value);
}

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});

const navItems = [
    { label: 'Dashboard', href: '/', icon: '◉' },
];

function isActive(href) {
    const url = page.url;
    if (href === '/') return url === '/' || url === '';
    return url.startsWith(href);
}

function route(name) {
    const routes = { home: '/', login: '/login' };
    return routes[name] ?? '/';
}
</script>
