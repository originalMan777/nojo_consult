<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/AppLayouts/AdminLayout.vue';

const form = useForm({
    package: '',
});

const submit = () => {
    form.post(route('admin.post-importer.store'));
};
</script>

<template>
    <Head title="Post Importer" />

    <AdminLayout>
        <div class="h-full p-4">
            <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        Tools
                    </p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">
                        Post Importer
                    </h1>
                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600">
                        Paste one AI post package in the strict TITLE / ARTICLE / LIST format. A valid package will be parsed and imported as a draft post.
                    </p>
                </div>

                <div class="flex-1 p-6">
                    <form class="space-y-5" @submit.prevent="submit">
                        <div>
                            <label for="package" class="mb-2 block text-sm font-medium text-gray-900">
                                AI Post Package
                            </label>
                            <textarea
                                id="package"
                                v-model="form.package"
                                rows="22"
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 font-mono text-sm leading-6 text-gray-900 shadow-sm focus:border-gray-500 focus:outline-none focus:ring-0"
                                placeholder="TITLE:
Example Title

ARTICLE:
Example article body.

LIST:
- SEO Title: Example SEO Title
- Slug: example-slug
- Excerpt: Example excerpt.
- Sources: Example source line.
- Category: Example Category
- Tags: example one, example two
- Meta Title: Example Meta Title
- Meta Description: Example meta description.
- Canonical URL: https://www.yourdomain.com/blog/example-slug
- OG Title: Example OG Title
- OG Description: Example OG description.
- Featured Image Path: /images/blog/example-cover.jpg
- OG Image Path: /images/blog/example-og.jpg
- Noindex: No"
                            />
                            <p v-if="form.errors.package" class="mt-2 text-sm text-red-600">
                                {{ form.errors.package }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm leading-relaxed text-gray-600">
                            Import rules:
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                <li>one package at a time</li>
                                <li>must use TITLE:, ARTICLE:, LIST: in exact order</li>
                                <li>LIST lines must match the required label format</li>
                                <li>successful import creates a draft post only</li>
                            </ul>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Importing…' : 'Import Draft Post' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
