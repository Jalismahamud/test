<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import { getDashboardStats } from '@/services/api'

const stats = ref(null)

onMounted(async () => {
    try {
        const res = await getDashboardStats()
        stats.value = res.data.data
    } catch {}
})
</script>

<template>
    <Head title="Reports" />
    <AuthenticatedLayout>
        <template #header>Reports</template>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition-shadow cursor-pointer">
                <div class="text-2xl mb-3">📊</div>
                <h3 class="font-semibold text-slate-800">Sales Report</h3>
                <p class="text-sm text-slate-400 mt-1">Daily, weekly, monthly breakdown</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition-shadow cursor-pointer">
                <div class="text-2xl mb-3">💹</div>
                <h3 class="font-semibold text-slate-800">Profit & Loss</h3>
                <p class="text-sm text-slate-400 mt-1">Revenue vs cost analysis</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition-shadow cursor-pointer">
                <div class="text-2xl mb-3">📦</div>
                <h3 class="font-semibold text-slate-800">Inventory Valuation</h3>
                <p class="text-sm text-slate-400 mt-1">Stock value by category</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition-shadow cursor-pointer">
                <div class="text-2xl mb-3">🏆</div>
                <h3 class="font-semibold text-slate-800">Top Products</h3>
                <p class="text-sm text-slate-400 mt-1">Best selling items</p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition-shadow cursor-pointer">
                <div class="text-2xl mb-3">👤</div>
                <h3 class="font-semibold text-slate-800">Cashier Report</h3>
                <p class="text-sm text-slate-400 mt-1">Performance by cashier</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
