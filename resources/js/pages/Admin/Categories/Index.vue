<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/AppLayouts/AdminLayout.vue';

import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import DangerButton from '@/components/DangerButton.vue';
import TextInput from '@/components/TextInput.vue';

type CategoryRow = {
    id: number;
    name: string;
    slug: string;
};

const props = defineProps<{ categories: CategoryRow[] }>();

const createForm = useForm({
    name: '',
    slug: '',
});

const editingId = ref<number | null>(null);
const editForm = useForm({
    name: '',
    slug: '',
});

const startEdit = (category: CategoryRow) => {
    editingId.value = category.id;
    editForm.clearErrors();
    editForm.name = category.name;
    editForm.slug = category.slug;
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const createCategory = () => {
    createForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const saveEdit = () => {
    if (!editingId.value) return;

    editForm.put(route('admin.categories.update', editingId.value), {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
            editForm.reset();
        },
    });
};

const deleteCategory = (category: CategoryRow) => {
    if (!confirm(`Delete category "${category.name}"?`)) return;

    router.delete(route('admin.categories.destroy', category.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Categories" />

    <AdminLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Categories</h1>
                <p class="mt-1 text-sm text-gray-600">Primary classification for posts.</p>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Create -->
            <form @submit.prevent="createCategory" class="rounded-lg border bg-white p-4 space-y-4">
                <div class="text-sm font-semibold text-gray-900">New Category</div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="new_name" value="Name" />
                        <TextInput id="new_name" v-model="createForm.name" type="text" class="mt-1 block w-full" />
                        <InputError :message="createForm.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="new_slug" value="Slug (optional)" />
                        <TextInput id="new_slug" v-model="createForm.slug" type="text" class="mt-1 block w-full" />
                        <InputError :message="createForm.errors.slug" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">If blank, it will be generated from the name.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <PrimaryButton :disabled="createForm.processing">Create</PrimaryButton>
                    <span v-if="createForm.processing" class="text-sm text-gray-500">Saving…</span>
                </div>
            </form>

            <!-- List -->
            <div class="rounded-lg border bg-white">
                <div class="border-b p-4">
                    <div class="text-sm font-semibold text-gray-900">All Categories</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                                    Name
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                                    Slug
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y bg-white">
                            <tr v-if="categories.length === 0">
                                <td colspan="3" class="px-4 py-6 text-sm text-gray-600">
                                    No categories yet.
                                </td>
                            </tr>

                            <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50">
                                <template v-if="editingId === category.id">
                                    <td class="px-4 py-3 align-top">
                                        <TextInput
                                            :id="`edit_name_${category.id}`"
                                            v-model="editForm.name"
                                            type="text"
                                            class="block w-full"
                                        />
                                        <InputError :message="editForm.errors.name" class="mt-2" />
                                    </td>

                                    <td class="px-4 py-3 align-top">
                                        <TextInput
                                            :id="`edit_slug_${category.id}`"
                                            v-model="editForm.slug"
                                            type="text"
                                            class="block w-full"
                                        />
                                        <InputError :message="editForm.errors.slug" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500">Blank = generate from name.</p>
                                    </td>

                                    <td class="px-4 py-3 text-right align-top">
                                        <div class="flex justify-end gap-2">
                                            <SecondaryButton type="button" @click="cancelEdit" :disabled="editForm.processing">
                                                Cancel
                                            </SecondaryButton>
                                            <PrimaryButton type="button" @click="saveEdit" :disabled="editForm.processing">
                                                Save
                                            </PrimaryButton>
                                        </div>
                                    </td>
                                </template>

                                <template v-else>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-700">{{ category.slug }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <div class="flex justify-end gap-2">
                                            <SecondaryButton type="button" @click="startEdit(category)">
                                                Edit
                                            </SecondaryButton>
                                            <DangerButton type="button" @click="deleteCategory(category)">
                                                Delete
                                            </DangerButton>
                                        </div>
                                    </td>
                                </template>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
