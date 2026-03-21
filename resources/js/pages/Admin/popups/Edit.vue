<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
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
      <div>
        <h1 class="text-3xl font-semibold text-gray-900">Edit Popup</h1>
        <p class="mt-2 text-gray-600">
          Update popup settings, audience rules, suppression, and submit behavior.
        </p>
      </div>

      <PopupForm mode="edit" :popup="popup" />
    </div>
  </AdminLayout>
</template>
