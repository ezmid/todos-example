# Install optimized deps
FROM ezmid/caddy-s4:1.0.2 AS vendor
RUN apk add yarn
COPY . /app
RUN composer install --no-dev --no-scripts --no-suggest --optimize-autoloader
RUN yarn install
RUN yarn build
RUN rm -f .env
RUN rm -rf assets
RUN rm -rf docker
RUN rm -rf docs
RUN rm -rf node_modules
RUN rm -rf var/*
RUN rm -rf vendor/*
RUN composer install --no-dev --no-scripts --no-suggest --optimize-autoloader

# Copy deps to base image
FROM ezmid/caddy-s4:1.0.2
RUN apk del git
COPY --from=vendor /app .
