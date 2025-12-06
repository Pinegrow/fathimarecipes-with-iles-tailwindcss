import type { DirectusClient, RestClient } from '@directus/sdk'
import type { Schema } from '@/types/schema'

declare module '#app' {
  interface NuxtApp {
    $directus: DirectusClient<Schema> & RestClient<Schema>
    $readItem: (typeof import('@directus/sdk'))['readItem']
    $readItems: (typeof import('@directus/sdk'))['readItems']
  }
}

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $directus: DirectusClient<Schema> & RestClient<Schema>
    $readItem: (typeof import('@directus/sdk'))['readItem']
    $readItems: (typeof import('@directus/sdk'))['readItems']
  }
}

export {}
