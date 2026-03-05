1. Project Overview
Ping Pulse is a blazing-fast, fullstack single-page web application designed to test network latency and API readiness for high-speed gaming servers and AI endpoints.

Design Language: Cyber-Brutalism (Pitch black backgrounds, neon cyan/magenta accents, sharp 4px solid borders, no border-radius, and monospace typography).

2. Tech Stack (Fullstack JS)
Framework: Astro JS (Handles both Server-Side Rendering and static UI).

Database: Astro DB (Managed SQLite, built directly into the repo).

Styling: Tailwind CSS (For rapid Cyber-Brutalist utility classes).

Interactivity: Vanilla JS (For the Modal and "Hacker" text sequence).

Backend APIs: Astro API Endpoints (.ts files running securely on the server to hide API keys).

Hosting: Vercel or Cloudflare Pages (Edge network speed, native Astro DB support).

3. Asset & Image Sourcing Strategy
Custom Graphics (Logos, Icons, UI Elements): Generated using Nano Banana Pro (Gemini 3 Flash Image model) to ensure a unique, cohesive Cyberpunk/Brutalist aesthetic.

Third-Party Media (Game Covers, Backgrounds): Fetched dynamically from the internet (e.g., IGDB for game covers, Unsplash for API backgrounds) to keep the repository lightweight and fast.

4. Database Schema & Seed Data (Astro DB / SQLite)
The database handles dynamic loading of test targets.

Table: PingTarget

id (String, Primary Key) - e.g., 'apex-sg'

name (String) - e.g., 'Apex Legends'

type (String) - 'game' or 'api' (Used to style cards differently, e.g., green for games, magenta for APIs)

region (String) - e.g., 'Singapore'

url (String) - e.g., 'https://dynamodb.ap-southeast-1.amazonaws.com/ping'

imageUrl (String) - External URL for the card background

5. UI/UX Architecture
The application is a single landing page to minimize friction.

A. The Landing Page (The Grid)
Header: Features the Nano Banana Pro-generated "PING PULSE" logo.

Main Content: A responsive grid displaying cards fetched directly from the SQLite database via Astro's server-side rendering.

Interaction: Clicking a card prevents default behavior, grabs the target url and type via data-attributes, and opens the Testing Modal.

B. The Testing Modal (The Terminal)
Overlay: A stark, semi-transparent black overlay with backdrop blur.

Container: A thick-bordered, neon-shadowed box (Cyber-Brutalist window).

The "Hacker" Sequence: When "TEST CONNECTION" is clicked:

A Vanilla JS setInterval cycles through hacker phrases ("INITIALIZING QUANTUM ROUTING...", "BYPASSING MAINFRAME FIREWALL...").

The frontend sends a fetch() request to our secure internal Astro API route (/api/ping).

The server pings the actual target (hiding the Gemini API key).

The result (Latency & Status) is returned and the hacking text stops, revealing the final ping time and a Green/Yellow/Red indicator.

6. Project Structure
Plaintext
ping-pulse/
├── db/
│   ├── config.ts            # SQLite Database Schema defined here
│   └── seed.ts              # Initial database records (Apex, LoL, Gemini, etc.)
├── src/
│   ├── components/
│   │   ├── Header.astro     # Nano Banana Pro logo & Title
│   │   ├── TargetCard.astro # Cyber-Brutalist grid item (props: target data)
│   │   └── Modal.astro      # Hidden overlay & testing logic (Vanilla JS)
│   ├── pages/
│   │   ├── index.astro      # Main page (Queries Astro DB, maps TargetCards)
│   │   └── api/
│   │       └── ping.ts      # Secure backend route to execute the actual pings
├── package.json
└── astro.config.mjs
7. Step-by-Step Implementation Guide
Phase 1: Skeleton & Database Setup
Run npm create astro@latest (Choose "Empty").

Run npx astro add db and npx astro add tailwind to configure the fullstack environment.

Define the PingTarget schema in db/config.ts.

Add the initial targets (Apex, Marvel Rivals, LoL, Helldivers, Fortnite, Gemini API) to db/seed.ts with their respective IGDB image URLs.

Phase 2: Building the UI (Cyber-Brutalism)
Setup global CSS variables in a BaseLayout.astro for neon colors, monospace fonts, and sharp shadows.

Build TargetCard.astro to display the database records. Add conditional styling based on type === 'game' or type === 'api'.

Wire up index.astro to query Astro DB and render the cards.

Phase 3: The Backend Ping Route
Create src/pages/api/ping.ts.

Write a server-side function that accepts a url and type from the request.

If type === 'api', append the GEMINI_API_KEY (stored in .env) to the URL and measure the HTTP request time.

If type === 'game', measure the HTTP HEAD request time to the AWS endpoint.

Return the latency and status (Healthy/Degraded/Error) as JSON.

Phase 4: The Modal & Frontend Logic
Build Modal.astro.

Add client-side <script> to handle card clicks, open the modal, and pass the target data.

Write the "Hacker Sequence" text cycling logic.

Wire the "TEST" button to call our internal /api/ping endpoint and display the results.

Phase 5: Polish & Assets
Prompt Nano Banana Pro to create the final SVG/PNG logo.

Refine the CSS hover states (e.g., glitch effects or hard translation shadows).

Deploy the fullstack app.