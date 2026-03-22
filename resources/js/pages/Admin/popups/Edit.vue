<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/AppLayouts/AdminLayout.vue'
import PopupForm from './Form.vue'

type PopupData = {
  id: number
  name: string
  slug: string
  type: 'general' | 'buyer' | 'seller' | 'consultation' | 'resource'
  role: 'primary' | 'fallback' | 'standard'
  priority: number
  is_active: boolean
  eyebrow: string
  headline: string
  body: string
  cta_text: string
  success_message: string
  layout: 'centered' | 'split' | 'banner'
  trigger_type: 'time' | 'scroll' | 'exit' | 'click'
  trigger_delay: number | null
  trigger_scroll: number | null
  target_pages: string[]
  device: 'all' | 'desktop' | 'mobile'
  frequency: 'once_session' | 'once_day' | 'always'
  audience: 'everyone' | 'guests' | 'authenticated'
  suppress_if_lead_captured: boolean
  suppression_scope: 'this_popup_only' | 'all_lead_popups'
  form_fields: string[]
  lead_type: 'general' | 'buyer' | 'seller'
  post_submit_action: 'message' | 'redirect'
  post_submit_redirect_url: string | null
}

defineProps<{
  popup: PopupData
}>()
</script>

<template>
  <AdminLayout>
    <Head title="Edit Popup" />

    <div class="space-y-6">
      <section class="rounded-3xl border border-gray-200/80 bg-white px-6 py-6 shadow-sm sm:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
          <div class="space-y-3">
            <div class="inline-flex w-fit items-center rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-gray-600">
              Popup Builder
            </div>

            <div>
              <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Edit Popup</h1>
              <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600 sm:text-base">
                Refine content, audience rules, suppression behavior, and post-submit flow without breaking the funnel.
              </p>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-3">
            <Link
              :href="route('admin.popups.index')"
              class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
              Back to Popups
            </Link>
          </div>
        </div>
      </section>

      <PopupForm mode="edit" :popup="popup" />
    </div>
  </AdminLayout>
</template>
