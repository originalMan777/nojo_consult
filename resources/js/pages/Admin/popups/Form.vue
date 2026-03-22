<script setup lang="ts">
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

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

const targetPageOptions = ['home', 'about', 'services', 'buyers', 'sellers', 'consultation', 'resources', 'contact', 'blog'] as const
const formFieldOptions = ['name', 'email', 'phone', 'message'] as const

const inputClass =
  'w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-300 focus:ring-4 focus:ring-gray-100'
const tagButtonBaseClass =
  'rounded-full border px-4 py-2 text-sm font-medium transition'

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

const submitLabel = computed(() => (props.mode === 'create' ? 'Create Popup' : 'Save Changes'))
const statusLabel = computed(() => (form.is_active ? 'Active' : 'Inactive'))
const selectedTargetPages = computed(() => (form.target_pages.length ? form.target_pages.join(', ') : 'No pages selected yet'))
const selectedFormFields = computed(() => (form.form_fields.length ? form.form_fields.join(', ') : 'No fields selected yet'))
const redirectEnabled = computed(() => form.post_submit_action === 'redirect')

const toggleArrayValue = (arr: string[], value: string) => {
  const idx = arr.indexOf(value)
  if (idx >= 0) {
    arr.splice(idx, 1)
  } else {
    arr.push(value)
  }
}

const buttonClassFor = (list: string[], value: string) => {
  return list.includes(value)
    ? `${tagButtonBaseClass} border-gray-900 bg-gray-900 text-white shadow-sm`
    : `${tagButtonBaseClass} border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50`
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
  <form class="grid gap-6 xl:grid-cols-[minmax(0,1.08fr)_380px]" @submit.prevent="submit">
    <div class="space-y-6">
      <section class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Popup Identity</p>
          <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Core setup</h2>
          <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">
            Define how this popup is named, prioritized, and recognized inside the admin system.
          </p>
        </div>

        <div class="grid gap-5 px-6 py-6 sm:px-8 md:grid-cols-2">
          <div class="space-y-2 md:col-span-2">
            <label class="text-sm font-medium text-gray-900">Name</label>
            <input v-model="form.name" type="text" :class="inputClass" placeholder="Home Seller Lead Popup" />
            <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Slug</label>
            <input v-model="form.slug" type="text" :class="inputClass" placeholder="home-seller-lead-popup" />
            <p class="text-xs text-gray-500">Leave this clean and predictable for admin reference.</p>
            <p v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Type</label>
            <select v-model="form.type" :class="inputClass">
              <option value="general">General</option>
              <option value="buyer">Buyer</option>
              <option value="seller">Seller</option>
              <option value="consultation">Consultation</option>
              <option value="resource">Resource</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Role</label>
            <select v-model="form.role" :class="inputClass">
              <option value="primary">Primary</option>
              <option value="fallback">Fallback</option>
              <option value="standard">Standard</option>
            </select>
            <p v-if="form.errors.role" class="text-sm text-red-600">{{ form.errors.role }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Priority</label>
            <input v-model="form.priority" type="number" min="1" :class="inputClass" />
            <p class="text-xs text-gray-500">Lower numbers win first when multiple popups qualify.</p>
            <p v-if="form.errors.priority" class="text-sm text-red-600">{{ form.errors.priority }}</p>
          </div>

          <div class="rounded-2xl border border-gray-200 bg-gray-50/80 px-4 py-4 md:col-span-2">
            <label class="inline-flex items-center gap-3">
              <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-300" />
              <span class="text-sm font-medium text-gray-900">Popup is active</span>
            </label>
            <p class="mt-2 text-xs leading-5 text-gray-500">
              Active popups can be matched and served on the front end if their audience, page, and trigger conditions are met.
            </p>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Content</p>
          <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Visual and messaging layer</h2>
          <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">
            Build the content the visitor will actually see, from eyebrow through success messaging.
          </p>
        </div>

        <div class="space-y-5 px-6 py-6 sm:px-8">
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Eyebrow</label>
            <input v-model="form.eyebrow" type="text" :class="inputClass" placeholder="Free Checklist" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Headline</label>
            <input v-model="form.headline" type="text" :class="inputClass" placeholder="Get the seller consultation checklist" />
            <p v-if="form.errors.headline" class="text-sm text-red-600">{{ form.errors.headline }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Body</label>
            <textarea v-model="form.body" rows="5" :class="inputClass" placeholder="Explain the offer, what they get, and why the popup matters." />
          </div>

          <div class="grid gap-5 md:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-900">CTA Text</label>
              <input v-model="form.cta_text" type="text" :class="inputClass" placeholder="Get Started" />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-gray-900">Success Message</label>
              <input v-model="form.success_message" type="text" :class="inputClass" placeholder="Thanks. Check your inbox for the next step." />
            </div>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Behavior</p>
          <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Trigger and delivery</h2>
          <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">
            Control how the popup appears and how often the visitor is allowed to see it.
          </p>
        </div>

        <div class="grid gap-5 px-6 py-6 sm:px-8 md:grid-cols-2">
          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Layout</label>
            <select v-model="form.layout" :class="inputClass">
              <option value="centered">Centered</option>
              <option value="split">Split</option>
              <option value="banner">Banner</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Trigger Type</label>
            <select v-model="form.trigger_type" :class="inputClass">
              <option value="time">Time</option>
              <option value="scroll">Scroll</option>
              <option value="exit">Exit</option>
              <option value="click">Click</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Trigger Delay (seconds)</label>
            <input v-model="form.trigger_delay" type="number" min="0" :class="inputClass" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Trigger Scroll (%)</label>
            <input v-model="form.trigger_scroll" type="number" min="0" max="100" :class="inputClass" />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Device</label>
            <select v-model="form.device" :class="inputClass">
              <option value="all">All</option>
              <option value="desktop">Desktop</option>
              <option value="mobile">Mobile</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Frequency</label>
            <select v-model="form.frequency" :class="inputClass">
              <option value="once_session">Once per session</option>
              <option value="once_day">Once per day</option>
              <option value="always">Always</option>
            </select>
          </div>
        </div>
      </section>
    </div>

    <div class="space-y-6">
      <section class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Targeting & Audience</p>
          <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Who should see this</h2>
        </div>

        <div class="space-y-6 px-6 py-6">
          <div>
            <label class="text-sm font-medium text-gray-900">Target Pages</label>
            <p class="mt-1 text-sm leading-6 text-gray-600">
              Pick where this popup is eligible to appear. Keep the targeting narrow when the message is specific.
            </p>

            <div class="mt-4 flex flex-wrap gap-3">
              <button
                v-for="page in targetPageOptions"
                :key="page"
                type="button"
                :class="buttonClassFor(form.target_pages, page)"
                @click="toggleArrayValue(form.target_pages, page)"
              >
                {{ page }}
              </button>
            </div>
            <p class="mt-3 text-xs text-gray-500">Selected: {{ selectedTargetPages }}</p>
            <p v-if="form.errors.target_pages" class="mt-2 text-sm text-red-600">{{ form.errors.target_pages }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Audience</label>
            <select v-model="form.audience" :class="inputClass">
              <option value="everyone">Everyone</option>
              <option value="guests">Guests only</option>
              <option value="authenticated">Logged-in users only</option>
            </select>
          </div>

          <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-4">
            <label class="inline-flex items-center gap-3">
              <input v-model="form.suppress_if_lead_captured" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-300" />
              <span class="text-sm font-medium text-gray-900">Suppress after any lead is captured</span>
            </label>

            <div class="mt-4 space-y-2">
              <label class="text-sm font-medium text-gray-900">Suppression Scope</label>
              <select v-model="form.suppression_scope" :class="inputClass">
                <option value="all_lead_popups">All lead popups</option>
                <option value="this_popup_only">This popup only</option>
              </select>
            </div>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Form & Submit Flow</p>
          <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Lead capture behavior</h2>
        </div>

        <div class="space-y-6 px-6 py-6">
          <div>
            <label class="text-sm font-medium text-gray-900">Form Fields</label>
            <p class="mt-1 text-sm leading-6 text-gray-600">
              Select only the fields needed for this popup’s purpose.
            </p>

            <div class="mt-4 flex flex-wrap gap-3">
              <button
                v-for="field in formFieldOptions"
                :key="field"
                type="button"
                :class="buttonClassFor(form.form_fields, field)"
                @click="toggleArrayValue(form.form_fields, field)"
              >
                {{ field }}
              </button>
            </div>
            <p class="mt-3 text-xs text-gray-500">Selected: {{ selectedFormFields }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Lead Type</label>
            <select v-model="form.lead_type" :class="inputClass">
              <option value="general">General</option>
              <option value="buyer">Buyer</option>
              <option value="seller">Seller</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Post-submit Action</label>
            <select v-model="form.post_submit_action" :class="inputClass">
              <option value="message">Show success message</option>
              <option value="redirect">Redirect visitor</option>
            </select>
          </div>

          <div v-if="redirectEnabled" class="space-y-2">
            <label class="text-sm font-medium text-gray-900">Redirect URL</label>
            <input v-model="form.post_submit_redirect_url" type="text" :class="inputClass" placeholder="/consultation or https://..." />
            <p v-if="form.errors.post_submit_redirect_url" class="text-sm text-red-600">{{ form.errors.post_submit_redirect_url }}</p>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-sm xl:sticky xl:top-6">
        <div class="border-b border-gray-100 px-6 py-5">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Summary</p>
          <h2 class="mt-2 text-xl font-semibold tracking-tight text-gray-900">Review before saving</h2>
        </div>

        <div class="space-y-4 px-6 py-6 text-sm text-gray-600">
          <div class="grid gap-3 rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
            <div class="flex items-center justify-between gap-4">
              <span class="font-medium text-gray-900">Status</span>
              <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-gray-700 ring-1 ring-gray-200">{{ statusLabel }}</span>
            </div>
            <div class="flex items-center justify-between gap-4">
              <span class="font-medium text-gray-900">Role</span>
              <span>{{ form.role }}</span>
            </div>
            <div class="flex items-center justify-between gap-4">
              <span class="font-medium text-gray-900">Priority</span>
              <span>{{ form.priority }}</span>
            </div>
            <div class="flex items-center justify-between gap-4">
              <span class="font-medium text-gray-900">Audience</span>
              <span>{{ form.audience }}</span>
            </div>
            <div class="flex items-center justify-between gap-4">
              <span class="font-medium text-gray-900">Lead Type</span>
              <span>{{ form.lead_type }}</span>
            </div>
          </div>

          <div class="rounded-2xl border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-900">Selected Pages</p>
            <p class="mt-2 text-sm leading-6 text-gray-600">{{ selectedTargetPages }}</p>
          </div>

          <div class="rounded-2xl border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-900">Selected Fields</p>
            <p class="mt-2 text-sm leading-6 text-gray-600">{{ selectedFormFields }}</p>
          </div>

          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button
              type="submit"
              class="inline-flex items-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Saving…' : submitLabel }}
            </button>

            <Link
              :href="route('admin.popups.index')"
              class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
              Cancel
            </Link>
          </div>
        </div>
      </section>
    </div>
  </form>
</template>
