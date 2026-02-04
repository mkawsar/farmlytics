<template>
    <Head title="Login" />
    <div class="min-h-screen flex">
        <!-- Left: Branding panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-[#2d5016] relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40" />
            <div class="relative z-10 flex flex-col justify-between p-12 text-white">
                <div class="text-2xl font-semibold tracking-tight">Farmlytics</div>
                <div>
                    <h2 class="text-3xl font-bold leading-tight mb-4">Manage your farm with confidence.</h2>
                    <p class="text-white/80 text-lg max-w-sm">Track yields, weather, and insights in one place.</p>
                </div>
                <p class="text-white/60 text-sm">© Farmlytics</p>
            </div>
        </div>

        <!-- Right: Login form -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-stone-50">
            <div class="w-full max-w-md">
                <!-- Mobile logo -->
                <div class="lg:hidden text-center mb-8">
                    <span class="text-2xl font-semibold text-[#2d5016] tracking-tight">Farmlytics</span>
                </div>

                <div class="bg-white rounded-2xl shadow-xl shadow-stone-200/50 border border-stone-200/80 p-8 sm:p-10">
                    <h1 class="text-2xl font-bold text-stone-900 mb-1">Welcome back</h1>
                    <p class="text-stone-500 mb-8">Sign in to your account</p>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-stone-700 mb-1.5">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                required
                                class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 transition"
                                placeholder="you@example.com"
                            />
                            <p v-if="form.errors.email" class="mt-1.5 text-sm text-rose-600">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-stone-700 mb-1.5">Password</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="w-full rounded-xl border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder-stone-400 focus:border-[#2d5016] focus:outline-none focus:ring-2 focus:ring-[#2d5016]/20 transition"
                                placeholder="••••••••"
                            />
                            <p v-if="form.errors.password" class="mt-1.5 text-sm text-rose-600">{{ form.errors.password }}</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-stone-300 text-[#2d5016] focus:ring-[#2d5016]"
                                />
                                <span class="text-sm text-stone-600">Remember me</span>
                            </label>
                        </div>

                        <p v-if="form.errors.message" class="text-sm text-rose-600">{{ form.errors.message }}</p>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full cursor-pointer rounded-xl bg-[#2d5016] px-4 py-3.5 font-semibold text-white shadow-lg shadow-[#2d5016]/25 hover:bg-[#244012] focus:outline-none focus:ring-2 focus:ring-[#2d5016] focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed transition"
                        >
                            <span v-if="form.processing">Signing in…</span>
                            <span v-else>Sign in</span>
                        </button>
                    </form>
                </div>

                <p class="mt-8 text-center text-sm text-stone-500">
                    Demo: use your account credentials to sign in.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login');
}
</script>
