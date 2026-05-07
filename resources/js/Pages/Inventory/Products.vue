<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({ products: Object, categories: Array, brands: Array, filters: Object })

const search = ref(props.filters?.search || '')
const categoryId = ref(props.filters?.category_id || '')
let searchTimer = null

watch(search, val => {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => router.get('/products', { search: val, category_id: categoryId.value }, { preserveState: true }), 400)
})

watch(categoryId, val => {
    router.get('/products', { search: search.value, category_id: val }, { preserveState: true })
})

const stockClass = (product) => {
    if (product.current_stock <= 0) return 'text-red-600'
    if (product.current_stock <= product.alert_quantity) return 'text-orange-500'
    return 'text-green-600'
}
</script>

<template>
    <Head title="Products" />
    <AuthenticatedLayout>
        <template #header>Products</template>

        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <input v-model="search" type="text" placeholder="Search products..."
                    class="flex-1 max-w-sm px-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <select v-model="categoryId" class="px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    <option value="">All Categories</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs text-slate-500 font-medium">
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">SKU</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3 text-right">Cost</th>
                            <th class="px-5 py-3 text-right">Price</th>
                            <th class="px-5 py-3 text-center">Stock</th>
                            <th class="px-5 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products.data" :key="product.id"
                            class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ product.name }}</td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ product.sku }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ product.category || '—' }}</td>
                            <td class="px-5 py-3 text-right text-slate-600">{{ product.cost_price }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-800">{{ product.selling_price }}</td>
                            <td class="px-5 py-3 text-center font-semibold" :class="stockClass(product)">
                                {{ product.current_stock }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span :class="['px-2 py-0.5 rounded-full text-xs font-medium',
                                    product.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500']">
                                    {{ product.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!products.data?.length">
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">No products found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="products.last_page > 1" class="flex gap-2 justify-center">
                <button v-for="page in products.last_page" :key="page"
                    @click="router.get('/products', { page, search: search, category_id: categoryId })"
                    :class="['px-3 py-1 rounded text-sm', page === products.current_page
                        ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50']">
                    {{ page }}
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
