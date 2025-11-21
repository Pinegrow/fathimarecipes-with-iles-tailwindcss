<script setup lang="ts">
  defineProps({
    currentPath: {
      type: String,
      default: '/',
    },
  })

  const { allNavs, navsPrimary, navsSecondary } = useNavMenu()
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
              :navs="navsPrimary"
              :current-path="currentPath"
              class="hidden sm:flex sm:ml-6"
              client:media="screen and (min-width: 640px)"
            />
          </div>
          <DarkModeSwitch client:load />
          <div class="-mr-2 items-center relative">
            <NavHamburger
              v-if="navsSecondary.length"
              class="hidden sm:block"
              client:load
            />
            <NavHamburger v-if="allNavs.length" class="sm:hidden" client:load />
            <NavSecondary
              class="hidden sm:flex sm:justify-end absolute right-0 mt-4"
              :navs="navsSecondary"
              :current-path="currentPath"
              client:media="screen and (min-width: 640px)"
            />
          </div>
        </div>
      </div>
      <NavSecondary
        class="sm:hidden"
        :navs="allNavs"
        :current-path="currentPath"
        client:media="screen and (max-width: 640px)"
      />
    </nav>
  </div>
</template>
<style scoped></style>
