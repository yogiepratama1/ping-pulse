import { exec } from 'node:child_process';
import os from 'node:os';
import path from 'node:path';
import fs from 'node:fs';

const isWindows = os.platform() === 'win32';
const rootDir = path.resolve(process.cwd());
const goDir = path.join(rootDir, 'go');
const binDir = path.join(rootDir, 'bin');
const binName = isWindows ? 'ping.exe' : 'ping';
const binPath = path.join(binDir, binName);

// Ensure bin directory exists
if (!fs.existsSync(binDir)) {
    fs.mkdirSync(binDir, { recursive: true });
}

console.log('🔨 Building Go pinger binary...');

// Exec go build
const buildCmd = `go build -o "${binPath}" ping.go`;

const proc = exec(buildCmd, { cwd: goDir }, (error, stdout, stderr) => {
    if (error) {
        console.error(`❌ Go build failed:\n${error.message}`);
        process.exit(1);
    }
    if (stderr && stderr.trim()) {
        console.error(`⚠️ Go build stderr:\n${stderr}`);
    }
    console.log(`✅ Go binary successfully compiled to: ${binPath}`);
});

proc.stdout?.pipe(process.stdout);
proc.stderr?.pipe(process.stderr);
