import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export const useCartStore = defineStore('cart', () => {
    const items = ref([])
    const customerId = ref(null)
    const discountAmount = ref(0)
    const discountType = ref('fixed')
    const paymentMethod = ref('cash')
    const paidAmount = ref(0)
    const note = ref('')

    const subtotal = computed(() =>
        items.value.reduce((sum, item) => sum + (item.price * item.quantity) - item.lineDiscount, 0)
    )

    const taxAmount = computed(() =>
        items.value.reduce((sum, item) => {
            const lineTotal = (item.price * item.quantity) - item.lineDiscount
            return sum + (lineTotal * item.taxRate / 100)
        }, 0)
    )

    const totalDiscount = computed(() => {
        if (discountType.value === 'percent') {
            return subtotal.value * (discountAmount.value / 100)
        }
        return parseFloat(discountAmount.value) || 0
    })

    const total = computed(() => Math.max(0, subtotal.value + taxAmount.value - totalDiscount.value))

    const changeAmount = computed(() => Math.max(0, (parseFloat(paidAmount.value) || 0) - total.value))

    const itemCount = computed(() => items.value.reduce((sum, i) => sum + i.quantity, 0))

    function addItem(product) {
        const existing = items.value.find(i => i.productId === product.id)
        if (existing) {
            existing.quantity++
        } else {
            items.value.push({
                productId:    product.id,
                name:         product.name,
                sku:          product.sku,
                price:        parseFloat(product.selling_price),
                costPrice:    parseFloat(product.cost_price),
                taxRate:      parseFloat(product.tax_rate) || 0,
                quantity:     1,
                lineDiscount: 0,
                stock:        parseFloat(product.current_stock) || 0,
            })
        }
    }

    function removeItem(productId) {
        items.value = items.value.filter(i => i.productId !== productId)
    }

    function updateQty(productId, qty) {
        const item = items.value.find(i => i.productId === productId)
        if (!item) return
        if (qty <= 0) {
            removeItem(productId)
        } else {
            item.quantity = qty
        }
    }

    function setLineDiscount(productId, discount) {
        const item = items.value.find(i => i.productId === productId)
        if (item) item.lineDiscount = parseFloat(discount) || 0
    }

    function clearCart() {
        items.value = []
        customerId.value = null
        discountAmount.value = 0
        discountType.value = 'fixed'
        paymentMethod.value = 'cash'
        paidAmount.value = 0
        note.value = ''
    }

    function buildSalePayload() {
        return {
            customer_id:     customerId.value,
            items:           items.value.map(i => ({
                product_id:  i.productId,
                quantity:    i.quantity,
                unit_price:  i.price,
                discount:    i.lineDiscount,
                tax_amount:  parseFloat(((i.price * i.quantity - i.lineDiscount) * i.taxRate / 100).toFixed(2)),
                total:       parseFloat(((i.price * i.quantity - i.lineDiscount) * (1 + i.taxRate / 100)).toFixed(2)),
            })),
            subtotal:        parseFloat(subtotal.value.toFixed(2)),
            tax_amount:      parseFloat(taxAmount.value.toFixed(2)),
            discount_amount: parseFloat(totalDiscount.value.toFixed(2)),
            total_amount:    parseFloat(total.value.toFixed(2)),
            paid_amount:     parseFloat(paidAmount.value) || 0,
            change_amount:   parseFloat(changeAmount.value.toFixed(2)),
            payment_method:  paymentMethod.value,
            note:            note.value,
            sold_at:         new Date().toISOString(),
        }
    }

    return {
        items, customerId, discountAmount, discountType,
        paymentMethod, paidAmount, note,
        subtotal, taxAmount, totalDiscount, total, changeAmount, itemCount,
        addItem, removeItem, updateQty, setLineDiscount, clearCart, buildSalePayload,
    }
})
