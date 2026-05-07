<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const isOnline = ref(navigator.onLine)
const sidebarOpen = ref(false)

const navLinks = [
    { name: 'Dashboard', href: '/dashboard', icon: '📊', routeName: 'dashboard' },
    { name: 'POS Terminal', href: '/pos', icon: '🛒', routeName: 'pos' },
    { name: 'Products', href: '/products', icon: '📦', routeName: 'products.index' },
    { name: 'Categories', href: '/categories', icon: '🏷️', routeName: 'categories.index' },
    { name: 'Sales', href: '/sales', icon: '💰', routeName: 'sales.index' },
    { name: 'Reports', href: '/reports', icon: '📈', routeName: 'reports.index' },
    { name: 'Settings', href: '/settings', icon: '⚙️', routeName: 'settings.index' },
]

const updateOnlineStatus = () => { isOnline.value = navigator.onLine }

onMounted(() => {
    window.addEventListener('online', updateOnlineStatus)
    window.addEventListener('offline', updateOnlineStatus)
})

onUnmounted(() => {
    window.removeEventListener('online', updateOnlineStatus)
    window.removeEventListener('offline', updateOnlineStatus)
})

const isActive = (href) => page.url.startsWith(href)
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <aside class="fixed inset-y-0 left-0 w-60 bg-slate-800 flex flex-col z-40">
            <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-700">
                <span class="text-xl">🏪</span>
                <span class="text-white font-semibold text-lg">Smart POS</span>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <Link
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                        isActive(link.href)
                            ? 'bg-slate-700 text-white'
                            : 'text-slate-300 hover:bg-slate-700 hover:text-white'
                    ]"
                >
                    <span class="text-base">{{ link.icon }}</span>
                    {{ link.name }}
                </Link>
            </nav>

            <div class="px-4 py-4 border-t border-slate-700">
                <div class="flex items-center gap-2 mb-3">
                    <span :class="['w-2 h-2 rounded-full', isOnline ? 'bg-green-400' : 'bg-red-400']"></span>
                    <span class="text-xs text-slate-400">{{ isOnline ? 'Online' : 'Offline' }}</span>
                </div>
                <p class="text-slate-300 text-sm font-medium mb-2">{{ $page.props.auth.user.name }}</p>
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="text-xs text-slate-400 hover:text-white transition-colors"
                >
                    Logout
                </Link>
            </div>
        </aside>

        <div class="flex-1 ml-60 flex flex-col">
            <header class="sticky top-0 z-30 bg-white border-b border-slate-200 h-14 flex items-center px-6">
                <h1 class="text-slate-800 font-semibold text-lg">
                    <slot name="header" />
                </h1>
            </header>

            <main class="flex-1 p-6">
                <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ $page.props.flash.error }}
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>
