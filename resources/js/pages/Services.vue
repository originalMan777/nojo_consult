<script setup lang="ts">
import FrontLayout from '@/layouts/FrontLayout.vue'
</script>

<template>
  <FrontLayout>
    <div class="max-w-5xl mx-auto px-6 py-16 space-y-12">
      <section class="space-y-4">
        <h1 class="text-4xl font-bold">Services</h1>
        <p class="text-lg text-gray-600 max-w-3xl">
          Nojo offers real estate consultation and strategy services designed to
          help clients make better decisions before they act.
        </p>
      </section>

      <section class="grid gap-6 md:grid-cols-2">
        <div class="rounded-lg border bg-white p-6 space-y-3">
          <h2 class="text-2xl font-semibold">Consultation</h2>
          <p class="text-gray-600">
            One-on-one guidance tailored to your real estate situation, goals,
            and next steps.
          </p>
        </div>

        <div class="rounded-lg border bg-white p-6 space-y-3">
          <h2 class="text-2xl font-semibold">Buyer Strategy</h2>
          <p class="text-gray-600">
            Clear direction for buyers who want to understand their options
            before making a purchase.
          </p>
        </div>

        <div class="rounded-lg border bg-white p-6 space-y-3">
          <h2 class="text-2xl font-semibold">Seller Strategy</h2>
          <p class="text-gray-600">
            Support for homeowners preparing to price, position, and sell their
            property wisely.
          </p>
        </div>

        <div class="rounded-lg border bg-white p-6 space-y-3">
          <h2 class="text-2xl font-semibold">Market Evaluation</h2>
          <p class="text-gray-600">
            Guidance to help you better understand market conditions and how
            they affect your decision-making.
          </p>
        </div>
      </section>
    </div>
  </FrontLayout>
</template>
