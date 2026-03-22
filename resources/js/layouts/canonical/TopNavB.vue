<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const currentUrl = computed(() => String(page.url || '').toLowerCase())

const currentSection = computed(() => {
  const url = currentUrl.value

  if (url.startsWith('/admin/posts')) {
    return {
      title: 'Posts',
      description: 'Manage blog posts, drafts, and publishing.',
    }
  }

  if (url.startsWith('/admin/categories')) {
    return {
      title: 'Categories',
      description: 'Organize blog categories and content structure.',
    }
  }

  if (url.startsWith('/admin/tags')) {
    return {
      title: 'Tags',
      description: 'Manage tagging for filtering and discovery.',
    }
  }

  if (url.startsWith('/admin/media')) {
    return {
      title: 'Media Library',
      description: 'Browse, upload, and manage site media.',
    }
  }

  if (url.startsWith('/admin/popups')) {
    return {
      title: 'Popups',
      description: 'Manage popup content, targeting, and lead capture.',
    }
  }

  return {
    title: 'Dashboard',
    description: 'Admin overview and system navigation.',
  }
})

const quickLinks = [
  { label: 'Posts', href: '/admin/posts' },
  { label: 'Categories', href: '/admin/categories' },
  { label: 'Tags', href: '/admin/tags' },
  { label: 'Media', href: '/admin/media' },
  { label: 'Popups', href: '/admin/popups' },
]

const isActive = (href: string) => currentUrl.value.startsWith(href.toLowerCase())
</script>

<template>
  <div class="border-b border-gray-200 bg-white px-4 py-3">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <div class="min-w-0">
        <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">
          Admin
        </div>
        <div class="mt-1 flex min-w-0 items-baseline gap-3">
          <h2 class="truncate text-base font-semibold text-gray-900">
            {{ currentSection.title }}
          </h2>
          <p class="hidden truncate text-sm text-gray-500 md:block">
            {{ currentSection.description }}
          </p>
        </div>
      </div>

      <nav class="flex flex-wrap items-center gap-2 text-sm">
        <Link
          v-for="item in quickLinks"
          :key="item.href"
          :href="item.href"
          class="rounded-md px-3 py-1.5 transition"
          :class="isActive(item.href)
            ? 'bg-gray-900 text-white'
            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
        >
          {{ item.label }}
        </Link>
      </nav>
    </div>
  </div>
</template>
