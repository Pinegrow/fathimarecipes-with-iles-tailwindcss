FROM node:18-alpine AS builder
WORKDIR /app

COPY package*.json ./
RUN npm ci     # deterministic, fast, consistent builds

COPY . .
RUN npm run build    # outputs /app/dist

FROM nginx:alpine
COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
