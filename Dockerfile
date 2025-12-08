# Stage 1: Build the static files
FROM node:18-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build # Outputs to /app/dist

# Stage 2: Serve the files using Nginx
FROM nginx:alpine
# Copy the built files from the 'builder' stage into Nginx's serving directory
COPY --from=builder /app/dist /usr/share/nginx/html
# Nginx starts automatically
EXPOSE 80
