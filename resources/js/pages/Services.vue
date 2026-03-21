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
              Services
            </p>

            <h1 class="text-4xl font-semibold tracking-tight text-gray-900 md:text-5xl lg:text-6xl">
              Strategic real estate guidance for every major decision
            </h1>

            <p class="max-w-3xl text-lg leading-relaxed text-gray-600 md:text-xl">
              Nojo offers focused consultation and real estate strategy services
              designed to help buyers, sellers, and investors move forward with
              more clarity, stronger positioning, and better decision-making.
            </p>
          </div>
        </div>
      </section>

      <!-- SERVICES GRID -->
      <section class="w-full bg-stone-50 py-16 md:py-20">
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto max-w-3xl space-y-4 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
              What We Offer
            </p>

            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
              Focused support for buyers, sellers, and property decisions
            </h2>

            <p class="text-base leading-relaxed text-gray-600 md:text-lg">
              Every service is designed to help clients understand their options,
              reduce uncertainty, and make stronger real estate decisions before
              they act.
            </p>
          </div>

          <div class="mt-12 grid gap-6 md:grid-cols-2">
            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <div class="space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                  01
                </p>

                <h3 class="text-2xl font-semibold tracking-tight text-gray-900">
                  Consultation
                </h3>

                <p class="leading-relaxed text-gray-600">
                  One-on-one guidance tailored to your real estate situation,
                  goals, and next steps. This service is designed for clients
                  who need clear direction before making a move.
                </p>
              </div>

              <div class="mt-8 border-t border-gray-100 pt-5">
                <p class="text-sm font-medium text-gray-900">
                  Personalized guidance for your situation
                </p>
              </div>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <div class="space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                  02
                </p>

                <h3 class="text-2xl font-semibold tracking-tight text-gray-900">
                  Buyer Strategy
                </h3>

                <p class="leading-relaxed text-gray-600">
                  Clear support for evaluating options, understanding the buying
                  process, and preparing to move toward the right purchase with
                  more confidence and less uncertainty.
                </p>
              </div>

              <div class="mt-8 border-t border-gray-100 pt-5">
                <p class="text-sm font-medium text-gray-900">
                  Smarter decisions before you buy
                </p>
              </div>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <div class="space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                  03
                </p>

                <h3 class="text-2xl font-semibold tracking-tight text-gray-900">
                  Seller Strategy
                </h3>

                <p class="leading-relaxed text-gray-600">
                  Strategic guidance for homeowners preparing to price, position,
                  and present their property effectively before entering the market.
                </p>
              </div>

              <div class="mt-8 border-t border-gray-100 pt-5">
                <p class="text-sm font-medium text-gray-900">
                  Better positioning before you sell
                </p>
              </div>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <div class="space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                  04
                </p>

                <h3 class="text-2xl font-semibold tracking-tight text-gray-900">
                  Market Evaluation
                </h3>

                <p class="leading-relaxed text-gray-600">
                  Insight into local market conditions, timing, and property
                  realities so you can better understand where you stand before
                  making your next move.
                </p>
              </div>

              <div class="mt-8 border-t border-gray-100 pt-5">
                <p class="text-sm font-medium text-gray-900">
                  Clearer perspective on the market
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- WHY THESE SERVICES MATTER -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="grid gap-10 md:grid-cols-[1fr_1fr] md:items-start">
            <div class="space-y-5">
              <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
                Why It Matters
              </p>

              <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
                Better decisions start with better guidance
              </h2>

              <p class="leading-relaxed text-gray-600">
                Real estate decisions often involve timing, preparation, pricing,
                and market realities that are easy to underestimate without the
                right support. Clear guidance helps reduce uncertainty and gives
                you a stronger understanding of what comes next.
              </p>

              <p class="leading-relaxed text-gray-600">
                Nojo is designed to make those decisions easier to navigate by
                helping clients slow down, assess their position, and move with
                more confidence.
              </p>
            </div>

            <div class="rounded-3xl bg-stone-50 p-8 shadow-sm ring-1 ring-black/5">
              <div class="space-y-4">
                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Clearer next steps</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Better preparation</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Stronger positioning</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">More confident decisions</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- WHO THESE SERVICES ARE FOR -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto max-w-3xl space-y-4 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
              Who These Services Are For
            </p>

            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
              Designed for clients who want clarity before they act
            </h2>

            <p class="text-base leading-relaxed text-gray-600 md:text-lg">
              These services are ideal for people who want to understand their
              options, avoid unnecessary confusion, and make better real estate
              decisions with the right support behind them.
            </p>
          </div>

          <div class="mx-auto mt-12 max-w-5xl space-y-4">
            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                First-time buyers who need a clearer understanding of the process
              </p>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                Homeowners preparing to sell and improve their market position
              </p>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                Investors evaluating property opportunities and timing
              </p>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                Clients who want informed direction before making their next move
              </p>
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
                Ready to talk through your next move?
              </h2>

              <p class="max-w-2xl text-base leading-relaxed text-white/80 md:text-lg">
                Book a consultation and get guidance tailored to your situation,
                your goals, and the decisions in front of you.
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
