<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import { useCartStore } from '@/stores/cartStore'
import { searchProducts, processSale, getCustomers } from '@/services/api'

const props = defineProps({ categories: Array, business: Object })

const cart = useCartStore()
const isOnline = ref(navigator.onLine)
const loading = ref(false)
const searchQuery = ref('')
const selectedCategory = ref(null)
const products = ref([])
const customers = ref([])
const searchTimeout = ref(null)
const successMsg = ref('')
const errorMsg = ref('')

onMounted(() => {
    window.addEventListener('online', () => isOnline.value = true)
    window.addEventListener('offline', () => isOnline.value = false)
    loadProducts()
    loadCustomers()
})

async function loadProducts() {
    try {
        const res = await searchProducts(searchQuery.value, selectedCategory.value)
        products.value = res.data.data
    } catch {}
}

async function loadCustomers() {
    try {
        const res = await getCustomers()
        customers.value = res.data.data?.data || []
    } catch {}
}

watch(searchQuery, (val) => {
    clearTimeout(searchTimeout.value)
    searchTimeout.value = setTimeout(loadProducts, 350)
})

watch(selectedCategory, loadProducts)

function selectCategory(id) {
    selectedCategory.value = selectedCategory.value === id ? null : id
}

function formatCurrency(amount) {
    return props.business.currency + ' ' + parseFloat(amount || 0).toFixed(2)
}

async function handleProcessSale() {
    if (!cart.items.length) return
    loading.value = true
    errorMsg.value = ''
    successMsg.value = ''
    try {
        const payload = cart.buildSalePayload()
        await processSale(payload)
        successMsg.value = 'Sale completed successfully!'
        cart.clearCart()
        setTimeout(() => successMsg.value = '', 3000)
    } catch (err) {
        errorMsg.value = err.response?.data?.message || 'Sale failed. Try again.'
    } finally {
        loading.value = false
    }
}

function stockBadgeClass(stock) {
    if (stock <= 0) return 'bg-red-100 text-red-600'
    if (stock <= 5) return 'bg-orange-100 text-orange-600'
    return 'bg-green-100 text-green-600'
}
</script>

<template>
    <Head title="POS Terminal" />
    <AuthenticatedLayout>
        <template #header>
            POS Terminal
            <span v-if="!isOnline" class="ml-3 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">● Offline</span>
        </template>

        <div class="flex gap-4 h-[calc(100vh-8rem)]">
            <!-- LEFT: Product Search -->
            <div class="flex-1 flex flex-col gap-3 overflow-hidden">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search by name, SKU or barcode..."
                    class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                />

                <div class="flex gap-2 flex-wrap">
                    <button
                        @click="selectCategory(null)"
                        :class="['px-3 py-1 rounded-full text-xs font-medium transition-colors',
                            !selectedCategory ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']"
                    >All</button>
                    <button
                        v-for="cat in categories" :key="cat.id"
                        @click="selectCategory(cat.id)"
                        :class="['px-3 py-1 rounded-full text-xs font-medium transition-colors',
                            selectedCategory === cat.id ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']"
                    >{{ cat.name }}</button>
                </div>

                <div class="grid grid-cols-3 gap-2 overflow-y-auto pr-1">
                    <button
                        v-for="product in products" :key="product.id"
                        @click="cart.addItem(product)"
                        :disabled="product.current_stock <= 0 && product.track_inventory"
                        class="bg-white border border-slate-200 rounded-lg p-3 text-left hover:border-blue-300 hover:shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <p class="text-sm font-medium text-slate-700 truncate">{{ product.name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ product.sku }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-sm font-bold text-blue-600">{{ formatCurrency(product.selling_price) }}</p>
                            <span :class="['text-xs px-1.5 py-0.5 rounded', stockBadgeClass(product.current_stock)]">
                                {{ product.current_stock }}
                            </span>
                        </div>
                    </button>
                    <div v-if="!products.length" class="col-span-3 py-12 text-center text-slate-400 text-sm">
                        No products found
                    </div>
                </div>
            </div>

            <!-- RIGHT: Cart -->
            <div class="w-96 flex flex-col bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <select v-model="cart.customerId" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2">
                        <option :value="null">Walk-in Customer</option>
                        <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-2 space-y-2">
                    <div v-if="successMsg" class="text-xs bg-green-50 text-green-600 border border-green-200 px-3 py-2 rounded-lg">{{ successMsg }}</div>
                    <div v-if="errorMsg" class="text-xs bg-red-50 text-red-600 border border-red-200 px-3 py-2 rounded-lg">{{ errorMsg }}</div>

                    <div
                        v-for="item in cart.items" :key="item.productId"
                        class="flex items-center gap-2 py-2 border-b border-slate-50"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 truncate">{{ item.name }}</p>
                            <p class="text-xs text-slate-400">{{ formatCurrency(item.price) }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="cart.updateQty(item.productId, item.quantity - 1)" class="w-6 h-6 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-sm font-bold">-</button>
                            <input
                                :value="item.quantity"
                                @change="cart.updateQty(item.productId, parseFloat($event.target.value))"
                                type="number" min="0.001" step="1"
                                class="w-12 text-center text-sm border border-slate-200 rounded py-0.5"
                            />
                            <button @click="cart.updateQty(item.productId, item.quantity + 1)" class="w-6 h-6 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-sm font-bold">+</button>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 w-20 text-right">
                            {{ formatCurrency(item.price * item.quantity - item.lineDiscount) }}
                        </p>
                        <button @click="cart.removeItem(item.productId)" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                    </div>

                    <div v-if="!cart.items.length" class="py-8 text-center text-slate-400 text-sm">
                        Cart is empty — click a product to add
                    </div>
                </div>

                <div class="px-4 py-3 border-t border-slate-100 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 w-24">Discount</span>
                        <select v-model="cart.discountType" class="text-xs border border-slate-200 rounded px-2 py-1">
                            <option value="fixed">Fixed</option>
                            <option value="percent">%</option>
                        </select>
                        <input v-model="cart.discountAmount" type="number" min="0" placeholder="0"
                            class="flex-1 text-xs border border-slate-200 rounded px-2 py-1 text-right" />
                    </div>

                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between text-slate-500 text-xs">
                            <span>Subtotal</span><span>{{ formatCurrency(cart.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 text-xs">
                            <span>Tax</span><span>{{ formatCurrency(cart.taxAmount) }}</span>
                        </div>
                        <div v-if="cart.totalDiscount > 0" class="flex justify-between text-red-500 text-xs">
                            <span>Discount</span><span>-{{ formatCurrency(cart.totalDiscount) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-slate-800 text-base border-t border-slate-200 pt-2">
                            <span>Total</span><span>{{ formatCurrency(cart.total) }}</span>
                        </div>
                    </div>

                    <div class="flex gap-1">
                        <button v-for="m in ['cash','card','mobile']" :key="m"
                            @click="cart.paymentMethod = m"
                            :class="['flex-1 py-1.5 text-xs rounded-lg font-medium capitalize transition-colors',
                                cart.paymentMethod === m ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']"
                        >{{ m }}</button>
                    </div>

                    <input v-model="cart.paidAmount" type="number" min="0" placeholder="Amount paid"
                        class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 text-right font-semibold" />

                    <div v-if="cart.changeAmount > 0" class="flex justify-between text-green-600 text-sm font-semibold px-1">
                        <span>Change</span><span>{{ formatCurrency(cart.changeAmount) }}</span>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button @click="cart.clearCart()" class="flex-1 py-2 text-sm rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200">
                            Clear
                        </button>
                        <button
                            @click="handleProcessSale()"
                            :disabled="!cart.items.length || loading"
                            class="flex-1 py-2 text-sm rounded-lg bg-green-600 text-white hover:bg-green-700 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ loading ? 'Processing...' : '✓ Sale' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
