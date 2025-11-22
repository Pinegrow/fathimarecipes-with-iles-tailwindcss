<script setup lang="ts">
  import { $fetch } from 'ofetch'
  const wordpressUrl: string = import.meta.env.VITE_WORDPRESS_URL

  const response = await $fetch(`${wordpressUrl}/wp-json/wp/v2/blocks`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      // authorization: `Bearer ${import.meta.env.VITE_WORDPRESS_API_KEY}`,
    },
  })
</script>
<template>
  <div v-if="response.length" v-html="response[0].content.rendered"></div>
</template>
<style scoped></style>
