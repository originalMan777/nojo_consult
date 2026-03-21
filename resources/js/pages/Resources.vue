<script setup lang="ts">
import FrontLayout from '@/layouts/FrontLayout.vue'
</script>

<template>
  <FrontLayout>
    <div class="space-y-20 py-16">
      <!-- HERO -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="max-w-4xl space-y-6">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
              Resources
            </p>

            <h1 class="text-4xl font-semibold tracking-tight text-gray-900 md:text-5xl lg:text-6xl">
              Real estate insights, guidance, and practical resources
            </h1>

            <p class="max-w-3xl text-lg leading-relaxed text-gray-600 md:text-xl">
              Explore articles, guides, and helpful information designed to make
              buying, selling, and understanding the market feel more clear and
              more manageable.
            </p>
          </div>
        </div>
      </section>

      <!-- FEATURED RESOURCE CATEGORIES -->
      <section class="w-full bg-stone-50 py-16 md:py-20">
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto max-w-3xl space-y-4 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
              Resource Categories
            </p>

            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
              Helpful guidance organized around the questions people ask most
            </h2>

            <p class="text-base leading-relaxed text-gray-600 md:text-lg">
              These resource areas are designed to help visitors find useful
              information quickly, whether they are preparing to buy, planning to
              sell, or trying to make sense of the market.
            </p>
          </div>

          <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                01
              </p>
              <h3 class="mt-5 text-2xl font-semibold tracking-tight text-gray-900">
                Buying Guidance
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Helpful direction for buyers preparing to understand the process,
                evaluate options, and move forward with more confidence.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                02
              </p>
              <h3 class="mt-5 text-2xl font-semibold tracking-tight text-gray-900">
                Selling Advice
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Resources for homeowners planning to prepare, position, and sell
                their property with stronger strategy.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                03
              </p>
              <h3 class="mt-5 text-2xl font-semibold tracking-tight text-gray-900">
                Market Insights
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Updates and explanations that help visitors better understand
                changing market conditions and what they may mean.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                04
              </p>
              <h3 class="mt-5 text-2xl font-semibold tracking-tight text-gray-900">
                Practical Tips
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Straightforward guidance on common questions, preparation steps,
                and the details that often matter most.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- FEATURED GUIDES -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="grid gap-10 md:grid-cols-[1fr_1fr] md:items-start">
            <div class="space-y-5">
              <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
                Featured Guides
              </p>

              <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
                Start with the most useful and relevant information
              </h2>

              <p class="leading-relaxed text-gray-600">
                Featured guides make it easier for visitors to begin with high-value
                content that answers common questions and helps them better
                understand where they stand.
              </p>

              <p class="leading-relaxed text-gray-600">
                Over time, this section can highlight cornerstone articles,
                downloadable resources, and practical guides tied directly to
                buying, selling, or understanding the local market.
              </p>
            </div>

            <div class="rounded-3xl bg-stone-50 p-8 shadow-sm ring-1 ring-black/5">
              <div class="space-y-4">
                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">First-time home buyer guide</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">How to prepare your home for sale</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Understanding local market conditions</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Questions to ask before making a move</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ARTICLE PREVIEW GRID -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto max-w-3xl space-y-4 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
              Insights
            </p>

            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
              Helpful content built around real decisions
            </h2>

            <p class="text-base leading-relaxed text-gray-600 md:text-lg">
              This section can feature recent articles, educational content, and
              practical posts that support buyers, sellers, and anyone trying to
              better understand their next move.
            </p>
          </div>

          <div class="mt-12 grid gap-6 md:grid-cols-3">
            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <h3 class="text-2xl font-semibold tracking-tight text-gray-900">
                Buying in New Jersey
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Helpful guidance for buyers preparing to navigate the New Jersey
                market with more confidence.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <h3 class="text-2xl font-semibold tracking-tight text-gray-900">
                Selling Tips
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Practical advice for homeowners getting ready to prepare, position,
                and sell effectively.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <h3 class="text-2xl font-semibold tracking-tight text-gray-900">
                Market Trends
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Insights into market conditions and what they may mean for your
                next real estate decision.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- WHY RESOURCES MATTER -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto max-w-5xl space-y-4">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500 text-center">
              Why This Matters
            </p>

            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 text-center md:text-4xl">
              Better information leads to stronger decisions
            </h2>

            <p class="mx-auto max-w-3xl text-center text-base leading-relaxed text-gray-600 md:text-lg">
              Resources like these help visitors feel more prepared, reduce
              uncertainty, and build trust before they ever reach out. They also
              strengthen the site as a true authority platform rather than just a
              simple online brochure.
            </p>

            <div class="pt-6 space-y-4">
              <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                <p class="text-base font-medium text-gray-900">
                  Helps buyers and sellers understand their options
                </p>
              </div>

              <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                <p class="text-base font-medium text-gray-900">
                  Builds trust through useful and relevant guidance
                </p>
              </div>

              <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                <p class="text-base font-medium text-gray-900">
                  Supports long-term content growth and SEO visibility
                </p>
              </div>

              <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
                <p class="text-base font-medium text-gray-900">
                  Gives visitors value before they book a consultation
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="rounded-[32px] bg-gray-900 px-8 py-12 text-white md:px-12 md:py-16">
            <div class="max-w-3xl space-y-5">
              <p class="text-sm font-semibold uppercase tracking-[0.18em] text-white/60">
                Consultation
              </p>

              <h2 class="text-3xl font-semibold tracking-tight md:text-4xl">
                Need guidance tailored to your specific situation?
              </h2>

              <p class="max-w-2xl text-base leading-relaxed text-white/80 md:text-lg">
                Explore the resources, then take the next step with a consultation
                designed around your goals, your questions, and your next move.
              </p>

              <div class="pt-2">
                <a
                  href="/consultation"
                  class="inline-flex rounded-md bg-white px-6 py-3 font-medium text-gray-900 transition hover:bg-white/90"
                >
                  Book a Consultation
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </FrontLayout>
</template>
