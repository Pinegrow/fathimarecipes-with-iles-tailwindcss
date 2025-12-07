# --- Build stage ---
FROM node:18-alpine AS builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

RUN rm -rf node_modules/.vite .vite .cache

COPY . .
RUN npm run build   # creates /app/dist

# --- Static server stage ---
FROM nginx:alpine
COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]