<script setup lang="ts">
import FrontLayout from '@/layouts/FrontLayout.vue'
</script>

<template>
  <FrontLayout>
    <div class="max-w-4xl mx-auto px-6 py-16 space-y-12">
      <section class="space-y-4">
        <h1 class="text-4xl font-bold">Contact</h1>
        <p class="text-lg text-gray-600 max-w-2xl">
          Reach out with your questions or let us know how we can help with your
          next real estate decision.
        </p>
      </section>

      <section class="grid gap-8 md:grid-cols-2">
        <div class="space-y-4">
          <h2 class="text-2xl font-semibold">Get In Touch</h2>
          <p class="text-gray-600">
            Use the form to send a message and we’ll follow up with you.
          </p>
          <p class="text-gray-600">
            You can also use this page for general inquiries before booking a
            consultation.
          </p>
        </div>

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

          <textarea
            placeholder="Your Message"
            class="w-full rounded-md border px-4 py-3 min-h-[160px]"
          ></textarea>

          <button
            type="submit"
            class="rounded-md bg-gray-900 px-6 py-3 text-white hover:bg-gray-800"
          >
            Send Message
          </button>
        </form>
      </section>
    </div>
  </FrontLayout>
</template>
