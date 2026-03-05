import type { APIRoute } from 'astro';
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import 'dotenv/config'

const execFileAsync = promisify(execFile);

export const POST: APIRoute = async ({ request }) => {
    try {
        const body = await request.json();
        const { url, type } = body as { url: string; type: 'game' | 'api' };

        if (!url || !type) {
            return new Response(
                JSON.stringify({ error: 'Missing url or type' }),
                { status: 400, headers: { 'Content-Type': 'application/json' } },
            );
        }

        const __dirname = path.dirname(fileURLToPath(import.meta.url));
        const isWindows = process.platform === 'win32';
        const binName = isWindows ? 'ping.exe' : 'ping';

        const possiblePaths = [
            path.resolve(__dirname, '..', '..', '..', 'bin', binName),
            path.resolve(__dirname, '..', 'bin', binName),
            path.resolve(process.cwd(), 'bin', binName),
        ];

        let binPath = possiblePaths[0];
        for (const p of possiblePaths) {
            try {
                const fs = await import('node:fs');
                if (fs.existsSync(p)) {
                    binPath = p;
                    break;
                }
            } catch {
                // continue
            }
        }

        // Build args: url, type, [apiKey]
        const args = [url, type];
        if (type === 'api') {
            const apiKey = process.env.GEMINI_API_KEY;
            if (apiKey) {
                args.push(apiKey);
            }
        }

        const { stdout } = await execFileAsync(binPath, args, {
            timeout: 120_000,
        });

        const result = JSON.parse(stdout.trim());

        return new Response(
            JSON.stringify(result),
            { status: 200, headers: { 'Content-Type': 'application/json' } },
        );
    } catch (err: any) {
        return new Response(
            JSON.stringify({
                latency: 9999,
                min: 9999,
                max: 9999,
                pings: [],
                status: 'Error',
                error: err?.message || 'Unknown error',
            }),
            { status: 500, headers: { 'Content-Type': 'application/json' } },
        );
    }
};
