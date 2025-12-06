<script setup lang="ts">
  import { useDirectus } from '@/plugins/directus'

  // import type { Hero } from '@/types/schema'

  const { directus, readSingleton } = useDirectus()
  const footer = await directus.request(readSingleton('footer'))
</script>
<template>
  <footer id="footer" class="bg-primary-100 py-12 px-4 md:px-8">
    <DirectusFooter client:load :footer="footer" />
  </footer>
</template>
<style scoped></style>
