import pg from 'pg';
import Database from 'better-sqlite3';
import 'dotenv/config';

// Determine the database type to use (default to sqlite)
const dbType = process.env.DB_TYPE || 'sqlite';

const targets = [
    {
        id: 'apex-sg',
        name: 'Apex Legends',
        type: 'game',
        region: 'Singapore',
        url: 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
        image_url:
            'https://www.dexerto.com/cdn-image/wp-content/uploads/2024/08/01/apex-season-22.jpg',
    },
    {
        id: 'marvel-sg',
        name: 'Marvel Rivals',
        type: 'game',
        region: 'Singapore',
        url: 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
        image_url:
            'https://images3.alphacoders.com/138/1386592.jpg',
    },
    {
        id: 'lol-sea',
        name: 'League of Legends',
        type: 'game',
        region: 'SEA',
        url: 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
        image_url:
            'https://interestingfacts.co.za/wp-content/uploads/2024/09/League-Of-Legends-1536x864.jpg',
    },
    {
        id: 'helldivers-sg',
        name: 'Helldivers 2',
        type: 'game',
        region: 'Singapore',
        url: 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
        image_url:
            'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/553850/header.jpg',
    },
    {
        id: 'fortnite-tokyo',
        name: 'Fortnite',
        type: 'game',
        region: 'Tokyo',
        url: 'https://dynamodb.ap-northeast-1.amazonaws.com/ping',
        image_url:
            'https://cdn2.unrealengine.com/social-image-chapter4-s3-3840x2160-d35912cc25ad.jpg',
    },
    {
        id: 'gemini-api',
        name: 'Gemini API',
        type: 'api',
        region: 'Global',
        url: 'https://generativelanguage.googleapis.com/v1beta/models',
        image_url:
            'https://storage.googleapis.com/gweb-uniblog-publish-prod/images/Gemini_SS.width-1300.jpg',
    },
];

async function seed() {
    console.log(`🔧 Connecting to ${dbType === 'sqlite' ? 'SQLite' : 'PostgreSQL'} database...`);

    const createTableQuery = `
      CREATE TABLE IF NOT EXISTS ping_targets (
        id         TEXT PRIMARY KEY,
        name       TEXT NOT NULL,
        type       TEXT NOT NULL CHECK (type IN ('game', 'api')),
        region     TEXT NOT NULL,
        url        TEXT NOT NULL,
        image_url  TEXT NOT NULL
      );
    `;

    if (dbType === 'sqlite') {
        const db = new Database('pingpulse.db');
        db.pragma('journal_mode = WAL');

        try {
            console.log('📦 Creating ping_targets table...');
            db.exec(createTableQuery);

            console.log('🌱 Seeding targets...');
            const stmt = db.prepare(`
                INSERT INTO ping_targets (id, name, type, region, url, image_url)
                VALUES (?, ?, ?, ?, ?, ?)
                ON CONFLICT (id) DO UPDATE SET
                  name = excluded.name,
                  type = excluded.type,
                  region = excluded.region,
                  url = excluded.url,
                  image_url = excluded.image_url;
            `);

            // Use a transaction for better performance
            const insertMany = db.transaction((targets) => {
                for (const t of targets) {
                    stmt.run(t.id, t.name, t.type, t.region, t.url, t.image_url);
                    console.log(`  ✅ ${t.name} (${t.region})`);
                }
            });

            insertMany(targets);
            console.log('🎉 Seeding complete!');
        } finally {
            db.close();
        }

    } else {
        const pool = new pg.Pool({ connectionString: process.env.DATABASE_URL });
        const client = await pool.connect();

        try {
            console.log('📦 Creating ping_targets table...');
            await client.query(createTableQuery);

            console.log('🌱 Seeding targets...');
            for (const t of targets) {
                await client.query(
                    `INSERT INTO ping_targets (id, name, type, region, url, image_url)
             VALUES ($1, $2, $3, $4, $5, $6)
             ON CONFLICT (id) DO UPDATE SET
               name = EXCLUDED.name,
               type = EXCLUDED.type,
               region = EXCLUDED.region,
               url = EXCLUDED.url,
               image_url = EXCLUDED.image_url;`,
                    [t.id, t.name, t.type, t.region, t.url, t.image_url],
                );
                console.log(`  ✅ ${t.name} (${t.region})`);
            }
            console.log('🎉 Seeding complete!');
        } finally {
            client.release();
            await pool.end();
        }
    }
}

seed().catch((err) => {
    console.error('❌ Seed failed:', err);
    process.exit(1);
});
