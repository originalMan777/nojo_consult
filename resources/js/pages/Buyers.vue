<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import FrontLayout from '@/layouts/FrontLayout.vue'
</script>

<template>
  <FrontLayout>
    <Head title="Buyers" />

    <section class="rounded-[32px] bg-white p-8 shadow-sm ring-1 ring-black/5 md:p-12">
      <div class="mx-auto max-w-4xl space-y-6">
        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">Buyers</p>
        <h1 class="text-4xl font-semibold tracking-tight text-gray-900">Guidance for buyers who want more clarity before they move</h1>
        <p class="max-w-2xl text-lg leading-relaxed text-gray-600">
          Get a clearer picture of timing, preparation, and next steps before you commit to the buying process.
        </p>

        <div class="flex flex-wrap gap-4">
          <button type="button" data-popup-trigger="current-page" class="rounded-lg bg-gray-900 px-6 py-3 text-sm font-medium text-white hover:bg-gray-800">
            Get Buyer Guidance
          </button>
          <Link href="/consultation" class="rounded-lg border border-gray-200 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Book Consultation
          </Link>
        </div>
      </div>
    </section>
  </FrontLayout>
</template>
