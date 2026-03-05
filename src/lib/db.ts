import pg from 'pg';

const { Pool } = pg;

let pool: pg.Pool | null = null;

function getPool(): pg.Pool {
    if (!pool) {
        pool = new Pool({
            connectionString: import.meta.env.DATABASE_URL,
        });
    }
    return pool;
}

/** Run a parameterized query and return the rows. */
export async function query<T extends pg.QueryResultRow = any>(
    text: string,
    params?: any[],
): Promise<T[]> {
    const client = await getPool().connect();
    try {
        const result = await client.query<T>(text, params);
        return result.rows;
    } finally {
        client.release();
    }
}

/** Create the ping_targets table if it doesn't exist. */
export async function initDb(): Promise<void> {
    await query(`
    CREATE TABLE IF NOT EXISTS ping_targets (
      id         TEXT PRIMARY KEY,
      name       TEXT NOT NULL,
      type       TEXT NOT NULL CHECK (type IN ('game', 'api')),
      region     TEXT NOT NULL,
      url        TEXT NOT NULL,
      image_url  TEXT NOT NULL
    );
  `);
}

export interface PingTarget {
    id: string;
    name: string;
    type: 'game' | 'api';
    region: string;
    url: string;
    image_url: string;
}
