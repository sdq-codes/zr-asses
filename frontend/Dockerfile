# Use the official Node.js image
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Install system dependencies for native modules
RUN apk add --no-cache \
    python3 \
    make \
    g++ \
    cairo-dev \
    jpeg-dev \
    pango-dev \
    musl-dev \
    giflib-dev \
    pixman-dev \
    pangomm-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    curl

# Copy package files
COPY package*.json ./

# Install ALL dependencies (including devDependencies)
RUN npm install --prefer-frozen-lockfile

# Copy the rest of the application
COPY . .

# Expose Vite dev server port
EXPOSE 5173

# Set environment variables for dev
ENV NODE_ENV=development

# Use non-root user for security
RUN addgroup -g 1001 -S nodejs && \
    adduser -S nuxt -u 1001
RUN chown -R nuxt:nodejs /app
USER nuxt

# Health check (Vite dev runs on 5173)
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:5173/ || exit 1

# Run Vite dev server with hot reload
CMD ["npm", "run", "dev", "--", "--host"]
