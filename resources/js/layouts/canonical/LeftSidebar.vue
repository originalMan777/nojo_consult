<template>
  <nav class="admin-sidebar">
    <div v-for="section in sections" :key="section.title" class="sidebar-section">
      <!-- Click the title row to expand/collapse extra links -->
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
        <!-- Always-visible links -->
        <li v-for="link in visibleLinks(section)" :key="link.name">
          <Link
            :href="link.route"
            class="sidebar-link"
            :class="{ active: isActive(link.route) }"
          >
            {{ link.name }}
          </Link>
        </li>

        <!-- Dropdown links -->
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

// Helpers
const isActive = (target: string) => {
  if (target === '/admin') {
    return currentUrl.value === '/admin' || currentUrl.value === '/admin/'
  }

  // If the target includes a query string, match only the base path
  const targetBase = target.split('?')[0]
  return currentUrl.value.startsWith(targetBase)
}

const cs = (label: string) => `/admin/coming-soon?m=${encodeURIComponent(label)}`

// ✅ DEFINE SECTIONS FIRST (fixes “before initialization”)
const sections: SidebarSection[] = [
  {
    title: 'Dashboard',
    links: [
      { name: 'Overview', route: '/admin' },
      { name: 'Activity Feed', route: '/admin/activity' },
      { name: 'Stats', route: '/admin/stats' },
    ],
  },
  {
    title: 'Tracks',
    links: [
      { name: 'All Tracks', route: '/admin/music/tracks' },
      { name: 'Create Track', route: '/admin/music/tracks/create' },
      { name: 'QC', route: '/admin/qc' },
    ],
  },
  {
    title: 'Commerce',
    links: [
      { name: 'Products', route: '/admin/commerce/products' },
      { name: 'Prices', route: '/admin/commerce/products' }, // routes to Products for now
      { name: 'Digital Assets', route: cs('Digital Assets') },
      { name: 'Orders', route: '/admin/commerce/orders' },
      { name: 'Payments', route: cs('Payments') },
      { name: 'Discounts', route: cs('Discounts') },
      { name: 'Shipping', route: cs('Shipping') },
      { name: 'Taxes', route: cs('Taxes') },
    ],
  },
  {
    title: 'Albums',
    links: [
      { name: 'All Albums', route: '/admin/music/albums' },
      { name: 'Create Album', route: '/admin/music/albums/create' },
    ],
  },
  {
    title: 'Music Store',
    links: [
      { name: 'Product List', route: '/admin/store/music/products' },
      { name: 'Pricing Matrix', route: '/admin/store/music/pricing' },
      { name: 'Promo Codes', route: '/admin/store/music/promos' },
      { name: 'Orders / Purchases', route: '/admin/store/music/orders' },
    ],
  },
  {
    title: 'Merch Store',
    links: [
      { name: 'Product List', route: '/admin/store/merch/products' },
      { name: 'Inventory / Sizes', route: '/admin/store/merch/inventory' },
      { name: 'Orders / Fulfillment', route: '/admin/store/merch/orders' },
      { name: 'Shipping Settings', route: '/admin/store/merch/shipping' },
      { name: 'Tags / Categories', route: '/admin/store/merch/categories' },
    ],
  },
  {
    title: 'Users',
    links: [
      { name: 'All Users', route: '/admin/users' },
      { name: 'Roles & Permissions', route: '/admin/users/roles' },
      { name: 'Invitations', route: '/admin/users/invitations' },
      { name: 'Bans / Restrictions', route: '/admin/users/bans' },
    ],
  },
  {
    title: 'Assets & Notes',
    links: [
      { name: 'Artwork Upload', route: '/admin/assets/artwork' },
      { name: 'Track Attachments', route: '/admin/assets/attachments' },
      { name: 'Notes / Comments', route: '/admin/assets/notes' },
      { name: 'Shared Files', route: '/admin/assets/files' },
    ],
  },
  {
    title: 'Analytics',
    links: [
      { name: 'Track Plays', route: '/admin/analytics/plays' },
      { name: 'Sales Reports', route: '/admin/analytics/sales' },
      { name: 'Conversion Rates', route: '/admin/analytics/conversions' },
      { name: 'Referral Traffic', route: '/admin/analytics/referrals' },
    ],
  },
]

// Expand/collapse state
const expanded = reactive<Record<string, boolean>>({})

const isExpanded = (title: string) => !!expanded[title]
const toggleSection = (title: string) => {
  expanded[title] = !expanded[title]
}

const visibleLinks = (section: SidebarSection) => section.links.slice(0, ALWAYS_VISIBLE_LINKS)
const hiddenLinks = (section: SidebarSection) => section.links.slice(ALWAYS_VISIBLE_LINKS)

// Auto-expand if current page is in hidden links (so active page isn't hidden)
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
