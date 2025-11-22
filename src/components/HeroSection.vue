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
  <div v-else>
    <header
      class="bg-center bg-cover bg-no-repeat font-sans not-prose relative bg-[url('https://images.unsplash.com/photo-1665323707970-1940a5b80871?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDExfHxzdW1tZXIlMjBoZXJiJTIwcGxhbnRlcnxlbnwwfHx8fDE3NjM0NTYyMDB8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop')]"
    >
      <div class="absolute bg-black/40 inset-0"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-20 sm:py-24 lg:py-32">
          <div class="max-w-3xl">
            <div
              class="backdrop-blur bg-white/80 p-6 ring-1 ring-white/50 rounded-xl shadow-lg sm:p-8 lg:p-10"
            >
              <div
                class="flex gap-2 items-center justify-start text-gray-700 text-sm w-full sm:text-base"
              >
                <span
                  class="inline-flex h-2.5 w-2.5 rounded-full bg-green-500"
                ></span>
                <span>Seasonal gardening made simple</span>
              </div>
              <h1
                class="font-semibold lg:text-5xl mt-3 sm:text-4xl text-3xl text-gray-900 text-left tracking-tight"
              >
                Help your garden thrive in every season
              </h1>
              <p
                class="mt-4 text-base sm:text-lg lg:text-xl text-gray-700 leading-relaxed text-left"
              >
                Transform your garden with our comprehensive guides covering
                everything from soil preparation to seasonal maintenance.
              </p>
              <div
                class="mt-6 flex flex-col sm:flex-row gap-3 sm:gap-4 justify-start"
              >
                <a
                  href="/guides"
                  class="inline-flex items-center justify-center px-5 py-3 text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm text-sm sm:text-base"
                >
                  Get the Seasonal Guide </a
                ><a
                  href="/blog"
                  class="inline-flex items-center justify-center px-5 py-3 text-green-700 bg-white hover:bg-gray-50 rounded-md shadow-sm ring-1 ring-gray-200 text-sm sm:text-base"
                >
                  Browse Tips & Tricks
                </a>
              </div>
              <div class="mt-5 flex items-center gap-4 justify-start">
                <div class="flex -space-x-2">
                  <img
                    src="https://images.unsplash.com/photo-1653694577641-a8c511df0df9?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fGhlcmIlMjBiZWQlMjBjbG9zZXVwfGVufDB8fHx8MTc2MzQ1NjE5OHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop"
                    alt="Healthy herb bed"
                    class="h-8 w-8 rounded-full ring-2 ring-white shadow"
                  />
                  <img
                    src="https://images.unsplash.com/photo-1722973681429-04332f5cb901?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fHBlcmVubmlhbCUyMGJsb29tc3xlbnwwfHx8fDE3NjM0NTYxOTl8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop"
                    alt="Perennial blooms"
                    class="h-8 w-8 rounded-full ring-2 ring-white shadow"
                  />
                  <img
                    src="https://images.unsplash.com/photo-1757917702671-fff5a7b3c2e3?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDEyfHx2ZWdldGFibGUlMjBoYXJ2ZXN0fGVufDB8fHx8MTc2MzQ1NjIwMHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop"
                    alt="Autumn vegetable harvest"
                    class="h-8 w-8 rounded-full ring-2 ring-white shadow"
                  />
                </div>
                <p class="text-sm text-gray-700">
                  Fresh, easy advice for your beds and planters.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  </div>
</template>
<style scoped></style>
