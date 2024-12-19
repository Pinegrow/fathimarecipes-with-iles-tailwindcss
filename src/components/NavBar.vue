<script setup lang="ts">
  import { computed } from 'vue'
  import { useNavMenu } from '@/composables/nav-menu'

  const { navlinks, currentPath } = useNavMenu()
  const horizontalNavlinks = computed(() => {
    return navlinks.value.slice(0, 2)
  })
  const verticalNavlinks = computed(() => {
    return navlinks.value.slice(2, navlinks.value.length)
  })

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
  <div class="w-full">
    <nav class>
      <div class="container mx-auto px-4 sm:px-6">
        <div class="flex h-24 items-center justify-between">
          <div class="flex items-center justify-between w-full">
            <div class="flex flex-shrink-0 items-center" data-pg-siteinfo>
              <BaseIcon
                name="i-vscode-icons-file-type-coffeelint"
                height="32px"
              ></BaseIcon>
              <a href="/" class="text-primary-600 dark:text-primary-200">
                <h5 class="font-extrabold mb-0 ml-2">{{ pgSiteinfo.name }}</h5>
              </a>
            </div>
            <NavPrimary
              :navlinks="horizontalNavlinks"
              :current-path="currentPath"
              class="hidden sm:flex sm:ml-6"
              client:media="screen and (min-width: 640px)"
            />
          </div>
          <DarkModeSwitch client:load />
          <div class="-mr-2 items-center relative">
            <NavBarHamburger
              v-if="verticalNavlinks.length"
              class="hidden sm:block"
              client:load
            />
            <NavBarHamburger
              v-if="navlinks.length"
              class="sm:hidden"
              client:load
            />
            <NavSecondary
              class="hidden sm:flex sm:justify-end absolute right-0 mt-4"
              :navlinks="verticalNavlinks"
              :current-path="currentPath"
              client:media="screen and (min-width: 640px)"
            />
          </div>
        </div>
      </div>
      <NavSecondary
        class="sm:hidden"
        :navlinks="navlinks"
        :current-path="currentPath"
        client:media="screen and (max-width: 640px)"
      />
    </nav>
  </div>
</template>
<style scoped></style>
