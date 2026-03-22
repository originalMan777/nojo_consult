<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/AppLayouts/AdminLayout.vue'

type PopupRow = {
  id: number
  name: string
  slug: string
  type: string
  role: string
  priority: number
  audience: string
  is_active: boolean
  headline: string
  layout: string
  trigger_type: string
  target_pages: string[]
  suppress_if_lead_captured: boolean
  suppression_scope: string
  post_submit_action: string
  updated_at: string | null
}

const props = defineProps<{
  popups: PopupRow[]
}>()

const destroyPopup = (id: number) => {
  if (!window.confirm('Delete this popup?')) return
  router.delete(`/admin/popups/${id}`)
}
</script>

<template>
  <AdminLayout>
    <Head title="Popups" />

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-semibold text-gray-900">Popups</h1>
          <p class="mt-2 text-gray-600">
            Control popup content, sequence, audience, suppression, and post-submit behavior.
          </p>
        </div>

        <Link href="/admin/popups/create" class="inline-flex rounded-lg bg-gray-900 px-5 py-3 text-sm font-medium text-white hover:bg-gray-800">
          Create Popup
        </Link>
      </div>

      <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
        <div v-if="props.popups.length === 0" class="p-8 text-gray-600">
          No popups created yet.
        </div>

        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Popup</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Role / Priority</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Audience</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Trigger</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Pages</th>
              <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Suppression</th>
              <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200">
            <tr v-for="popup in props.popups" :key="popup.id">
              <td class="px-6 py-4 align-top">
                <div class="flex items-center gap-3">
                  <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium" :class="popup.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'">
                    {{ popup.is_active ? 'Active' : 'Inactive' }}
                  </span>
                  <div class="font-medium text-gray-900">{{ popup.name }}</div>
                </div>
                <div class="mt-1 text-sm text-gray-500">{{ popup.headline }}</div>
                <div class="mt-1 text-xs text-gray-400">{{ popup.slug }}</div>
              </td>

              <td class="px-6 py-4 text-sm text-gray-700">
                <div class="font-medium text-gray-900">{{ popup.role }}</div>
                <div class="text-gray-500">Priority {{ popup.priority }}</div>
              </td>

              <td class="px-6 py-4 text-sm text-gray-700">
                <div>{{ popup.audience }}</div>
                <div class="text-gray-500">{{ popup.type }}</div>
              </td>

              <td class="px-6 py-4 text-sm text-gray-700">
                <div>{{ popup.trigger_type }}</div>
                <div class="text-gray-500">{{ popup.post_submit_action }}</div>
              </td>

              <td class="px-6 py-4 text-sm text-gray-700">
                <div v-if="popup.target_pages?.length" class="flex flex-wrap gap-2">
                  <span v-for="page in popup.target_pages" :key="page" class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-700">
                    {{ page }}
                  </span>
                </div>
                <span v-else class="text-gray-400">—</span>
              </td>

              <td class="px-6 py-4 text-sm text-gray-700">
                <div>{{ popup.suppression_scope }}</div>
                <div class="text-gray-500">
                  {{ popup.suppress_if_lead_captured ? 'Blocks after lead captured' : 'Does not block after lead captured' }}
                </div>
              </td>

              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center gap-3">
                  <Link :href="`/admin/popups/${popup.id}/edit`" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                    Edit
                  </Link>

                  <button type="button" class="text-sm font-medium text-red-600 hover:text-red-700" @click="destroyPopup(popup.id)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
