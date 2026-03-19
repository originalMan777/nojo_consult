<script setup lang="ts">
import FrontLayout from '@/layouts/FrontLayout.vue'
</script>

<template>
  <FrontLayout>
    <div class="max-w-5xl mx-auto px-6 py-16 space-y-12">
      <section class="space-y-4">
        <h1 class="text-4xl font-bold">About Nojo</h1>
        <p class="text-lg text-gray-600 max-w-3xl">
          Nojo provides clear, professional real estate consultation for buyers,
          sellers, and investors who want to make informed decisions with more
          confidence.
        </p>
      </section>

      <section class="grid gap-8 md:grid-cols-2">
        <div class="space-y-3">
          <h2 class="text-2xl font-semibold">Our Approach</h2>
          <p class="text-gray-600">
            We believe real estate decisions should be made with clarity,
            strategy, and the right information. Our role is to help clients
            understand their options and move forward with direction.
          </p>
        </div>

        <div class="space-y-3">
          <h2 class="text-2xl font-semibold">Who We Help</h2>
          <p class="text-gray-600">
            We work with first-time home buyers, homeowners preparing to sell,
            investors evaluating opportunities, and people who simply need
            trusted guidance before making their next move.
          </p>
        </div>
      </section>

      <section class="space-y-4">
        <h2 class="text-2xl font-semibold">Why Nojo</h2>
        <div class="grid gap-4 md:grid-cols-3">
          <div class="rounded-lg border bg-white p-5">
            <h3 class="font-semibold">Clear Guidance</h3>
            <p class="mt-2 text-sm text-gray-600">
              Straightforward advice without confusion or pressure.
            </p>
          </div>

          <div class="rounded-lg border bg-white p-5">
            <h3 class="font-semibold">Strategic Thinking</h3>
            <p class="mt-2 text-sm text-gray-600">
              Focused on helping you understand your next best step.
            </p>
          </div>

          <div class="rounded-lg border bg-white p-5">
            <h3 class="font-semibold">Client-Focused</h3>
            <p class="mt-2 text-sm text-gray-600">
              Guidance shaped around your actual situation and goals.
            </p>
          </div>
        </div>
      </section>
    </div>
  </FrontLayout>
</template>
