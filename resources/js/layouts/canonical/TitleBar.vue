<template>
  <header class="title-bar flex items-center justify-between px-4 py-2 border-b border-gray-200 bg-white">
    <h1 class="text-xl font-semibold text-gray-900">
      {{ title }}
    </h1>

    <!-- Optional right slot for actions -->
    <div class="flex items-center space-x-2">
      <slot name="actions" />
    </div>
  </header>
</template>

<script setup lang = 'ts'>
defineProps({
  title: {
    type: String,
    default: 'Untitled Page',
  },
})
</script>
