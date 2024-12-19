<page> 
    #layout: default
    #name: about-me
    #alias: about-me
    title: About Me
    description: About Me
    #hidden: true
    navOrder: 2
    type: primary
    icon: i-mdi-home
</page>
<script setup lang="ts">
  // import { usePage } from 'iles'
  // const { frontmatter, meta } = usePage()
</script>
<template layout="default">
  <div>
    <div
      style="
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
      "
    >
      <span style="color: #0198cd">about-me.vue (Iles 0.10)</span>
    </div>
  </div>
</template>
<style scoped></style>
