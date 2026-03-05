import pg from 'pg';
import Database from 'better-sqlite3';

const dbType = import.meta.env.DB_TYPE || process.env.DB_TYPE || 'sqlite';

let pgPool: pg.Pool | null = null;
let sqliteDb: Database.Database | null = null;

function getSqliteDb(): Database.Database {
    if (!sqliteDb) {
        sqliteDb = new Database('pingpulse.db');
        sqliteDb.pragma('journal_mode = WAL');
    }
    return sqliteDb;
}

function getPgPool(): pg.Pool {
    if (!pgPool) {
        pgPool = new pg.Pool({
            connectionString: import.meta.env.DATABASE_URL || process.env.DATABASE_URL,
        });
    }
    return pgPool;
}

/** Run a parameterized query and return the rows. */
export async function query<T = any>(
    text: string,
    params: any[] = [],
): Promise<T[]> {
    if (dbType === 'sqlite') {
        const sqliteQuery = text.replace(/\$\d+/g, '?');
        const stmt = getSqliteDb().prepare(sqliteQuery);
        return stmt.all(...params) as T[];
    } else {
        const client = await getPgPool().connect();
        try {
            const result = await client.query(text, params);
            return result.rows as T[];
        } finally {
            client.release();
        }
    }
}

/** Create the ping_targets table if it doesn't exist. */
export async function initDb(): Promise<void> {
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
        getSqliteDb().exec(createTableQuery);
    } else {
        await query(createTableQuery);
    }
}

export interface PingTarget {
    id: string;
    name: string;
    type: 'game' | 'api';
    region: string;
    url: string;
    image_url: string;
}
