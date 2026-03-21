<template>
  <nav class="admin-sidebar">
    <div v-for="section in sections" :key="section.title" class="sidebar-section">
      <button
        type="button"
        class="sidebar-section-title-row"
        @click="toggleSection(section.title)"
      >
        <span class="sidebar-section-title">{{ section.title }}</span>

        <span
          v-if="section.links.length > ALWAYS_VISIBLE_LINKS"
          class="sidebar-section-caret"
          :class="{ open: isExpanded(section.title) }"
          aria-hidden="true"
        >
          ▾
        </span>
      </button>

      <ul class="sidebar-link-group">
        <li v-for="link in visibleLinks(section)" :key="link.name">
          <Link
            :href="link.route"
            class="sidebar-link"
            :class="{ active: isActive(link.route) }"
          >
            {{ link.name }}
          </Link>
        </li>

        <template v-if="section.links.length > ALWAYS_VISIBLE_LINKS && isExpanded(section.title)">
          <li v-for="link in hiddenLinks(section)" :key="link.name">
            <Link
              :href="link.route"
              class="sidebar-link sidebar-link-sub"
              :class="{ active: isActive(link.route) }"
            >
              {{ link.name }}
            </Link>
          </li>
        </template>
      </ul>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed, reactive, watch } from 'vue'

type SidebarLink = { name: string; route: string }
type SidebarSection = { title: string; links: SidebarLink[] }

const ALWAYS_VISIBLE_LINKS = 2

const page = usePage()
const currentUrl = computed(() => page.url)

const isActive = (target: string) => {
  if (target === '/admin') {
    return currentUrl.value === '/admin' || currentUrl.value === '/admin/'
  }

  const targetBase = target.split('?')[0]
  return currentUrl.value.startsWith(targetBase)
}

const cs = (label: string) => `/admin/coming-soon?m=${encodeURIComponent(label)}`

const sections: SidebarSection[] = [
  {
    title: 'Dashboard',
    links: [
      { name: 'Overview', route: '/admin' },
      { name: 'Activity', route: cs('Activity') },
    ],
  },

  {
    title: 'Posts',
    links: [
      { name: 'All Posts', route: '/admin/posts' },
      { name: 'Create Post', route: '/admin/posts/create' },
      { name: 'Categories', route: '/admin/categories' },
      { name: 'Tags', route: '/admin/tags' },
    ],
  },

  {
    title: 'Popups',
    links: [
      { name: 'All Popups', route: '/admin/popups' },
      { name: 'Create Popup', route: '/admin/popups/create' },
      { name: 'Popup Submissions', route: cs('Popup Submissions') },
    ],
  },

  {
    title: 'Media',
    links: [
      { name: 'Library', route: '/admin/media' },
      { name: 'Browser', route: '/admin/media/browser' },
    ],
  },

  {
    title: 'Site Pages',
    links: [
      { name: 'Home', route: cs('Homepage Settings') },
      { name: 'About', route: cs('About Page') },
      { name: 'Services', route: cs('Services Page') },
      { name: 'Consultation', route: cs('Consultation Page') },
      { name: 'Resources', route: cs('Resources Page') },
      { name: 'Contact', route: cs('Contact Page') },
    ],
  },

  {
    title: 'Leads & Clients',
    links: [
      { name: 'All Leads', route: cs('All Leads') },
      { name: 'Consultations', route: cs('Consultations') },
    ],
  },

  {
    title: 'System',
    links: [
      { name: 'Settings', route: cs('Settings') },
    ],
  },
]

const expanded = reactive<Record<string, boolean>>({})

const isExpanded = (title: string) => !!expanded[title]
const toggleSection = (title: string) => {
  expanded[title] = !expanded[title]
}

const visibleLinks = (section: SidebarSection) => section.links.slice(0, ALWAYS_VISIBLE_LINKS)
const hiddenLinks = (section: SidebarSection) => section.links.slice(ALWAYS_VISIBLE_LINKS)

watch(
  () => currentUrl.value,
  () => {
    for (const section of sections) {
      if (section.links.length <= ALWAYS_VISIBLE_LINKS) continue

      const hidden = hiddenLinks(section)
      const activeInHidden = hidden.some((l) => isActive(l.route))
      if (activeInHidden) expanded[section.title] = true
    }
  },
  { immediate: true },
)
</script>

<style scoped>
.admin-sidebar {
  width: 240px;
  background-color: #0f172a;
  color: white;
  height: 100vh;
  padding: 1rem;
  overflow-y: auto;
}

.sidebar-section {
  margin-bottom: 1.25rem;
}

.sidebar-section-title-row {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 0;
  background: transparent;
  padding: 0;
  cursor: pointer;
  text-align: left;
}

.sidebar-section-title {
  font-weight: 700;
  font-size: 0.95rem;
  margin-bottom: 0.35rem;
}

.sidebar-section-caret {
  font-size: 0.85rem;
  color: #94a3b8;
  transform: rotate(0deg);
  transition: transform 120ms ease-in-out;
  margin-bottom: 0.35rem;
}

.sidebar-section-caret.open {
  transform: rotate(180deg);
}

.sidebar-link-group {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-link {
  display: block;
  padding: 0.45rem 0;
  color: #cbd5e1;
  text-decoration: none;
  border-radius: 6px;
}

.sidebar-link:hover {
  color: #ffffff;
}

.sidebar-link.active {
  font-weight: 700;
  color: #facc15;
}

.sidebar-link-sub {
  padding-left: 0.75rem;
  opacity: 0.95;
}
</style>
