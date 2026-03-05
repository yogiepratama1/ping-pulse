# ⚡ Ping Pulse

**Blazing-fast network latency tester** for gaming servers and API endpoints.  
Built with Astro 5, Go, PostgreSQL, and Tailwind CSS v4.

> Ping Pulse measures real HTTP round-trip latency using a compiled Go binary — no JavaScript `fetch` overhead. Each test runs **10 measured pings**, reporting min / avg / max and status.

---

## 🎯 Features

- **Go-powered pinger** — compiled binary for accurate latency measurements
- **1 warmup + 10 measured pings** — returns min, avg, max, individual results, and health status
- **Game servers & API endpoints** — supports HEAD pings for game servers, GET for APIs
- **SQLite / PostgreSQL storage** — zero-config SQLite locally, or PostgreSQL for production
- **SSR with Astro** — server-rendered pages with API routes
- **Docker-ready** — multi-stage build with full compose stack

## 🛠 Tech Stack

| Layer      | Tech                                |
| :--------- | :---------------------------------- |
| Frontend   | Astro 5 (SSR), Tailwind CSS 4       |
| Pinger     | Go 1.23                             |
| Database   | SQLite (local) / PostgreSQL (prod)  |
| Runtime    | Node.js (standalone adapter)         |
| Deployment | Docker, Docker Compose              |

## 📁 Project Structure

```
ping-pulse/
├── go/
│   ├── go.mod              # Go module
│   └── ping.go             # Go pinger binary source
├── bin/                    # Compiled Go binary (gitignored)
├── src/
│   ├── components/
│   │   ├── Header.astro    # Site header
│   │   ├── Modal.astro     # Ping test modal with terminal UI
│   │   └── TargetCard.astro # Target card component
│   ├── layouts/
│   │   └── BaseLayout.astro
│   ├── lib/
│   │   ├── db.ts           # SQLite / PostgreSQL connection helpers
│   │   └── seed.ts         # Database seeder script
│   ├── pages/
│   │   ├── api/
│   │   │   └── ping.ts     # API route — shells out to Go binary
│   │   └── index.astro     # Main page
│   └── styles/
│       └── global.css      # Design system & tokens
├── Dockerfile              # Multi-stage: Go build → Node build → slim runtime
├── docker-compose.yml      # PostgreSQL + app services
├── .env.example            # Environment variable template
└── package.json
```

## 🚀 Installation & Running

Choose one of the two options below to run the project.

### Option A: Without Docker (Zero Config, SQLite)

This is the fastest way to get started locally. It uses a local SQLite database file.

**Prerequisites:** Node.js 18+ and Go 1.23+

1. **Install dependencies:**
   ```sh
   npm install
   ```
2. **Set up environment:**
   ```sh
   cp .env.example .env
   ```
   *(By default, `DB_TYPE=sqlite` is used.)*
3. **Start the app:**
   ```sh
   npm run dev or npm run start
   ```
   *Note: `npm run dev or npm run start` automatically compiles the Go binary, seeds the local database, and starts the Astro dev server.*

Open [http://localhost:4321](http://localhost:4321) and click any target card to test latency.

---

### Option B: With Docker (PostgreSQL)

This option runs both the Node.js application and a PostgreSQL 17 database in Docker containers.

**Prerequisites:** Docker and Docker Compose

1. **Set up environment:**
   ```sh
   cp .env.example .env
   ```
   Open `.env` and change `DB_TYPE=sqlite` to `DB_TYPE=postgres`.
2. **Build and start the stack:**
   ```sh
   docker compose up --build
   ```
   *(This uses a multi-stage Dockerfile that automatically compiles the Go binary and builds the Astro site, deploying it alongside PostgreSQL.)*

The app will be available at [http://localhost:4321](http://localhost:4321).

## 📡 API

### `POST /api/ping`

Triggers the Go pinger binary and returns latency results.

**Request body:**

```json
{
  "url": "https://dynamodb.ap-southeast-1.amazonaws.com/ping",
  "type": "game"
}
```

**Response:**

```json
{
  "latency": 14,
  "min": 13,
  "max": 16,
  "pings": [14, 13, 14, 15, 16, 14, 15, 14, 14, 13],
  "status": "Healthy"
}
```

| Status       | Condition          |
| :----------- | :----------------- |
| `Healthy`    | avg ≤ 300ms        |
| `Degraded`   | avg > 300ms        |
| `Error`      | request failed     |

## 🧞 Commands

| Command           | Action                                    |
| :---------------- | :---------------------------------------- |
| `npm run dev`     | Seeds the DB and starts dev server at 4321 |
| `npm run start`   | Seeds the DB and runs the production build |
| `npm install`     | Install dependencies                      |
| `npm run build`   | Build for production to `./dist/`         |
| `npm run preview` | Preview production build locally          |
| `npm run seed`    | Manually seed the database                |

## 🌿 Branches

| Branch             | Design Style    |
| :----------------- | :-------------- |
| `main`             | Neo-Brutalism   |
| `cyber-brutalist`  | Cyber-Brutalist |

## 📜 License

Creative Commons Attribution-NonCommercial (CC BY-NC 4.0)
