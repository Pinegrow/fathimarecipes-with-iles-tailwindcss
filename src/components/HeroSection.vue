<script setup lang="ts">
  const { name } = defineProps({
    name: {
      type: String,
      default: 'HeroSection',
    },
  })

  // const emit = defineEmits(['addToCart'])

  // // Use the addToCart function like this:
  // // Eg-1: addToCart('pizza', 10)
  // // Eg-2: (event) => addToCart('pizza', 10, event)
  // const addToCart = (item, quantity /*, event*/) => {
  // 	emit('addToCart', { item, quantity })
  // }

  /* Auto-generated logic by Vue Designer Headless Wordpress 6.7 begins */
  const { getPgWordpressSiteinfo } = usePgWordpressData()
  const pgData: any = {
    pgWordpressData: {},
  }

  const pgSiteinfoArgs = { siteinfo: {} }

  pgData.pgWordpressData.siteinfo = {
    ...pgSiteinfoArgs.siteinfo,
    data: await getPgWordpressSiteinfo(pgSiteinfoArgs.siteinfo),
  }

  const pgSiteinfo = pgData.pgWordpressData.siteinfo.data
  /* Auto-generated logic by Vue Designer Headless Wordpress 6.7 ends */
</script>
<template>
    <header class="bg-cover bg-design-image flex items-center justify-center min-h-screen relative">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto" data-pg-siteinfo>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif text-white font-bold mb-6">
        {{ pgSiteinfo.name }} </h1>
            <p class="text-lg sm:text-xl lg:text-2xl font-sans text-white/90 mb-8 max-w-3xl mx-auto">
        {{ pgSiteinfo.description }} </p><a href="#recipes" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-sans px-8 py-4 rounded-lg text-lg transition-colors duration-300">Browse Recipes</a>
        </div>
    </header>
</template>
<style scoped></style>
