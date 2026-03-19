<script setup lang="ts">
import FrontLayout from '@/layouts/FrontLayout.vue'
</script>

<template>
  <FrontLayout>
    <div class="max-w-5xl mx-auto px-6 py-16 space-y-12">
      <section class="space-y-4">
        <h1 class="text-4xl font-bold">Resources</h1>
        <p class="text-lg text-gray-600 max-w-3xl">
          Explore helpful real estate insights, guides, and educational content
          designed to help you make informed property decisions.
        </p>
      </section>

      <section class="grid gap-6 md:grid-cols-3">
        <div class="rounded-lg border bg-white p-6">
          <h2 class="text-xl font-semibold">Buying Guidance</h2>
          <p class="mt-2 text-sm text-gray-600">
            Helpful information for buyers preparing for their next move.
          </p>
        </div>

        <div class="rounded-lg border bg-white p-6">
          <h2 class="text-xl font-semibold">Selling Advice</h2>
          <p class="mt-2 text-sm text-gray-600">
            Resources for homeowners planning to position and sell effectively.
          </p>
        </div>

        <div class="rounded-lg border bg-white p-6">
          <h2 class="text-xl font-semibold">Market Insights</h2>
          <p class="mt-2 text-sm text-gray-600">
            Articles and updates that help you understand what is happening in
            the market.
          </p>
        </div>
      </section>
    </div>
  </FrontLayout>
</template>
