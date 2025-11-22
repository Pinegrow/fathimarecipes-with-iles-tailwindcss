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
  // console.log(response)
</script>
<template>
    <div v-html="response[0].content.rendered"></div>
</template>
<style scoped></style>
