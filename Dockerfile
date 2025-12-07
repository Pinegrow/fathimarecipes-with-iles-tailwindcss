# ================================
# 1) BUILD STAGE
# ================================
FROM node:18-alpine AS builder

WORKDIR /app

# Only copy dependency manifests first (faster caching)
COPY package.json package-lock.json ./

# Install dependencies
RUN npm install

# Copy the full source code
COPY . .

# Build îles → output goes into /app/dist
RUN npm run build


# ================================
# 2) STATIC SERVER STAGE
# ================================
FROM nginx:alpine

# Set working directory (not required but clean)
WORKDIR /usr/share/nginx/html

# Copy our custom nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copy built static site from builder
COPY --from=builder /app/dist /usr/share/nginx/html

# Expose port 80 (Coolify will reverse-proxy to this)
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]