<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/AppLayouts/AdminLayout.vue';

const sections = [
    {
        title: 'Posts',
        description: 'Manage blog posts, drafts, and published content.',
        href: route('admin.posts.index'),
        action: 'Open Posts',
    },
    {
        title: 'Categories',
        description: 'Organize content categories used across the blog.',
        href: route('admin.categories.index'),
        action: 'Open Categories',
    },
    {
        title: 'Tags',
        description: 'Manage tagging structure for filtering and discovery.',
        href: route('admin.tags.index'),
        action: 'Open Tags',
    },
    {
        title: 'Media Library',
        description: 'Upload and manage images and media assets.',
        href: route('admin.media.index'),
        action: 'Open Media',
    },
    {
        title: 'Popups',
        description: 'Control popup content, triggers, targeting, and lead capture.',
        href: route('admin.popups.index'),
        action: 'Open Popups',
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="h-full p-4">
            <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white">
                <div class="border-b border-gray-200 p-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                Admin Home
                            </p>
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">
                                Dashboard
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600">
                                This is the admin entry point for the consultation site. Use it as a clean launcher into
                                the real working sections of the system instead of the default Laravel placeholder screen.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Link
                                :href="route('admin.posts.create')"
                                class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                            >
                                New Post
                            </Link>

                            <Link
                                :href="route('admin.popups.create')"
                                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                New Popup
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <Link
                            v-for="section in sections"
                            :key="section.title"
                            :href="section.href"
                            class="group rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-gray-300 hover:shadow-sm"
                        >
                            <div class="flex h-full flex-col justify-between gap-6">
                                <div class="space-y-3">
                                    <h2 class="text-xl font-semibold tracking-tight text-gray-900">
                                        {{ section.title }}
                                    </h2>
                                    <p class="text-sm leading-relaxed text-gray-600">
                                        {{ section.description }}
                                    </p>
                                </div>

                                <div class="text-sm font-medium text-gray-900 group-hover:text-black">
                                    {{ section.action }}
                                </div>
                            </div>
                        </Link>
                    </div>

                    <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-gray-600">
                            This dashboard is intentionally simple. It matches the real admin structure already in use
                            by the site and avoids the default starter dashboard that came with Laravel.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
