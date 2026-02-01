import fs from 'node:fs/promises';
import path from 'node:path';

const repoRoot = process.cwd();
const src = path.join(repoRoot, 'frontend', 'admin-theme');
const dest = path.join(repoRoot, 'laravel', 'public', 'admin-theme');

async function exists(p) {
	try {
		await fs.access(p);
		return true;
	} catch {
		return false;
	}
}

if (!(await exists(src))) {
	console.error(`Source folder not found: ${src}`);
	process.exit(1);
}

await fs.rm(dest, { recursive: true, force: true });
await fs.mkdir(path.dirname(dest), { recursive: true });
await fs.cp(src, dest, { recursive: true, force: true });

console.log(`Synced admin theme: ${src} -> ${dest}`);
