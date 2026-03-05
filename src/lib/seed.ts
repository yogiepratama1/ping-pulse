import pg from 'pg';
import 'dotenv/config'
const { Pool } = pg;

const DATABASE_URL = process.env.DATABASE_URL
const pool = new Pool({ connectionString: DATABASE_URL });

const targets = [
    {
        id: 'apex-sg',
        name: 'Apex Legends',
        type: 'game',
        region: 'Singapore',
        url: 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
        image_url:
            'https://media.contentapi.ea.com/content/dam/apex-legends/common/articles/apex-legends-arsenal-background.jpg.adapt.crop191x100.1200w.jpg',
    },
    {
        id: 'marvel-sg',
        name: 'Marvel Rivals',
        type: 'game',
        region: 'Singapore',
        url: 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
        image_url:
            'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/2767030/header.jpg',
    },
    {
        id: 'lol-sea',
        name: 'League of Legends',
        type: 'game',
        region: 'SEA',
        url: 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
        image_url:
            'https://cmsassets.rgpub.io/sanity/images/dsfx7636/news/4291a09d2e4095a2747e011798703113fb80e553-1920x1080.jpg',
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
    console.log('🔧 Connecting to database...');
    const client = await pool.connect();

    try {
        console.log('📦 Creating ping_targets table...');
        await client.query(`
      CREATE TABLE IF NOT EXISTS ping_targets (
        id         TEXT PRIMARY KEY,
        name       TEXT NOT NULL,
        type       TEXT NOT NULL CHECK (type IN ('game', 'api')),
        region     TEXT NOT NULL,
        url        TEXT NOT NULL,
        image_url  TEXT NOT NULL
      );
    `);

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

seed().catch((err) => {
    console.error('❌ Seed failed:', err);
    process.exit(1);
});
