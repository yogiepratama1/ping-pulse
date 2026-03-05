FROM golang:1.23-alpine AS go-build
WORKDIR /app/go
COPY go/ .
RUN go build -o /app/bin/ping ping.go

FROM node:lts AS node-build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
COPY --from=go-build /app/bin/ping ./bin/ping
RUN npm run build

FROM node:lts-slim AS runtime
WORKDIR /app
COPY --from=node-build /app/dist ./dist
COPY --from=node-build /app/node_modules ./node_modules
COPY --from=go-build /app/bin/ping ./bin/ping
ENV HOST=0.0.0.0
ENV PORT=4321
EXPOSE 4321
CMD ["node", "./dist/server/entry.mjs"]
