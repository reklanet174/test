FROM node:20-alpine AS build

WORKDIR /app
COPY frontend/package.json frontend/package-lock.json* ./
RUN npm install
COPY frontend/ ./
RUN npm run build

# swagger ui локально, без CDN
RUN mkdir -p /swagger-ui \
  && cd /tmp \
  && npm pack swagger-ui-dist@5.17.14 \
  && tar -xzf swagger-ui-dist-*.tgz \
  && cp package/swagger-ui.css package/swagger-ui-bundle.js package/swagger-ui-standalone-preset.js /swagger-ui/

FROM nginx:1.27-alpine
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html
COPY --from=build /swagger-ui /usr/share/nginx/html/swagger-ui
COPY openapi.yaml /usr/share/nginx/html/openapi.yaml
COPY docker/nginx/swagger.html /usr/share/nginx/html/docs/index.html

EXPOSE 80
