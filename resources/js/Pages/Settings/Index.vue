<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({ business: Object, users: Array })

const form = useForm({
    name:     props.business?.name || '',
    phone:    props.business?.phone || '',
    email:    props.business?.email || '',
    address:  props.business?.address || '',
    currency: props.business?.currency || 'BDT',
    tax_rate: props.business?.tax_rate || 0,
})

const roleClass = (role) => {
    const m = { admin: 'bg-purple-100 text-purple-700', manager: 'bg-blue-100 text-blue-700', cashier: 'bg-slate-100 text-slate-600' }
    return m[role] || 'bg-slate-100 text-slate-600'
}
</script>

<template>
    <Head title="Settings" />
    <AuthenticatedLayout>
        <template #header>Settings</template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Business Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Business Name</label>
                        <input v-model="form.name" type="text" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Phone</label>
                        <input v-model="form.phone" type="text" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 mb-1">Email</label>
                        <input v-model="form.email" type="email" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Currency</label>
                            <input v-model="form.currency" type="text" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Tax Rate (%)</label>
                            <input v-model="form.tax_rate" type="number" min="0" max="100" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="font-semibold text-slate-800 mb-4">Users</h2>
                <div class="space-y-2">
                    <div v-for="user in users" :key="user.id"
                        class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ user.name }}</p>
                            <p class="text-xs text-slate-400">{{ user.email }}</p>
                        </div>
                        <span :class="['px-2 py-0.5 rounded-full text-xs font-medium capitalize', roleClass(user.role)]">
                            {{ user.role }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
