<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({ sales: Object, filters: Object })

const from = ref(props.filters?.from || '')
const to = ref(props.filters?.to || '')
const status = ref(props.filters?.status || '')

function applyFilter() {
    router.get('/sales', { from: from.value, to: to.value, status: status.value }, { preserveState: true })
}

const statusClass = (s) => {
    const m = { completed: 'bg-green-100 text-green-700', refunded: 'bg-red-100 text-red-700', held: 'bg-yellow-100 text-yellow-700' }
    return m[s] || 'bg-slate-100 text-slate-600'
}
</script>

<template>
    <Head title="Sales" />
    <AuthenticatedLayout>
        <template #header>Sales History</template>

        <div class="space-y-4">
            <div class="flex items-center gap-3 flex-wrap">
                <input v-model="from" type="date" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" />
                <span class="text-slate-400 text-sm">to</span>
                <input v-model="to" type="date" class="border border-slate-200 rounded-lg px-3 py-2 text-sm" />
                <select v-model="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="refunded">Refunded</option>
                    <option value="held">Held</option>
                </select>
                <button @click="applyFilter" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Filter</button>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs text-slate-500 font-medium">
                            <th class="px-5 py-3">Invoice</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-center">Payment</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3">Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in sales.data" :key="sale.id" class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="px-5 py-3 font-mono text-xs text-slate-600">{{ sale.invoice_number }}</td>
                            <td class="px-5 py-3 text-slate-500 text-xs">{{ sale.sold_at }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ sale.customer }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-800">{{ sale.total_amount }}</td>
                            <td class="px-5 py-3 text-center capitalize text-slate-500">{{ sale.payment_method }}</td>
                            <td class="px-5 py-3 text-center">
                                <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', statusClass(sale.status)]">{{ sale.status }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ sale.cashier }}</td>
                        </tr>
                        <tr v-if="!sales.data?.length">
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">No sales found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
