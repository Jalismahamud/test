<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({ categories: Object })
</script>

<template>
    <Head title="Categories" />
    <AuthenticatedLayout>
        <template #header>Categories</template>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-left text-xs text-slate-500 font-medium">
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3 text-center">Products</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="cat in categories.data" :key="cat.id" class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ cat.name }}</td>
                        <td class="px-5 py-3 text-center text-slate-600">{{ cat.products_count }}</td>
                        <td class="px-5 py-3 text-center">
                            <span :class="['px-2 py-0.5 rounded-full text-xs font-medium',
                                cat.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500']">
                                {{ cat.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="!categories.data?.length">
                        <td colspan="3" class="px-5 py-10 text-center text-slate-400">No categories yet</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
