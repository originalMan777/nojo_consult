<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import PopupForm from './Form.vue'
</script>

<template>
  <AdminLayout>
    <Head title="Create Popup" />

    <div class="space-y-6">
      <div>
        <h1 class="text-3xl font-semibold text-gray-900">Create Popup</h1>
        <p class="mt-2 text-gray-600">
          Create a new popup campaign for the site.
        </p>
      </div>

      <PopupForm mode="create" />
    </div>
  </AdminLayout>
</template>
