<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import FrontLayout from '@/layouts/FrontLayout.vue'

type CategoryDto = {
  name: string;
  slug: string;
}

type PostRow = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  published_at: string;
  category: CategoryDto | null;
  featured_image_url: string | null;
}

type PaginationLink = {
  url: string | null;
  label: string;
  active: boolean;
}

type Pagination<T> = {
  data: T[];
  links: PaginationLink[];
}

const { posts } = defineProps<{
  posts: Pagination<PostRow>;
}>()

const formatDate = (value: string) => {
  const d = new Date(value)

  if (Number.isNaN(d.getTime())) return value

  return d.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const badgeClasses = (category?: CategoryDto | null) => {
  const value = (category?.slug || category?.name || '').toString().trim().toLowerCase()

  if (value === 'food') {
    return 'border-2 border-green-600 text-green-700 bg-white'
  }

  if (value === 'dogs' || value === 'dog') {
    return 'border-2 border-blue-600 text-blue-700 bg-white'
  }

  if (value === 'trees' || value === 'tree') {
    return 'border-2 border-emerald-600 text-emerald-700 bg-white'
  }

  if (value === 'gold') {
    return 'border-2 border-amber-500 text-amber-700 bg-white'
  }

  if (value === 'news') {
    return 'border-2 border-slate-600 text-slate-700 bg-white'
  }

  if (value === 'guides' || value === 'guide') {
    return 'border-2 border-violet-600 text-violet-700 bg-white'
  }

  return 'border-2 border-rose-600 text-rose-700 bg-white'
}
</script>

<template>
  <FrontLayout>
    <Head title="Blog" />

    <div class="mx-auto max-w-6xl px-6 py-16">
      <section class="space-y-4">
        <p class="font-sans text-sm uppercase tracking-[0.22em] text-gray-500">
          Journal
        </p>

        <h1
          class="font-display text-6xl font-extrabold uppercase leading-none tracking-tight text-gray-950 md:text-7xl"
        >
          Blog
        </h1>

        <p class="max-w-3xl font-sans text-base leading-relaxed text-gray-600 md:text-lg">
          Real estate insights, guidance, and helpful articles for buyers,
          sellers, and investors.
        </p>
      </section>

      <section class="mt-10">
        <div class="mx-auto max-w-3xl">
          <div class="relative">
            <input
              type="text"
              placeholder="Search articles..."
              class="w-full rounded-full border border-gray-200 bg-white px-5 py-4 pr-12 text-base text-gray-700 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-gray-300 focus:ring-2 focus:ring-gray-200"
            />

            <svg
              class="pointer-events-none absolute right-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M9 3.5a5.5 5.5 0 1 0 3.473 9.765l3.63 3.631a.75.75 0 1 0 1.06-1.06l-3.63-3.632A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0a4 4 0 0 1-8 0Z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
        </div>
      </section>

      <section class="mt-14">
        <div v-if="posts.data.length === 0" class="font-sans text-sm text-gray-600">
          No posts yet.
        </div>

        <div v-else class="grid grid-cols-1 gap-14 md:grid-cols-2">
          <Link
            v-for="post in posts.data"
            :key="post.id"
            :href="route('blog.show', post.slug)"
            class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-[0_10px_45px_rgba(0,0,0,0.40)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_60px_rgba(0,0,0,0.50)]"
          >
            <div class="relative overflow-hidden">
              <div class="absolute left-4 top-4 z-10">
                <span
                  class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] shadow-sm"
                  :class="badgeClasses(post.category)"
                >
                  {{ post.category?.name ?? 'Article' }}
                </span>
              </div>

              <img
                v-if="post.featured_image_url"
                :src="post.featured_image_url"
                :alt="post.title"
                class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105"
              />

              <div
                v-else
                class="aspect-[16/10] w-full bg-gray-100"
              />
            </div>

            <div class="p-7">
              <div class="flex flex-wrap items-center gap-3 font-sans text-xs text-gray-400">
                <span>{{ formatDate(post.published_at) }}</span>

                <template v-if="post.category">
                  <span class="text-gray-300">•</span>
                  <span class="rounded-full bg-gray-100 px-2.5 py-1 font-medium text-gray-700">
                    {{ post.category.name }}
                  </span>
                </template>
              </div>

              <h2
                class="font-display mt-4 text-[26px] font-extrabold uppercase leading-tight tracking-tight text-[#9ca3af] transition group-hover:text-gray-700"
              >
                {{ post.title }}
              </h2>

              <p
                v-if="post.excerpt"
                class="mt-5 font-sans text-base leading-relaxed text-gray-600"
              >
                {{ post.excerpt }}
              </p>

              <div
                class="mt-8 font-display text-sm font-extrabold uppercase tracking-wide text-[#9ca3af] transition group-hover:text-gray-700"
              >
                Read more »
              </div>

              <div class="mt-8 border-t border-gray-100 pt-5 font-sans text-sm text-gray-400">
                {{ formatDate(post.published_at) }}
              </div>
            </div>
          </Link>
        </div>

        <div v-if="posts.links?.length" class="mt-12">
          <div class="flex flex-wrap gap-2">
            <template v-for="link in posts.links" :key="link.label">
              <span
                v-if="!link.url"
                class="rounded-md px-3 py-2 text-sm text-gray-400"
                v-html="link.label"
              />
              <Link
                v-else
                :href="link.url"
                class="rounded-md px-3 py-2 text-sm transition"
                :class="link.active ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                v-html="link.label"
                preserve-scroll
              />
            </template>
          </div>
        </div>
      </section>
    </div>
  </FrontLayout>
</template>
