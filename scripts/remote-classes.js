import fs from 'fs'
import { loadEnv } from 'vite'

// Load env from project root
const env = loadEnv('', process.cwd())

const res = await fetch(`${env.VITE_WORDPRESS_URL}/wp-json/wp/v2/blocks`)
const json = await res.json()

let markup = ''
for (const block of json) {
  if (block.content && block.content.rendered) {
    markup += block.content.rendered
  }
}

function decode(str) {
  return str
    .replace(/\\n/g, '\n') // remove \n
    .replace(/\\"/g, '"') // remove \"
    .replace(/\\'/g, "'") // remove \'
    .replace(/\\\\/g, '\\') // remove escaped slash
    .replace(/&quot;/g, '"') // html-encoded "
    .replace(/&#039;/g, "'") // html-encoded '
    .replace(/&amp;/g, '&') // html-encoded &
}

const decodedMarkup = decode(markup)

fs.writeFileSync('src/assets/remote-classes.html', decodedMarkup)
