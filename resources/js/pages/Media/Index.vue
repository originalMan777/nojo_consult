<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import axios from 'axios'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/AppLayouts/AdminLayout.vue'

type FolderOption = {
  value: string
  label: string
}

type MediaItem = {
  name: string
  filename: string
  folder: string
  path: string
  url: string
  size_kb: number
  modified_at: string
  extension: string
}

type PaginationLink = {
  url: string | null
  label: string
  active: boolean
}

type Pagination<T> = {
  data: T[]
  links: PaginationLink[]
  meta?: {
    current_page?: number
    last_page?: number
    from?: number | null
    to?: number | null
    total?: number
    per_page?: number
  }
}

const props = defineProps<{
  folders: FolderOption[]
  filters: {
    folder: string
    search: string
    per_page: number
  }
  media: Pagination<MediaItem>
}>()

const folder = ref(props.filters.folder ?? 'blog')
const search = ref(props.filters.search ?? '')
const perPage = ref(String(props.filters.per_page ?? 24))
const uploadFolder = ref(props.filters.folder ?? 'blog')
const fileInput = ref<HTMLInputElement | null>(null)
const uploadFile = ref<File | null>(null)
const isUploading = ref(false)
const isDeletingPath = ref<string | null>(null)
const notice = ref<{ type: 'success' | 'error'; text: string } | null>(null)
const previewItem = ref<MediaItem | null>(null)

let searchTimer: number | undefined

const applyFilters = (immediate = false) => {
  if (searchTimer) window.clearTimeout(searchTimer)

  const run = () => {
    router.get(
      route('admin.media.index'),
      {
        folder: folder.value || undefined,
        search: search.value || undefined,
        per_page: perPage.value || undefined,
      },
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      },
    )
  }

  if (immediate) run()
  else searchTimer = window.setTimeout(run, 250)
}

watch(folder, (value) => {
  uploadFolder.value = value
  applyFilters(true)
})

watch(perPage, () => applyFilters(true))

const resetNotice = () => {
  window.setTimeout(() => {
    notice.value = null
  }, 3000)
}

const pickFile = () => {
  fileInput.value?.click()
}

const onFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  uploadFile.value = target.files?.[0] ?? null
}

const uploadImage = async () => {
  if (!uploadFile.value) {
    notice.value = { type: 'error', text: 'Choose an image first.' }
    resetNotice()
    return
  }

  isUploading.value = true
  notice.value = null

  try {
    const form = new FormData()
    form.append('folder', uploadFolder.value)
    form.append('image', uploadFile.value)

    await axios.post(route('admin.media.store'), form, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    uploadFile.value = null
    if (fileInput.value) fileInput.value.value = ''

    notice.value = { type: 'success', text: 'Image uploaded.' }
    resetNotice()
    applyFilters(true)
  } catch (error: any) {
    notice.value = {
      type: 'error',
      text: error?.response?.data?.message || 'Upload failed.',
    }
    resetNotice()
  } finally {
    isUploading.value = false
  }
}

const deleteImage = async (item: MediaItem) => {
  if (!window.confirm(`Delete ${item.filename}?`)) return

  isDeletingPath.value = item.path
  notice.value = null

  try {
    await axios.delete(route('admin.media.destroy'), {
      data: { path: item.path },
    })

    if (previewItem.value?.path === item.path) {
      previewItem.value = null
    }

    notice.value = { type: 'success', text: 'Image deleted.' }
    resetNotice()
    applyFilters(true)
  } catch (error: any) {
    notice.value = {
      type: 'error',
      text: error?.response?.data?.message || 'Delete failed.',
    }
    resetNotice()
  } finally {
    isDeletingPath.value = null
  }
}

const copyUrl = async (item: MediaItem) => {
  const absolute = item.url.startsWith('http')
    ? item.url
    : `${window.location.origin}${item.url}`

  try {
    await navigator.clipboard.writeText(absolute)
    notice.value = { type: 'success', text: 'Image URL copied.' }
    resetNotice()
  } catch {
    notice.value = { type: 'error', text: 'Could not copy URL.' }
    resetNotice()
  }
}

const openPreview = (item: MediaItem) => {
  previewItem.value = item
}

const closePreview = () => {
  previewItem.value = null
}

const formattedRange = computed(() => {
  const meta = props.media.meta
  if (!meta?.total) return '0 images'

  const from = meta.from ?? 0
  const to = meta.to ?? 0
  const total = meta.total

  return `Showing ${from}-${to} of ${total} images`
})

const formatDate = (value: string | null) => {
  if (!value) return '—'
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return value
  return d.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}
</script>

<template>
  <Head title="Media Library" />

  <AdminLayout>
    <div class="h-full p-4">
      <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white">
        <div class="p-6 pb-4">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h2 class="text-2xl font-semibold text-gray-900">Media Library</h2>
              <p class="mt-1 text-sm text-gray-600">
                Upload, browse, and manage site images.
              </p>
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-500">
              <span>{{ formattedRange }}</span>
            </div>
          </div>
        </div>

        <div class="px-6 pb-6">
          <div class="space-y-4 overflow-hidden rounded-2xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 p-4">
              <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:gap-3">
                  <div class="w-full lg:w-64">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                      Folder
                    </label>
                    <select
                      v-model="folder"
                      class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                      <option v-for="option in folders" :key="option.value" :value="option.value">
                        {{ option.label }}
                      </option>
                    </select>
                  </div>

                  <div class="w-full lg:w-72">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                      Search
                    </label>
                    <input
                      v-model="search"
                      type="text"
                      placeholder="Search filename…"
                      class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                      @input="applyFilters(false)"
                    />
                  </div>

                  <div class="w-full lg:w-40">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                      Per page
                    </label>
                    <select
                      v-model="perPage"
                      class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                      <option value="12">12</option>
                      <option value="24">24</option>
                      <option value="48">48</option>
                      <option value="96">96</option>
                    </select>
                  </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                  <div class="mb-3 flex items-start justify-between gap-3">
                    <div>
                      <h3 class="text-sm font-semibold text-gray-900">Upload image</h3>
                      <p class="mt-1 text-xs text-gray-500">
                        Uploads go into the selected folder inside <span class="font-medium">/public/images</span>.
                      </p>
                    </div>
                  </div>

                  <div class="space-y-3">
                    <div>
                      <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Upload folder
                      </label>
                      <select
                        v-model="uploadFolder"
                        class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                      >
                        <option v-for="option in folders" :key="option.value" :value="option.value">
                          {{ option.label }}
                        </option>
                      </select>
                    </div>

                    <div>
                      <input
                        ref="fileInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="onFileChange"
                      />

                      <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        @click="pickFile"
                      >
                        Choose image
                      </button>

                      <p class="mt-2 text-xs text-gray-500">
                        {{ uploadFile ? uploadFile.name : 'No file selected.' }}
                      </p>
                    </div>

                    <div class="flex items-center gap-3">
                      <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="isUploading"
                        @click="uploadImage"
                      >
                        {{ isUploading ? 'Uploading…' : 'Upload' }}
                      </button>

                      <span
                        v-if="notice"
                        class="text-sm"
                        :class="notice.type === 'success' ? 'text-green-700' : 'text-red-600'"
                      >
                        {{ notice.text }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="p-4">
              <div v-if="media.data.length === 0" class="rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center">
                <p class="text-sm font-medium text-gray-700">No images found.</p>
                <p class="mt-1 text-sm text-gray-500">Try another folder, search term, or upload a new image.</p>
              </div>

              <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <div
                  v-for="item in media.data"
                  :key="item.path"
                  class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
                >
                  <button
                    type="button"
                    class="block w-full bg-gray-100"
                    @click="openPreview(item)"
                  >
                    <img
                      :src="item.url"
                      :alt="item.filename"
                      class="h-52 w-full object-cover"
                      loading="lazy"
                      decoding="async"
                    />
                  </button>

                  <div class="space-y-3 p-4">
                    <div>
                      <p class="truncate text-sm font-semibold text-gray-900">{{ item.filename }}</p>
                      <p class="mt-1 text-xs text-gray-500">
                        {{ item.folder === '__root__' ? 'Images Root' : item.folder }}
                        · {{ item.size_kb }} KB
                      </p>
                      <p class="mt-1 text-xs text-gray-500">Updated {{ formatDate(item.modified_at) }}</p>
                    </div>

                    <div class="rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
                      <span class="block truncate">{{ item.path }}</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                      <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                        @click="copyUrl(item)"
                      >
                        Copy URL
                      </button>

                      <a
                        :href="item.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                      >
                        Open
                      </a>

                      <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="isDeletingPath === item.path"
                        @click="deleteImage(item)"
                      >
                        {{ isDeletingPath === item.path ? 'Deleting…' : 'Delete' }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="media.links?.length" class="border-t border-gray-200 p-4">
              <div class="flex flex-wrap gap-1">
                <template v-for="link in media.links" :key="link.label + String(link.url)">
                  <span
                    v-if="!link.url"
                    class="px-2 py-1 text-sm text-gray-400"
                    v-html="link.label"
                  />
                  <Link
                    v-else
                    :href="link.url"
                    class="rounded-md px-2 py-1 text-sm"
                    :class="link.active ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    preserve-scroll
                    preserve-state
                    v-html="link.label"
                  />
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="previewItem"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-4 py-8"
      @click.self="closePreview"
    >
      <div class="w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
          <div>
            <h3 class="text-base font-semibold text-gray-900">{{ previewItem.filename }}</h3>
            <p class="mt-1 text-xs text-gray-500">{{ previewItem.path }}</p>
          </div>

          <button
            type="button"
            class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
            @click="closePreview"
          >
            Close
          </button>
        </div>

        <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_320px]">
          <div class="flex items-center justify-center bg-gray-100 p-4">
            <img :src="previewItem.url" :alt="previewItem.filename" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain" />
          </div>

          <div class="space-y-4 border-l border-gray-200 p-5">
            <div>
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Folder</p>
              <p class="mt-1 text-sm text-gray-900">
                {{ previewItem.folder === '__root__' ? 'Images Root' : previewItem.folder }}
              </p>
            </div>

            <div>
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">URL</p>
              <p class="mt-1 break-all text-sm text-gray-900">{{ previewItem.url }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Extension</p>
                <p class="mt-1 text-sm text-gray-900">{{ previewItem.extension }}</p>
              </div>
              <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Size</p>
                <p class="mt-1 text-sm text-gray-900">{{ previewItem.size_kb }} KB</p>
              </div>
            </div>

            <div>
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Modified</p>
              <p class="mt-1 text-sm text-gray-900">{{ formatDate(previewItem.modified_at) }}</p>
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
              <button
                type="button"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                @click="copyUrl(previewItem)"
              >
                Copy URL
              </button>

              <a
                :href="previewItem.url"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Open in new tab
              </a>

              <button
                type="button"
                class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="isDeletingPath === previewItem.path"
                @click="deleteImage(previewItem)"
              >
                {{ isDeletingPath === previewItem.path ? 'Deleting…' : 'Delete' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
