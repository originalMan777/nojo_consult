<script setup lang="ts">
import FrontLayout from '@/layouts/FrontLayout.vue'
</script>

<template>
  <FrontLayout>
    <div class="max-w-4xl mx-auto px-6 py-16 space-y-16">
      <section class="space-y-4 text-center">
        <h1 class="text-4xl font-bold">Schedule a Consultation</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Get clear, professional guidance on buying, selling, or understanding
          your next property decision.
        </p>
      </section>

      <section class="space-y-4">
        <h2 class="text-2xl font-semibold">Who This Is For</h2>
        <ul class="space-y-2 text-gray-600">
          <li>• First-time home buyers who need direction</li>
          <li>• Homeowners preparing to sell</li>
          <li>• Investors evaluating property opportunities</li>
          <li>• Anyone unsure about their next move in real estate</li>
        </ul>
      </section>

      <section class="space-y-4">
        <h2 class="text-2xl font-semibold">What You’ll Get</h2>
        <ul class="space-y-2 text-gray-600">
          <li>• A review of your current situation and goals</li>
          <li>• A clear breakdown of your options</li>
          <li>• Insight into current market conditions</li>
          <li>• Guidance on risks, opportunities, and next steps</li>
        </ul>
      </section>

      <section class="space-y-4">
        <h2 class="text-2xl font-semibold">Request Your Consultation</h2>
        <form class="space-y-4 rounded-lg border bg-white p-6">
          <input
            type="text"
            placeholder="Full Name"
            class="w-full rounded-md border px-4 py-3"
          />

          <input
            type="email"
            placeholder="Email Address"
            class="w-full rounded-md border px-4 py-3"
          />

          <input
            type="text"
            placeholder="Phone Number"
            class="w-full rounded-md border px-4 py-3"
          />

          <textarea
            placeholder="What do you need help with?"
            class="w-full rounded-md border px-4 py-3 min-h-[140px]"
          ></textarea>

          <button
            type="submit"
            class="rounded-md bg-gray-900 px-6 py-3 text-white hover:bg-gray-800"
          >
            Submit Request
          </button>
        </form>
      </section>
    </div>
  </FrontLayout>
</template>
