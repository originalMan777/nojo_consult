<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/AppLayouts/PublicLayout.vue';

type CategoryDto = { name: string; slug: string };

type PostRow = {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    published_at: string;
    featured_image_url: string | null;
};

type PaginationLink = { url: string | null; label: string; active: boolean };
type Pagination<T> = { data: T[]; links: PaginationLink[] };

type SeoDto = { title: string; description: string; canonical_url: string };

const props = defineProps<{
    seo: SeoDto;
    category: CategoryDto;
    posts: Pagination<PostRow>;
}>();

const formatDate = (value: string) => {
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>

<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <link rel="canonical" :href="seo.canonical_url" />
    </Head>

    <PublicLayout>
        <div class="mx-auto max-w-3xl">
            <div class="mb-8">
                <Link :href="route('blog.index')" class="text-sm text-gray-600 hover:text-gray-900">← Back to Blog</Link>
            </div>

            <header class="mb-8">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">{{ category.name }}</h1>
                <p class="mt-2 text-sm text-gray-600">{{ seo.description }}</p>
            </header>

            <div class="space-y-6">
                <div v-if="posts.data.length === 0" class="text-sm text-gray-600">
                    No published posts in this category yet.
                </div>

                <article
                    v-for="post in posts.data"
                    :key="post.id"
                    class="group rounded-xl border bg-white p-5 hover:bg-gray-50 transition"
                >
                    <div v-if="post.featured_image_url" class="mb-4">
                        <img
                            :src="post.featured_image_url"
                            alt=""
                            class="h-48 w-full rounded-lg border object-cover"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>

                    <div class="text-xs text-gray-500">{{ formatDate(post.published_at) }}</div>

                    <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">
                        <Link :href="route('blog.show', post.slug)" class="group-hover:underline">
                            {{ post.title }}
                        </Link>
                    </h2>

                    <p v-if="post.excerpt" class="mt-2 text-sm text-gray-700 whitespace-pre-wrap">
                        {{ post.excerpt }}
                    </p>

                    <div class="mt-4">
                        <Link :href="route('blog.show', post.slug)" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                            Read article →
                        </Link>
                    </div>
                </article>

                <div v-if="posts.links?.length" class="pt-2">
                    <div class="flex flex-wrap gap-1">
                        <template v-for="link in posts.links" :key="link.label">
                            <span v-if="!link.url" class="px-2 py-1 text-sm text-gray-400" v-html="link.label" />
                            <Link
                                v-else
                                :href="link.url"
                                class="rounded-md px-2 py-1 text-sm"
                                :class="link.active ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                                v-html="link.label"
                                preserve-scroll
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
