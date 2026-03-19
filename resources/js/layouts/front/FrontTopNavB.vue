<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="bg-white">
    <nav class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-2">
      <Link href="/" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">
        Home
      </Link>

      <Link href="/about" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">
        About
      </Link>

      <Link href="/services" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">
        Services
      </Link>

      <Link
        href="/consultation"
        class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100"
      >
        Consultation
      </Link>

      <Link href="/resources" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">
        Resources
      </Link>

      <Link href="/blog" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">
        Blog
      </Link>

      <Link href="/contact" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">
        Contact
      </Link>
    </nav>
  </div>
</template>
