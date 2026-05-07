<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    stats: Object,
    recentSales: Array,
})

const formatCurrency = (amount) => {
    return 'BDT ' + parseFloat(amount).toLocaleString('en-BD', { minimumFractionDigits: 2 })
}

const statusColor = (status) => {
    const colors = { completed: 'bg-green-100 text-green-700', refunded: 'bg-red-100 text-red-700', held: 'bg-yellow-100 text-yellow-700' }
    return colors[status] || 'bg-slate-100 text-slate-700'
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>Dashboard</template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500 mb-1">Today's Sales</p>
                    <p class="text-3xl font-bold text-slate-800">{{ stats.todaySales }}</p>
                    <p class="text-xs text-slate-400 mt-1">transactions</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500 mb-1">Today's Revenue</p>
                    <p class="text-2xl font-bold text-green-600">{{ formatCurrency(stats.todayRevenue) }}</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500 mb-1">Low Stock Items</p>
                    <p class="text-3xl font-bold text-orange-500">{{ stats.lowStockCount }}</p>
                    <p class="text-xs text-slate-400 mt-1">need restock</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500 mb-1">Total Products</p>
                    <p class="text-3xl font-bold text-slate-800">{{ stats.totalProducts }}</p>
                    <p class="text-xs text-slate-400 mt-1">active products</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-700">Recent Sales</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 text-xs border-b border-slate-100">
                                <th class="px-6 py-3 font-medium">Invoice</th>
                                <th class="px-6 py-3 font-medium">Time</th>
                                <th class="px-6 py-3 font-medium">Items</th>
                                <th class="px-6 py-3 font-medium">Total</th>
                                <th class="px-6 py-3 font-medium">Cashier</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sale in recentSales" :key="sale.id" class="border-b border-slate-50 hover:bg-slate-50">
                                <td class="px-6 py-3 font-mono text-xs text-slate-600">{{ sale.invoice_number }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ sale.sold_at }}</td>
                                <td class="px-6 py-3 text-center">{{ sale.items_count }}</td>
                                <td class="px-6 py-3 font-semibold text-slate-700">{{ formatCurrency(sale.total_amount) }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ sale.cashier }}</td>
                                <td class="px-6 py-3">
                                    <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', statusColor(sale.status ?? 'completed')]">
                                        {{ sale.status ?? 'completed' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!recentSales?.length">
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400">No sales yet today</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
