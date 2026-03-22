<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage<any>()
const user = computed(() => page.props.auth?.user ?? null)
const displayName = computed(() => user.value?.name ?? user.value?.email ?? 'Account')
const isAdmin = computed(() => Boolean(user.value?.is_admin))
</script>

<template>
  <header class="border-b border-gray-200 bg-white">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
      <div class="flex items-center gap-3">
        <Link href="/" class="text-lg font-semibold tracking-tight text-gray-900">
          Nojo
        </Link>

        <span class="text-sm text-gray-500">
          Consultation
        </span>
      </div>

      <div class="flex items-center gap-2 sm:gap-3">
        <template v-if="!user">
          <Link
            :href="route('login')"
            class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
          >
            Login
          </Link>

          <Link
            :href="route('register')"
            class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800"
          >
            Register
          </Link>
        </template>

        <template v-else>
          <div class="hidden rounded-md px-3 py-2 text-sm text-gray-600 sm:block">
            {{ displayName }}
          </div>

          <Link
            v-if="isAdmin"
            :href="route('dashboard')"
            class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
          >
            Dashboard
          </Link>

          <Link
            href="/logout"
            method="post"
            as="button"
            class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
          >
            Log out
          </Link>
        </template>
      </div>
    </nav>
  </header>
</template>
