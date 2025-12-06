import {
  createDirectus,
  rest,
  readItem,
  readItems,
  readSingleton,
} from '@directus/sdk'
import type { Schema } from '@/types/schema'

export const useDirectus = () => {
  const directus = createDirectus<Schema>(
    import.meta.env.VITE_DIRECTUS_URL || 'http://localhost:8055',
  ).with(rest())
  return { directus, readItem, readItems, readSingleton }
}
