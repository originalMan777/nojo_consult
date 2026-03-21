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
              About
            </p>

            <h1 class="text-4xl font-semibold tracking-tight text-gray-900 md:text-5xl lg:text-6xl">
              Real estate guidance built on clarity, trust, and thoughtful direction
            </h1>

            <p class="max-w-3xl text-lg leading-relaxed text-gray-600 md:text-xl">
              Nojo helps buyers, sellers, and investors approach real estate decisions
              with more confidence by offering focused guidance, clear communication,
              and practical support every step of the way.
            </p>
          </div>
        </div>
      </section>

      <!-- STORY / INTRO -->
      <section class="w-full">
        <div class="mx-auto grid max-w-6xl gap-10 px-6 md:grid-cols-[1.1fr_0.9fr] md:items-start">
          <div class="space-y-5">
            <h2 class="text-3xl font-semibold tracking-tight text-gray-900">
              A more thoughtful approach to real estate
            </h2>

            <p class="leading-relaxed text-gray-600">
              Real estate decisions carry weight. Whether someone is buying their
              first home, preparing to sell, or trying to understand the market,
              the process can quickly become overwhelming without the right guidance.
            </p>

            <p class="leading-relaxed text-gray-600">
              Nojo was created to make that process clearer. The goal is not to add
              pressure or confusion, but to provide steady, informed direction that
              helps clients understand their options and move forward with confidence.
            </p>

            <p class="leading-relaxed text-gray-600">
              Every situation is different, which is why the approach stays grounded
              in listening first, understanding the client’s real position, and then
              offering guidance that actually fits the moment.
            </p>
          </div>

          <div class="rounded-3xl bg-stone-50 p-8 shadow-sm ring-1 ring-black/5">
            <div class="space-y-5">
              <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
                Core Focus
              </p>

              <div class="space-y-4">
                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Clear communication</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Practical strategy</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Client-focused guidance</p>
                </div>

                <div class="rounded-2xl bg-white px-5 py-4 ring-1 ring-black/5">
                  <p class="font-medium text-gray-900">Confidence before action</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- WHO WE HELP -->
      <section class="w-full bg-stone-50 py-16 md:py-20">
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto max-w-3xl space-y-4 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
              Who We Help
            </p>

            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
              Support for buyers, sellers, and people planning their next move
            </h2>

            <p class="text-base leading-relaxed text-gray-600 md:text-lg">
              Nojo is designed for clients who want to make better real estate
              decisions with the right information, the right perspective, and a
              clearer understanding of what comes next.
            </p>
          </div>

          <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <h3 class="text-xl font-semibold tracking-tight text-gray-900">
                First-time buyers
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Guidance for understanding the process, evaluating options, and
                moving forward with more clarity.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <h3 class="text-xl font-semibold tracking-tight text-gray-900">
                Homeowners preparing to sell
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Strategic support for pricing, preparation, and entering the market
                with stronger positioning.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <h3 class="text-xl font-semibold tracking-tight text-gray-900">
                Investors
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Insight for evaluating property opportunities and understanding how
                market conditions affect decisions.
              </p>
            </div>

            <div class="rounded-[28px] bg-white p-8 shadow-sm ring-1 ring-black/5">
              <h3 class="text-xl font-semibold tracking-tight text-gray-900">
                Families relocating
              </h3>
              <p class="mt-3 leading-relaxed text-gray-600">
                Direction for navigating a move with less uncertainty and more
                confidence about the next step.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- WHY NOJO -->
      <section class="w-full">
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto max-w-3xl space-y-4 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-gray-500">
              Why Nojo
            </p>

            <h2 class="text-3xl font-semibold tracking-tight text-gray-900 md:text-4xl">
              A steady, grounded approach to important decisions
            </h2>

            <p class="text-base leading-relaxed text-gray-600 md:text-lg">
              The focus is simple: offer practical guidance that helps clients
              feel more informed, more prepared, and more confident before they act.
            </p>
          </div>

          <div class="mx-auto mt-12 max-w-5xl space-y-4">
            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">Clear, honest guidance</p>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                Focused on your specific situation
              </p>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                Local market understanding
              </p>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                Strategy before action
              </p>
            </div>

            <div class="rounded-2xl bg-white px-6 py-5 shadow-sm ring-1 ring-black/5">
              <p class="text-base font-medium text-gray-900">
                No pressure — just direction you can trust
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
                Next Step
              </p>

              <h2 class="text-3xl font-semibold tracking-tight md:text-4xl">
                Ready to move forward with more clarity?
              </h2>

              <p class="max-w-2xl text-base leading-relaxed text-white/80 md:text-lg">
                Book a consultation and get real estate guidance tailored to your
                situation, your goals, and your next move.
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
