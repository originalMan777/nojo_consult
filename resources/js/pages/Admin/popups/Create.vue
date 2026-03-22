<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/AppLayouts/AdminLayout.vue'
import PopupForm from './Form.vue'
</script>

<template>
  <AdminLayout>
    <Head title="Create Popup" />

    <div class="space-y-6">
      <section class="rounded-3xl border border-gray-200/80 bg-white px-6 py-6 shadow-sm sm:px-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
          <div class="space-y-3">
            <div class="inline-flex w-fit items-center rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] text-gray-600">
              Popup Builder
            </div>

            <div>
              <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Create Popup</h1>
              <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600 sm:text-base">
                Build a new popup campaign with clean targeting, trigger logic, and lead-capture behavior.
                Keep the structure tight so the popup is easy to manage later from admin.
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

      <PopupForm mode="create" />
    </div>
  </AdminLayout>
</template>
