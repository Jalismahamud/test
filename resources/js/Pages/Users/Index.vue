<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({ users: Array })

const roleClass = (role) => {
    const m = { admin: 'bg-purple-100 text-purple-700', manager: 'bg-blue-100 text-blue-700', cashier: 'bg-slate-100 text-slate-600' }
    return m[role] || ''
}
</script>

<template>
    <Head title="Users" />
    <AuthenticatedLayout>
        <template #header>Users</template>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs text-slate-500 font-medium">
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3 text-center">Role</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3">Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ user.name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ user.email }}</td>
                        <td class="px-5 py-3 text-center">
                            <span :class="['px-2 py-0.5 rounded-full text-xs font-medium capitalize', roleClass(user.role)]">{{ user.role }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', user.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600']">
                                {{ user.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs">{{ user.last_login_at || 'Never' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
