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
  <div class="container mx-auto px-4 py-16">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="relative h-96">
          <img
            src="https://images.unsplash.com/photo-1512485800893-b08ec1ea59b1?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDh8fGNoZWYlMjBwb3J0cmFpdHxlbnwwfHx8fDE3MzQ1OTA0ODB8MA&ixlib=rb-4.0.3q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop"
            alt="Chef Fathima"
            class="w-full h-full object-cover"
          />
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"
          ></div>
        </div>
        <div class="p-8 md:p-12">
          <h1 class="text-4xl md:text-5xl font-serif text-primary-800 mb-6">
            Fathima's Kitchen
          </h1>
          <div class="space-y-6">
            <p class="text-lg text-gray-700 leading-relaxed">
              Welcome to my culinary journey! I'm Fathima, a passionate home
              chef with over 15 years of experience crafting delicious recipes
              that blend traditional flavors with modern techniques.
            </p>
            <div class="border-l-4 border-primary-500 pl-6">
              <p class="text-lg italic text-gray-600">
                "Cooking is not just about ingredients; it's about bringing joy
                and connecting people through food."
              </p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 text-center py-6">
              <div class="bg-primary-50 p-4 rounded-lg">
                <span class="block text-3xl font-bold text-primary-700"
                  >500+</span
                ><span class="text-gray-600">Recipes Shared</span>
              </div>
              <div class="bg-primary-50 p-4 rounded-lg">
                <span class="block text-3xl font-bold text-primary-700"
                  >15+</span
                ><span class="text-gray-600">Years Experience</span>
              </div>
              <div class="bg-primary-50 p-4 rounded-lg">
                <span class="block text-3xl font-bold text-primary-700"
                  >50K+</span
                ><span class="text-gray-600">Happy Readers</span>
              </div>
            </div>
            <div class="space-y-4">
              <h2 class="text-2xl font-serif text-primary-800">Specialties</h2>
              <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li>Traditional Middle Eastern Cuisine</li>
                <li>Modern Fusion Dishes</li>
                <li>Healthy Mediterranean Recipes</li>
                <li>Artisanal Bread Making</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped></style>
