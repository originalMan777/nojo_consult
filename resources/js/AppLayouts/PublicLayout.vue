<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import PublicPopupModal from '@/components/public/PublicPopupModal.vue'
import { computed, ref } from 'vue'

type CategoryItem = {
  name: string
  slug: string
  count?: number
}

const props = withDefaults(
  defineProps<{
    pageClass?: string
    headerClass?: string
    subnavClass?: string
  }>(),
  {
    pageClass: 'bg-white',
    headerClass: 'bg-white',
    subnavClass: 'bg-gray-50',
  }
)

const page = usePage<any>()

const user = computed(() => page.props?.auth?.user ?? null)
const isAdmin = computed(() => Boolean(user.value?.is_admin))
const displayName = computed(() => user.value?.name ?? user.value?.email ?? 'Account')
const categories = computed<CategoryItem[]>(() => page.props?.categories ?? [])

const sidebarOpen = ref(true)
const currentYear = new Date().getFullYear()

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}
</script>

<template>
  <div class="min-h-screen flex flex-col" :class="props.pageClass">
    <div class="relative z-20">
      <Link
        href="/"
        class="absolute left-6 top-1/2 z-30 -translate-y-1/2 md:left-8"
      >
        <img
          src="/images/branding/logo-wide.svg"
          alt="Awestruck"
          class="h-[60px] w-auto"
          loading="eager"
          decoding="async"
        />
      </Link>

      <header class="border-b" :class="props.headerClass">
        <div class="mx-auto flex max-w-7xl items-center justify-end gap-3 px-6 py-3 pl-44 md:px-8">
          <nav class="flex items-center gap-2 sm:gap-4">
            <Link :href="route('blog.index')" class="text-sm text-gray-600 hover:text-gray-900">
              Blog
            </Link>

            <Link href="/sites" class="text-sm text-gray-600 hover:text-gray-900">
              Sites
            </Link>

            <template v-if="user">
              <span class="hidden text-sm text-gray-500 sm:inline-flex">
                {{ displayName }}
              </span>

              <Link
                v-if="isAdmin"
                :href="route('dashboard')"
                class="text-sm text-gray-600 hover:text-gray-900"
              >
                Dashboard
              </Link>

              <Link
                href="/logout"
                method="post"
                as="button"
                class="text-sm text-gray-600 hover:text-gray-900"
              >
                Log out
              </Link>
            </template>

            <template v-else>
              <Link :href="route('login')" class="text-sm text-gray-600 hover:text-gray-900">
                Login
              </Link>
              <Link :href="route('register')" class="text-sm text-gray-600 hover:text-gray-900">
                Register
              </Link>
            </template>
          </nav>
        </div>
      </header>

      <div class="border-b" :class="props.subnavClass">
        <div class="mx-auto grid max-w-7xl grid-cols-[1fr_auto_1fr] items-center px-6 py-3 md:px-8">
          <div></div>

          <nav class="flex items-center justify-center gap-6 overflow-x-auto">
            <Link href="#" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-gray-900">
              All Posts
            </Link>
            <Link href="#" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-gray-900">
              Featured
            </Link>
            <Link href="#" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-gray-900">
              Tutorials
            </Link>
            <Link href="#" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-gray-900">
              Guides
            </Link>
            <Link href="#" class="whitespace-nowrap text-sm font-medium text-gray-700 hover:text-gray-900">
              Categories
            </Link>
          </nav>

          <div class="flex justify-end">
            <button
              v-if="!sidebarOpen"
              type="button"
              @click="toggleSidebar"
              class="ml-4 shrink-0 rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-100"
            >
              Show Menu
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="flex-1">
      <div class="mx-auto flex max-w-7xl gap-10 px-6 py-10">
        <main class="min-w-0 flex-1">
          <slot />
        </main>

        <aside
          v-if="categories.length"
          class="shrink-0 transition-all duration-300"
          :class="sidebarOpen ? 'w-72' : 'w-0 overflow-hidden'"
        >
          <div
            v-if="sidebarOpen"
            class="sticky top-0 h-[calc(100vh-129px)] overflow-y-auto rounded-2xl border border-gray-100 bg-gray-50 p-5"
          >
            <div class="flex items-center justify-between gap-3">
              <h2 class="text-lg font-serif font-semibold text-gray-900">
                Categories
              </h2>

              <button
                type="button"
                @click="toggleSidebar"
                class="shrink-0 rounded-md border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 transition hover:bg-gray-100"
              >
                Hide Sidebar
              </button>
            </div>

            <p class="mt-2 font-sans text-sm leading-6 text-gray-600">
              Browse articles by topic.
            </p>

            <div class="mt-5 flex flex-col gap-3">
              <Link
                v-for="category in categories"
                :key="category.slug"
                :href="route('blog.category', category.slug)"
                class="flex items-center justify-between rounded-xl bg-white px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-100"
              >
                <span>{{ category.name }}</span>

                <span
                  v-if="category.count !== undefined"
                  class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500"
                >
                  {{ category.count }}
                </span>
              </Link>
            </div>
          </div>
        </aside>
      </div>
    </div>

    <footer class="mt-10 border-t border-black/5 bg-white">
      <div class="mx-auto max-w-7xl px-6 py-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
          <div class="flex items-center gap-4">
            <img
              src="/images/branding/logo-square.png"
              alt="Awestruck"
              class="h-[72px] w-[72px] shrink-0"
              loading="lazy"
              decoding="async"
            />

            <p class="text-sm font-medium text-gray-700">
              Powered by <span class="text-gray-900">Awestruk Multimedia</span>
            </p>
          </div>

          <nav class="flex flex-wrap gap-x-8 gap-y-3 text-sm text-gray-600 md:justify-end">
            <Link href="/" class="transition hover:text-gray-900">
              Home
            </Link>
            <Link :href="route('blog.index')" class="transition hover:text-gray-900">
              Blog
            </Link>
            <Link href="/sites" class="transition hover:text-gray-900">
              Sites
            </Link>
            <Link href="/about" class="transition hover:text-gray-900">
              About
            </Link>
          </nav>
        </div>

        <div class="mt-6 border-t border-black/5 pt-4 text-center">
          <p class="text-xs text-gray-400">
            © {{ currentYear }} Awestruk Multimedia. All rights reserved.
          </p>
        </div>
      </div>
    </footer>

    <PublicPopupModal />
  </div>
</template>
