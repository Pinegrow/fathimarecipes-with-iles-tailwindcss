<page> 
    #layout: default
    #name: slug
    #alias: slug
    title: Slug
    description: Slug
    #hidden: true
    navOrder: 3
    type: primary
    icon: i-mdi-home
</page>
<script lang="ts">
  // import { fetchArticles } from '~/composables/articles'
  import { WP_REST_API_POST } from 'wp-types'

  /* https://github.com/directus/eslint-config/issues/2 */
  export default definePageComponent({
    async getStaticPaths() {
      /* Auto-generated logic by Vue Designer Headless Wordpress 6.7 begins */
      const { getPgWordpressPosts } = usePgWordpressData()
      const pgData: any = {
        pgWordpressData: {},
      }

      const pgPostsArgs = {
        posts: { filters: { per_page: '10', _embed: 'true' } },
      }

      pgData.pgWordpressData.posts = {
        ...pgPostsArgs.posts,
        data: await getPgWordpressPosts(pgPostsArgs.posts),
      }

      const pgPosts = pgData.pgWordpressData.posts.data
      /* Auto-generated logic by Vue Designer Headless Wordpress 6.7 ends */

      // pgPosts.forEach((pgPost: WP_REST_API_POST) => {
      //   pgPost._embedded['wp:featuredmedia'] = pgPost._embedded[
      //     'wp:featuredmedia'
      //   ].map((media: any) => ({
      //     ...media,
      //     source_url: `${import.meta.env.VITE_WORDPRESS_URL}${media.source_url}`,
      //   }))
      //   pgPost.content.rendered = pgPost.content.rendered.replaceAll(
      //     '/wp-content/',
      //     `${import.meta.env.VITE_WORDPRESS_URL}/wp-content/`,
      //   )
      // })

      const recipes = pgPosts.map((recipe) => {
        return {
          ...recipe,
          dateDisplay: new Date(recipe.date).toDateString(),
        }
      })

      const articles = recipes /*await fetchArticles()*/

      return articles.map((article) => ({
        // Specify the parameters for the page.
        params: { slug: String(article.slug) },

        // Pass any data needed to render the page.
        props: { article },
      }))
    },
  })
</script>
<script setup lang="ts">
  // import { usePage } from 'iles'

  const { article } = defineProps({
    article: {
      type: Object,
      default: () => ({}),
    },
  })

  // const { frontmatter, meta } = usePage()

  useHead({
    title: article?.title,
  })
</script>
<template layout="default">
  <div>
    <article class="max-w-4xl mx-auto px-4 py-12">
      <div class="space-y-8">
        <!-- Featured Image -->
        <img
          alt="Homemade Pasta Dish"
          class="w-full h-[500px] object-cover rounded-lg shadow-lg"
          :src="article._embedded['wp:featuredmedia'][0].source_url"
        />
        <!-- Article Header -->
        <header class="space-y-4">
          <h1
            class="font-serif text-4xl md:text-5xl text-primary-800 leading-tight"
            v-html="article.title.rendered"
          ></h1>
          <div class="flex items-center space-x-4 text-sm text-gray-600">
            <time>{{ article.dateDisplay }}</time>
          </div>
          <!-- Categories and Tags -->
          <div class="flex flex-wrap gap-2">
            <span
              v-for="(category, index) in article._embedded['wp:term'][0]"
              :key="index"
              class="px-3 py-1 bg-secondary-100 text-secondary-800 rounded-full text-sm"
              >{{ category.name }}</span
            >
          </div>
        </header>
        <!-- Article Excerpt -->
        <!-- Article Content -->
        <div class="max-w-none prose prose-lg dark:prose-invert">
          <div v-html="article.content.rendered"></div>
        </div>
      </div>
    </article>
  </div>
</template>
<style scoped></style>
