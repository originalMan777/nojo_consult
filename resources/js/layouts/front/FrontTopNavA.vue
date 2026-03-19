<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)
</script>

<template>
  <header class="bg-white">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
      <div>
        <Link href="/" class="text-lg font-semibold">
          Nojo
        </Link>
      </div>

      <div class="flex items-center gap-3">
        <template v-if="!user">
          <Link
            href="/login"
            class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100"
          >
            Log in
          </Link>
        </template>

        <template v-else>
          <div class="rounded-md px-3 py-2 text-sm text-gray-600">
            {{ user.username ?? user.name }}
          </div>

          <Link
            v-if="user.role === 'admin'"
            href="/dashboard"
            class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100"
          >
            Dashboard
          </Link>

          <Link
            href="/logout"
            method="post"
            as="button"
            class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100"
          >
            Log out
          </Link>
        </template>
      </div>
    </nav>
  </header>
</template>
