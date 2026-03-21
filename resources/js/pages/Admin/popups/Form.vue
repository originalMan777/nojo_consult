<script setup lang="ts">
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'

type PopupFormData = {
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
  post_submit_redirect_url: string
}

const props = defineProps<{
  mode: 'create' | 'edit'
  popup?: Partial<PopupFormData> & { id?: number }
}>()

const form = useForm<PopupFormData>({
  name: props.popup?.name ?? '',
  slug: props.popup?.slug ?? '',
  type: (props.popup?.type as PopupFormData['type']) ?? 'general',
  role: (props.popup?.role as PopupFormData['role']) ?? 'standard',
  priority: props.popup?.priority ?? 100,
  is_active: props.popup?.is_active ?? true,
  eyebrow: props.popup?.eyebrow ?? '',
  headline: props.popup?.headline ?? '',
  body: props.popup?.body ?? '',
  cta_text: props.popup?.cta_text ?? 'Get Started',
  success_message: props.popup?.success_message ?? '',
  layout: (props.popup?.layout as PopupFormData['layout']) ?? 'centered',
  trigger_type: (props.popup?.trigger_type as PopupFormData['trigger_type']) ?? 'time',
  trigger_delay: props.popup?.trigger_delay ?? 5,
  trigger_scroll: props.popup?.trigger_scroll ?? 50,
  target_pages: props.popup?.target_pages ?? [],
  device: (props.popup?.device as PopupFormData['device']) ?? 'all',
  frequency: (props.popup?.frequency as PopupFormData['frequency']) ?? 'once_session',
  audience: (props.popup?.audience as PopupFormData['audience']) ?? 'guests',
  suppress_if_lead_captured: props.popup?.suppress_if_lead_captured ?? true,
  suppression_scope: (props.popup?.suppression_scope as PopupFormData['suppression_scope']) ?? 'all_lead_popups',
  form_fields: props.popup?.form_fields ?? ['name', 'email'],
  lead_type: (props.popup?.lead_type as PopupFormData['lead_type']) ?? 'general',
  post_submit_action: (props.popup?.post_submit_action as PopupFormData['post_submit_action']) ?? 'message',
  post_submit_redirect_url: props.popup?.post_submit_redirect_url ?? '',
})

const submitLabel = computed(() => props.mode === 'create' ? 'Create Popup' : 'Save Changes')

const toggleArrayValue = (arr: string[], value: string) => {
  const idx = arr.indexOf(value)
  if (idx >= 0) {
    arr.splice(idx, 1)
  } else {
    arr.push(value)
  }
}

const submit = () => {
  if (props.mode === 'create') {
    form.post('/admin/popups')
    return
  }

  form.put(`/admin/popups/${props.popup?.id}`)
}
</script>

<template>
  <form class="grid gap-8 xl:grid-cols-[1.08fr_0.92fr]" @submit.prevent="submit">
    <div class="space-y-8">
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="text-xl font-semibold text-gray-900">Popup Identity</h2>

        <div class="mt-6 grid gap-5 md:grid-cols-2">
          <div class="space-y-2 md:col-span-2">
            <label class="text-sm font-medium text-gray-900">Name</label>
            <input v-model="form.name" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
            <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Slug</label>
            <input v-model="form.slug" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
            <p v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Type</label>
            <select v-model="form.type" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="general">General</option>
              <option value="buyer">Buyer</option>
              <option value="seller">Seller</option>
              <option value="consultation">Consultation</option>
              <option value="resource">Resource</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Role</label>
            <select v-model="form.role" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="primary">Primary</option>
              <option value="fallback">Fallback</option>
              <option value="standard">Standard</option>
            </select>
            <p v-if="form.errors.role" class="text-sm text-red-600">{{ form.errors.role }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Priority</label>
            <input v-model="form.priority" type="number" min="1" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
            <p class="text-xs text-gray-500">Lower numbers win first.</p>
            <p v-if="form.errors.priority" class="text-sm text-red-600">{{ form.errors.priority }}</p>
          </div>

          <div class="space-y-2 md:col-span-2">
            <label class="inline-flex items-center gap-3">
              <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
              <span class="text-sm font-medium text-gray-900">Active</span>
            </label>
          </div>
        </div>
      </section>

      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="text-xl font-semibold text-gray-900">Content</h2>

        <div class="mt-6 space-y-5">
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Eyebrow</label>
            <input v-model="form.eyebrow" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Headline</label>
            <input v-model="form.headline" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
            <p v-if="form.errors.headline" class="text-sm text-red-600">{{ form.errors.headline }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Body</label>
            <textarea v-model="form.body" rows="5" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
          </div>

          <div class="grid gap-5 md:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-900">CTA Text</label>
              <input v-model="form.cta_text" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-900">Success Message</label>
              <input v-model="form.success_message" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
            </div>
          </div>
        </div>
      </section>

      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="text-xl font-semibold text-gray-900">Behavior</h2>

        <div class="mt-6 grid gap-5 md:grid-cols-2">
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Layout</label>
            <select v-model="form.layout" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="centered">Centered</option>
              <option value="split">Split</option>
              <option value="banner">Banner</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Trigger Type</label>
            <select v-model="form.trigger_type" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="time">Time</option>
              <option value="scroll">Scroll</option>
              <option value="exit">Exit</option>
              <option value="click">Click</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Trigger Delay (seconds)</label>
            <input v-model="form.trigger_delay" type="number" min="0" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Trigger Scroll (%)</label>
            <input v-model="form.trigger_scroll" type="number" min="0" max="100" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Device</label>
            <select v-model="form.device" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="all">All</option>
              <option value="desktop">Desktop</option>
              <option value="mobile">Mobile</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Frequency</label>
            <select v-model="form.frequency" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="once_session">Once per session</option>
              <option value="once_day">Once per day</option>
              <option value="always">Always</option>
            </select>
          </div>
        </div>
      </section>
    </div>

    <div class="space-y-8">
      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="text-xl font-semibold text-gray-900">Targeting & Audience</h2>

        <div class="mt-6 space-y-6">
          <div>
            <label class="text-sm font-medium text-gray-900">Target Pages</label>
            <div class="mt-3 flex flex-wrap gap-3">
              <button v-for="page in ['home', 'about', 'services', 'buyers', 'sellers', 'consultation', 'resources', 'contact', 'blog']" :key="page" type="button" class="rounded-full border px-4 py-2 text-sm transition" :class="form.target_pages.includes(page) ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'" @click="toggleArrayValue(form.target_pages, page)">
                {{ page }}
              </button>
            </div>
            <p v-if="form.errors.target_pages" class="mt-2 text-sm text-red-600">{{ form.errors.target_pages }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Audience</label>
            <select v-model="form.audience" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="everyone">Everyone</option>
              <option value="guests">Guests only</option>
              <option value="authenticated">Logged-in users only</option>
            </select>
          </div>

          <div class="space-y-3 rounded-2xl border border-gray-200 p-4">
            <label class="inline-flex items-center gap-3">
              <input v-model="form.suppress_if_lead_captured" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
              <span class="text-sm font-medium text-gray-900">Suppress after any lead is captured</span>
            </label>

            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-900">Suppression Scope</label>
              <select v-model="form.suppression_scope" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
                <option value="all_lead_popups">All lead popups</option>
                <option value="this_popup_only">This popup only</option>
              </select>
            </div>
          </div>
        </div>
      </section>

      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="text-xl font-semibold text-gray-900">Form & Submit Flow</h2>

        <div class="mt-6 space-y-6">
          <div>
            <label class="text-sm font-medium text-gray-900">Form Fields</label>
            <div class="mt-3 flex flex-wrap gap-3">
              <button v-for="field in ['name', 'email', 'phone', 'message']" :key="field" type="button" class="rounded-full border px-4 py-2 text-sm transition" :class="form.form_fields.includes(field) ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'" @click="toggleArrayValue(form.form_fields, field)">
                {{ field }}
              </button>
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Lead Type</label>
            <select v-model="form.lead_type" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="general">General</option>
              <option value="buyer">Buyer</option>
              <option value="seller">Seller</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Post-submit Action</label>
            <select v-model="form.post_submit_action" class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200">
              <option value="message">Show success message</option>
              <option value="redirect">Redirect visitor</option>
            </select>
          </div>

          <div v-if="form.post_submit_action === 'redirect'" class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Redirect URL</label>
            <input v-model="form.post_submit_redirect_url" type="text" placeholder="/consultation or https://..." class="w-full rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-gray-300 focus:ring-2 focus:ring-gray-200" />
            <p v-if="form.errors.post_submit_redirect_url" class="text-sm text-red-600">{{ form.errors.post_submit_redirect_url }}</p>
          </div>
        </div>
      </section>

      <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
        <h2 class="text-xl font-semibold text-gray-900">Summary</h2>
        <div class="mt-4 space-y-2 text-sm leading-6 text-gray-600">
          <p><span class="font-medium text-gray-900">Role:</span> {{ form.role }}</p>
          <p><span class="font-medium text-gray-900">Priority:</span> {{ form.priority }}</p>
          <p><span class="font-medium text-gray-900">Audience:</span> {{ form.audience }}</p>
          <p><span class="font-medium text-gray-900">Suppression:</span> {{ form.suppression_scope }}</p>
          <p><span class="font-medium text-gray-900">Post-submit:</span> {{ form.post_submit_action }}</p>
        </div>

        <div class="mt-6 flex items-center gap-3">
          <button type="submit" class="inline-flex rounded-lg bg-gray-900 px-5 py-3 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-60" :disabled="form.processing">
            {{ submitLabel }}
          </button>
        </div>
      </section>
    </div>
  </form>
</template>
