<template>
  <div class="qbar flex h-full w-full flex-col bg-white">
    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-2.5">
      <div>
        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
          Domain Queue
        </div>
        <div class="mt-1 text-sm font-medium text-gray-800">
          {{ recentDomains.length > 0 ? 'Recent domains' : 'All admin domains' }}
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div class="text-xs text-gray-500">
          <span v-if="isLoading">Refreshing…</span>
          <span v-else>{{ displayDomains.length }} tile{{ displayDomains.length === 1 ? '' : 's' }}</span>
        </div>

        <button
          type="button"
          class="inline-flex items-center rounded-md border border-gray-300 px-2.5 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50"
          @click="$emit('toggle')"
        >
          {{ open ? 'Collapse' : 'Expand' }}
        </button>
      </div>
    </div>

    <div v-if="open" class="min-h-0 flex-1 overflow-auto px-4 py-4">
      <div
        v-if="displayDomains.length === 0"
        class="rounded-lg border border-dashed border-gray-300 px-4 py-5 text-sm text-gray-500"
      >
        No domains available yet.
      </div>

      <div v-else class="flex flex-wrap gap-3">
        <component
          :is="item.href ? Link : 'div'"
          v-for="(item, index) in displayDomains"
          :key="item.key"
          :href="item.href || undefined"
          class="group relative flex h-20 w-40 flex-col justify-between rounded-xl border px-3 py-3 text-left transition"
          :class="item.key === currentDomain
            ? 'border-black bg-gray-900 text-white'
            : 'border-gray-300 bg-gray-50 text-gray-800 hover:border-gray-400 hover:bg-white'"
        >
          <div class="flex items-start justify-between gap-3">
            <span
              class="inline-flex h-6 min-w-6 items-center justify-center rounded-md px-1.5 text-[11px] font-semibold"
              :class="item.key === currentDomain
                ? 'bg-white/15 text-white'
                : 'bg-white text-gray-700 ring-1 ring-gray-200'"
            >
              {{ recentDomains.length > 0 ? index + 1 : item.badge }}
            </span>

            <span
              v-if="item.key === currentDomain"
              class="text-[10px] font-semibold uppercase tracking-[0.18em]"
            >
              Current
            </span>
          </div>

          <div>
            <div class="text-sm font-semibold leading-tight">
              {{ item.label }}
            </div>
            <div class="mt-1 text-[11px] uppercase tracking-[0.16em] opacity-70">
              {{ item.key }}
            </div>
          </div>
        </component>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, onMounted, ref, watch } from 'vue'

type QBarDomain = {
  key: string
  label: string
  badge: string
  href: string | null
}

type QBarPayload = {
  currentDomain: string | null
  recentDomains: QBarDomain[]
  availableDomains: QBarDomain[]
}

const fallbackDomains: QBarDomain[] = [
  { key: 'dashboard', label: 'Dashboard', badge: 'DB', href: '/admin' },
  { key: 'posts', label: 'Posts', badge: 'PO', href: '/admin/posts' },
  { key: 'categories', label: 'Categories', badge: 'CA', href: '/admin/categories' },
  { key: 'tags', label: 'Tags', badge: 'TG', href: '/admin/tags' },
  { key: 'media', label: 'Media', badge: 'ME', href: '/admin/media' },
  { key: 'popups', label: 'Popups', badge: 'PU', href: '/admin/popups' },
]

defineProps<{
  open: boolean
}>()

defineEmits<{
  (e: 'toggle'): void
}>()

const page = usePage()
const isLoading = ref(false)
const currentDomain = ref<string>('dashboard')
const recentDomains = ref<QBarDomain[]>([])
const availableDomains = ref<QBarDomain[]>(fallbackDomains)

const displayDomains = computed(() => {
  return recentDomains.value.length > 0 ? recentDomains.value : availableDomains.value
})

function inferDomainFromUrl(url: string): string {
  if (url === '/admin' || url === '/admin/') return 'dashboard'
  if (url.startsWith('/admin/posts')) return 'posts'
  if (url.startsWith('/admin/categories')) return 'categories'
  if (url.startsWith('/admin/tags')) return 'tags'
  if (url.startsWith('/admin/media')) return 'media'
  if (url.startsWith('/admin/popups')) return 'popups'
  if (url.startsWith('/admin/coming-soon')) return 'dashboard'
  return 'dashboard'
}

async function fetchQueue() {
  const url = String(page.url || '')
  currentDomain.value = inferDomainFromUrl(url)
  availableDomains.value = fallbackDomains
  isLoading.value = true

  try {
    const response = await fetch(`/admin/qbar?domain=${encodeURIComponent(currentDomain.value)}`, {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!response.ok) {
      throw new Error('Failed to load Q-Bar data')
    }

    const data = (await response.json()) as QBarPayload

    recentDomains.value = Array.isArray(data.recentDomains) ? data.recentDomains : []
    availableDomains.value = Array.isArray(data.availableDomains) && data.availableDomains.length > 0
      ? data.availableDomains
      : fallbackDomains
    currentDomain.value = data.currentDomain || currentDomain.value
  } catch {
    recentDomains.value = []
    availableDomains.value = fallbackDomains
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchQueue)
watch(() => page.url, fetchQueue)
</script>
