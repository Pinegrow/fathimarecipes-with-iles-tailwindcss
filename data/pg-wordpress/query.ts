/* Auto-generated logic by Vue Designer Headless Wordpress 6.7 begins */
/* eslint-disable */
// @ts-nocheck

import { $fetch } from 'ofetch'
import type {
  WP_REST_API_Posts,
  WP_REST_API_Post,
  WP_REST_API_Categories,
  WP_REST_API_Category,
  WP_REST_API_Tags,
  WP_REST_API_Tag,
  WP_REST_API_Comments,
  WP_REST_API_Comment,
  WP_REST_API_Attachments,
  WP_REST_API_Attachment,
  WP_REST_API_Users,
  WP_REST_API_User,
} from 'wp-types'

const wordpressUrl: string = import.meta.env.VITE_WORDPRESS_URL

interface FetchArgs {
  filters?: Record<string, string | number | string[]>
  fields?: string[]
}

const fetchWordpressData = async <T>({
  filters = {},
  fields = [],
  endpoint,
  endpointIsACollection = true,
}: FetchArgs & { endpoint: string }): Promise<T | null> => {
  const filtersArgs = Object.entries(filters).reduce((acc, [filter, value]) => {
    const filterRecord = `${filter}=${value}`
    return [...acc, filterRecord]
  }, [] as string[])

  const fieldsParam = []
  const fieldsVal = fields.join(',')
  if (fieldsVal) {
    fieldsParam.push(`_fields=${fieldsVal}`)
  }

  const args = [...filtersArgs, ...fieldsParam].join('&')

  let response

  if (endpointIsACollection) {
    response = await $fetch<T>(
      `${wordpressUrl}/wp-json/wp/v2/${endpoint}${args ? `?${args}` : ''}`,
    )
  } else {
    if (filters.id) {
      response = await $fetch<T>(
        `${wordpressUrl}/wp-json/wp/v2/${endpoint}/${filters.id}${filters._embed ? `?_embed=${filters._embed}` : ''}`,
      )
    } else {
      response = null // No data is returned, caller should use v-if to ensure this is handled
    }
  }
  return response
}

const getPgWordpressPosts = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Posts> => {
  return fetchWordpressData<WP_REST_API_Posts>({ ...args, endpoint: 'posts' })
}

const getPgWordpressPost = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Post> => {
  return fetchWordpressData<WP_REST_API_Post>({
    ...args,
    endpoint: 'posts',
    endpointIsACollection: false,
  })
}

const getPgWordpressCategories = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Categories> => {
  return fetchWordpressData<WP_REST_API_Categories>({
    ...args,
    endpoint: 'categories',
  })
}

const getPgWordpressCategory = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Category> => {
  return fetchWordpressData<WP_REST_API_Category>({
    ...args,
    endpoint: 'categories',
    endpointIsACollection: false,
  })
}

const getPgWordpressTags = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Tags> => {
  return fetchWordpressData<WP_REST_API_Tags>({
    ...args,
    endpoint: 'tags',
  })
}

const getPgWordpressTag = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Tag> => {
  return fetchWordpressData<WP_REST_API_Tag>({
    ...args,
    endpoint: 'tags',
    endpointIsACollection: false,
  })
}

const getPgWordpressComments = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Comments> => {
  return fetchWordpressData<WP_REST_API_Comments>({
    ...args,
    endpoint: 'comments',
  })
}

const getPgWordpressComment = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Comment> => {
  return fetchWordpressData<WP_REST_API_Comment>({
    ...args,
    endpoint: 'comments',
    endpointIsACollection: false,
  })
}

const getPgWordpressAttachments = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Attachments> => {
  return fetchWordpressData<WP_REST_API_Attachments>({
    ...args,
    endpoint: 'media',
  })
}

const getPgWordpressAttachment = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Attachment> => {
  return fetchWordpressData<WP_REST_API_Attachment>({
    ...args,
    endpoint: 'media',
    endpointIsACollection: false,
  })
}

const getPgWordpressUsers = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_Users> => {
  return fetchWordpressData<WP_REST_API_Users>({
    ...args,
    endpoint: 'users',
  })
}

const getPgWordpressUser = async (
  args: FetchArgs = {},
): Promise<WP_REST_API_User> => {
  return fetchWordpressData<WP_REST_API_User>({
    ...args,
    endpoint: 'users',
    endpointIsACollection: false,
  })
}

interface WP_SITE_INFO {
  name: string
  description: string
  url: string
  home: string
}

const getPgWordpressSiteinfo = async ({}): Promise<WP_SITE_INFO> => {
  const fields = ['name', 'description', 'url', 'home']
  const args = fields.join('&')
  const siteInfo = await $fetch(
    `${wordpressUrl}/wp-json${args ? `?${args}` : ''}`,
  )
  return siteInfo
}

const usePgWordpressData = () => {
  return {
    getPgWordpressSiteinfo,
    getPgWordpressPosts,
    getPgWordpressPost,
    getPgWordpressCategories,
    getPgWordpressCategory,
    getPgWordpressTags,
    getPgWordpressTag,
    getPgWordpressComments,
    getPgWordpressComment,
    getPgWordpressAttachments,
    getPgWordpressAttachment,
    getPgWordpressUsers,
    getPgWordpressUser,
  }
}

export { usePgWordpressData }
/* Auto-generated logic by Vue Designer Headless Wordpress 6.7 ends */
