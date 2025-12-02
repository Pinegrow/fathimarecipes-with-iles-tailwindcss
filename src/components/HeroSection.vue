<script setup lang="ts">
  import { $fetch } from 'ofetch'
  const wordpressUrl: string = import.meta.env.VITE_WORDPRESS_URL

  const response = await $fetch(`${wordpressUrl}/wp-json/wp/v2/blocks`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  })

  const hero =
    response.find((res: { slug: string }) => res.slug === 'hero') || null
</script>
<template>
  <!-- <div v-if="response.length" v-html="response.find(res => res.slug='header').content.rendered"></div> -->
  <div
    v-if="hero"
    id="hero"
    :class="[hero.class_list]"
    v-html="hero.content.rendered"
  ></div>
</template>
<style scoped></style>
