// Return types as a variable
import { generateDirectusTypes } from 'directus-sdk-typegen'
import { loadEnv } from 'vite'

async function generateTypes() {
  try {
    const env = loadEnv('', process.cwd())

    const types = await generateDirectusTypes({
      directusUrl: env.VITE_DIRECTUS_URL,
      directusToken: env.VITE_DIRECTUS_SERVER_TOKEN,
    })
    // Do something else with the types
    console.log(types)
  } catch (error) {
    console.error('Failed to generate types:', error)
  }
}

generateTypes()

export { generateTypes }
