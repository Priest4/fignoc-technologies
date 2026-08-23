/**
 * Lint the PHP inside Blade templates.
 *
 *   node tools/lint-blade.mjs [file ...]
 *
 * `php artisan view:cache` compiles Blade to PHP without executing it, so a
 * parse error in the generated PHP survives a green deploy and only surfaces as
 * a 500 when the view renders. This catches that class of error before it ships:
 * it pulls every @php block, every {{ }} / {!! !!} expression and every
 * directive argument out of the template and runs php -l over each.
 *
 * Blade comments are stripped first — {{-- ... --}} is prose, not PHP, and
 * matching it as an expression produces nothing but false alarms.
 */
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { execFileSync } from 'node:child_process';

const targets = process.argv.slice(2).length
  ? process.argv.slice(2)
  : [
      'resources/views/pages/landing/website.blade.php',
      'resources/views/components/lp-quote.blade.php',
      'resources/views/components/lp-img.blade.php',
      'resources/views/components/lp-script.blade.php',
      'resources/views/components/landing-header.blade.php',
      'resources/views/components/landing-footer.blade.php',
      'resources/views/components/layout.blade.php',
    ];

const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'blade-lint-'));
let checked = 0;
const failures = [];

const lint = (code, label) => {
  const file = path.join(tmp, 'snippet.php');
  fs.writeFileSync(file, '<?php\n' + code);
  checked++;
  try {
    execFileSync('php', ['-l', file], { stdio: 'pipe' });
  } catch (err) {
    const out = (err.stdout?.toString() || '') + (err.stderr?.toString() || '');
    failures.push({ label, message: out.split('\n')[0], code: code.slice(0, 160) });
  }
};

for (const f of targets) {
  if (!fs.existsSync(f)) { console.log('  skipped (missing): ' + f); continue; }
  // Comments out first: their contents are prose.
  const src = fs.readFileSync(f, 'utf8').replace(/\{\{--[\s\S]*?--\}\}/g, '');

  // @php ... @endphp
  for (const m of src.matchAll(/@php\b([\s\S]*?)@endphp/g)) lint(m[1], f + ' @php');

  // {{ expr }} and {!! expr !!}
  for (const m of src.matchAll(/\{\{([\s\S]*?)\}\}|\{!!([\s\S]*?)!!\}/g)) {
    const expr = (m[1] ?? m[2] ?? '').trim();
    if (expr) lint('$__ = ' + expr + ';', f + ' echo');
  }

  // Component attribute expressions: <x-thing :alt="$a . 'b'" />. Blade turns
  // these into PHP, so a bad expression here fails exactly like a bad echo.
  for (const tag of src.matchAll(/<x-[\w.-]+((?:\s+[^>]*?)?)\/?>/g)) {
    for (const attr of tag[1].matchAll(/\s:([\w.-]+)="([^"]*)"/g)) {
      const expr = attr[2].trim();
      if (expr) lint('$__ = ' + expr + ';', f + ' <x-… :' + attr[1] + '>');
    }
  }

  // Directives that take a PHP expression.
  const dirs = 'if|elseif|unless|foreach|forelse|for|while|switch|case|isset|empty|push|prepend|include|includeIf|json|class|checked|selected|disabled|props';
  for (const m of src.matchAll(new RegExp('@(' + dirs + ')\\s*\\(', 'g'))) {
    // Walk from the opening paren to its match, so nested parens survive.
    let i = m.index + m[0].length - 1, depth = 0, end = -1;
    for (let j = i; j < src.length; j++) {
      if (src[j] === '(') depth++;
      else if (src[j] === ')') { depth--; if (depth === 0) { end = j; break; } }
    }
    if (end < 0) continue;
    const arg = src.slice(i + 1, end).trim();
    if (!arg) continue;
    const name = m[1];
    if (name === 'foreach' || name === 'forelse') lint('foreach (' + arg + ') {}', f + ' @' + name);
    else if (name === 'for') lint('for (' + arg + ') {}', f + ' @for');
    else if (name === 'case') lint('switch (1) { case ' + arg + ': }', f + ' @case');
    else lint('$__ = (' + arg + ') ?? null;', f + ' @' + name);
  }
}

fs.rmSync(tmp, { recursive: true, force: true });

console.log(`\nlinted ${checked} PHP snippets across ${targets.length} templates`);
if (!failures.length) {
  console.log('no parse errors');
} else {
  console.log(failures.length + ' PARSE ERROR(S):\n');
  for (const f of failures) {
    console.log('  ' + f.label);
    console.log('    ' + f.message);
    console.log('    ' + f.code.replace(/\n/g, ' ⏎ ') + '\n');
  }
  process.exitCode = 1;
}
