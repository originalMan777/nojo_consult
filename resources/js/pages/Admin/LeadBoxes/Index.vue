<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/AppLayouts/AdminLayout.vue';

type LeadBoxRow = {
    id: number;
    type: 'resource' | 'service' | 'offer' | string;
    status: 'draft' | 'active' | 'inactive';
    internal_name: string;
    title: string;
    updated_at: string | null;
};

const props = defineProps<{
    leadBoxes: LeadBoxRow[];
}>();

const formatDate = (value: string | null) => {
    if (!value) return '—';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>

<template>
    <Head title="Lead Boxes" />

    <AdminLayout>
        <div class="h-full p-4">
            <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 p-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                Lead Blocks
                            </p>
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">
                                Lead Boxes
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600">
                                Library of CTA lead blocks. Create a Lead Box, then assign it to fixed slots.
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <Link
                                :href="route('admin.lead-boxes.create')"
                                class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-800"
                            >
                                Create New Lead Box
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-auto p-6">
                    <div class="overflow-hidden rounded-2xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">Name</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Title</th>
                                    <th class="px-4 py-3 text-left">Updated</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="box in props.leadBoxes" :key="box.id">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ box.internal_name }}
                                        <div class="mt-1 text-xs text-gray-500">Type: {{ box.type }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset"
                                            :class="{
                                                'bg-amber-50 text-amber-800 ring-amber-200': box.status === 'draft',
                                                'bg-emerald-50 text-emerald-800 ring-emerald-200': box.status === 'active',
                                                'bg-gray-100 text-gray-700 ring-gray-200': box.status === 'inactive',
                                            }"
                                        >
                                            {{ box.status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ box.title }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ formatDate(box.updated_at) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <Link
                                            :href="route('admin.lead-boxes.edit', box.id)"
                                            class="text-sm font-semibold text-gray-900 hover:text-gray-700"
                                        >
                                            Edit
                                        </Link>
                                    </td>
                                </tr>

                                <tr v-if="!props.leadBoxes.length">
                                    <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-600">
                                        No Lead Boxes yet.
                                        <div class="mt-3">
                                            <Link
                                                :href="route('admin.lead-boxes.create')"
                                                class="text-sm font-semibold text-gray-900 hover:text-gray-700"
                                            >
                                                Create your first Lead Box
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
