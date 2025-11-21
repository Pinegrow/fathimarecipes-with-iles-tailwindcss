<script setup lang="ts"></script>

<template>
  <div>
    <header
      class="bg-center bg-cover bg-no-repeat font-sans not-prose relative bg-[url('https://images.unsplash.com/photo-1665323707970-1940a5b80871?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDExfHxzdW1tZXIlMjBoZXJiJTIwcGxhbnRlcnxlbnwwfHx8fDE3NjM0NTYyMDB8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop')]"
      cms-block="hero-seasonal"
      cms-block-title="Hero - Seasonal Gardening"
      cms-block-description="Hero section promoting seasonal gardening guides with call-to-action buttons"
      cms-block-field="background_image"
      cms-block-field-type="background-image"
      cms-block-field-title="Background Image"
    >
      <div class="absolute inset-0 bg-black/40"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-20 sm:py-24 lg:py-32">
          <div class="max-w-3xl">
            <div
              class="bg-white/80 backdrop-blur rounded-xl shadow-lg ring-1 ring-white/50 p-6 sm:p-8 lg:p-10"
            >
              <span
                class="inline-flex items-center gap-2 text-sm sm:text-base text-gray-700"
              >
                <span
                  class="inline-flex h-2.5 w-2.5 rounded-full bg-green-500"
                ></span>
                <span
                  cms-block-field="badge_text"
                  cms-block-field-type="content"
                  cms-block-field-title="Badge Text"
                  >Seasonal gardening made simple</span
                >
              </span>
              <h1
                class="mt-3 text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight text-gray-900"
                cms-block-field="main_heading"
                cms-block-field-type="content"
                cms-block-field-title="Main Heading"
              >
                Help your garden thrive in every season
              </h1>
              <p
                class="mt-4 text-base sm:text-lg lg:text-xl text-gray-700 leading-relaxed"
                cms-block-field="description"
                cms-block-field-type="content"
                cms-block-field-title="Description"
                cms-block-field-control="richtext"
              >
                Transform your garden with our comprehensive guides covering
                everything from soil preparation to seasonal maintenance.
              </p>
              <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:gap-4">
                <a
                  href="/guides"
                  class="inline-flex items-center justify-center px-5 py-3 text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm text-sm sm:text-base"
                  cms-block-field="primary_button_link"
                  cms-block-field-type="link"
                  cms-block-field-title="Primary Button Link"
                  cms-block-field-2="primary_button_label"
                  cms-block-field-type-2="content"
                  cms-block-field-title-2="Primary Button Label"
                >
                  Get the Seasonal Guide
                </a>
                <a
                  href="/blog"
                  class="inline-flex items-center justify-center px-5 py-3 text-green-700 bg-white hover:bg-gray-50 rounded-md shadow-sm ring-1 ring-gray-200 text-sm sm:text-base"
                  cms-block-field="secondary_button_link"
                  cms-block-field-type="link"
                  cms-block-field-title="Secondary Button Link"
                  cms-block-field-2="secondary_button_label"
                  cms-block-field-type-2="content"
                  cms-block-field-title-2="Secondary Button Label"
                >
                  Browse Tips & Tricks
                </a>
              </div>
              <div class="mt-5 flex items-center gap-4">
                <div class="flex -space-x-2">
                  <img
                    src="https://images.unsplash.com/photo-1653694577641-a8c511df0df9?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fGhlcmIlMjBiZWQlMjBjbG9zZXVwfGVufDB8fHx8MTc2MzQ1NjE5OHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop"
                    alt="Healthy herb bed"
                    class="h-8 w-8 rounded-full ring-2 ring-white shadow"
                    cms-block-field="avatar_image_1"
                    cms-block-field-type="image"
                    cms-block-field-title="Avatar Image 1"
                  />
                  <img
                    src="https://images.unsplash.com/photo-1722973681429-04332f5cb901?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fHBlcmVubmlhbCUyMGJsb29tc3xlbnwwfHx8fDE3NjM0NTYxOTl8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop"
                    alt="Perennial blooms"
                    class="h-8 w-8 rounded-full ring-2 ring-white shadow"
                    cms-block-field="avatar_image_2"
                    cms-block-field-type="image"
                    cms-block-field-title="Avatar Image 2"
                  />
                  <img
                    src="https://images.unsplash.com/photo-1757917702671-fff5a7b3c2e3?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDEyfHx2ZWdldGFibGUlMjBoYXJ2ZXN0fGVufDB8fHx8MTc2MzQ1NjIwMHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop"
                    alt="Autumn vegetable harvest"
                    class="h-8 w-8 rounded-full ring-2 ring-white shadow"
                    cms-block-field="avatar_image_3"
                    cms-block-field-type="image"
                    cms-block-field-title="Avatar Image 3"
                  />
                </div>
                <p
                  class="text-sm text-gray-700"
                  cms-block-field="social_proof_text"
                  cms-block-field-type="content"
                  cms-block-field-title="Social Proof Text"
                >
                  Fresh, actionable advice for your beds and planters.
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
