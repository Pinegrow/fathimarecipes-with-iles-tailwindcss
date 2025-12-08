# Stage 1: Build the static files
FROM node:18-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build # Outputs to /app/dist

# Stage 2: Serve the files using Nginx
FROM nginx:alpine
# Copy the built application files
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy the custom Nginx configuration file to override the default one
COPY nginx.conf /etc/nginx/nginx.conf

# Nginx starts automatically
EXPOSE 80
