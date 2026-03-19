<template>
  <div class="qbar w-full border-t border-gray-200 bg-white">
    <div class="flex items-center justify-between px-4 py-2">
      <div class="flex items-center gap-3">
        <div class="text-xs font-semibold text-gray-700">Q-Bar</div>

        <button
          type="button"
          @click="toggleMode"
          class="text-xs text-gray-600 hover:text-black"
        >
          Mode: {{ mode === 'domain' ? 'Domains' : 'Tracks' }}
        </button>
      </div>

      <div v-if="isLoading" class="text-xs text-gray-500">Loading…</div>
    </div>

    <div class="flex gap-4 px-4 pb-4">
      <!-- Domain selector (left) -->
      <div class="flex flex-col gap-2">
        <button
          v-for="d in domains"
          :key="d.key"
          type="button"
          class="h-14 w-24 rounded-md border text-sm font-bold"
          :class="d.key === domain ? 'border-black bg-white' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
          @click="domain = d.key"
        >
          {{ d.label }}
        </button>
      </div>

      <!-- Tiles (right) -->
      <div class="flex-1">
        <div v-if="items.length === 0" class="py-6 text-sm text-gray-500">
          No tiles yet.
        </div>

        <div v-else class="flex flex-wrap gap-4">
          <component
            :is="item.href ? Link : 'div'"
            v-for="item in items"
            :key="itemKey(item)"
            :href="item.href || undefined"
            class="flex h-24 w-44 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 px-3 text-center font-semibold text-gray-800 hover:bg-gray-200"
          >
            {{ itemLabel(item) }}
          </component>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, ref, watch } from 'vue'

type QBarItem = { id?: string | number; label?: string; name?: string; href?: string | null }

const domains = [
  { key: 'music', label: 'Music' },
  { key: 'pages', label: 'Pages' },
] as const

type DomainKey = (typeof domains)[number]['key']

const page = usePage()
const mode = ref<'domain' | 'song'>('domain')
const domain = ref<DomainKey>('music')
const items = ref<QBarItem[]>([])
const isLoading = ref(false)

function inferDomainFromUrl(url: string): DomainKey {
  if (url.startsWith('/admin/pages')) return 'pages'
  if (url.startsWith('/admin/music')) return 'music'
  return 'music'
}

function itemLabel(item: QBarItem) {
  return item.label ?? item.name ?? '(item)'
}

function itemKey(item: QBarItem) {
  return item.id ?? item.label ?? item.name ?? Math.random()
}

async function fetchItems() {
  isLoading.value = true
  try {
    const res = await axios.get('/admin/qbar', {
      params: { mode: mode.value, domain: domain.value, url: String(page.url || '') },
    })
    items.value = Array.isArray(res.data) ? res.data : []
  } catch {
    items.value = []
  } finally {
    isLoading.value = false
  }
}

function toggleMode() {
  mode.value = mode.value === 'domain' ? 'song' : 'domain'
}

onMounted(() => {
  domain.value = inferDomainFromUrl(String(page.url || ''))
  fetchItems()
})

watch([mode, domain], fetchItems)
</script>
