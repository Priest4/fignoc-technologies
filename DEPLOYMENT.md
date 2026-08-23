# Deploying to cPanel with Git

Laravel 13 + Filament 4 on shared cPanel hosting, deployed by cPanel's **Git
Version Control** pulling from GitHub.

This account is shared by several sites, so the Laravel site is deployed as an
**addon domain with its own document root** and never touches the other ones.

| | |
|---|---|
| cPanel user | `daxutaxh` |
| Home | `/home/daxutaxh` |
| Domain | `fignoc.co.zw` (addon domain) |
| App directory | `/home/daxutaxh/fignoc-technologies` |
| Document root | `/home/daxutaxh/fignoc-technologies/public` (set on the addon domain — no symlink, no copying) |
| Database | `daxutaxh_fgnc` |
| GitHub repo | `github.com/Priest4/fignoc-technologies` |
| Deployed branch | `production` (built by GitHub Actions from `main`) |

**Occupied paths on this account — do not reuse:** `~/fignoc` is the live Django
site for `fignoconline.co.zw`, `~/public_html` is that site's document root, and
`~/nestzim.co.zw`, `~/fignoc-old`, `~/nestzim-backend-BEFORE-GIT` are other
apps. Confirm any target directory is free before pointing cPanel at it:
`ls -d ~/fignoc-technologies 2>/dev/null || echo free`

## How it fits together

```
you push -> main ──► GitHub Actions (npm ci && npm run build)
                        │
                        └─► production branch = main's tree + public/build
                                     │
                    cPanel "Update from Remote" (fast-forward pull)
                                     │
                    cPanel "Deploy HEAD Commit" -> .cpanel.yml
                                     │
                             deploy/deploy.sh on the server:
                             composer install --no-dev, migrate,
                             optimize, permissions, maintenance on/off
```

Two things drive that split:

- **`main` never carries build artifacts.** cPanel has no Node toolchain you
  want in a deploy path, but `@vite(...)` in
  [layout.blade.php](resources/views/components/layout.blade.php#L135) needs
  `public/build/manifest.json` at runtime. Actions builds it and commits it onto
  `production` only.
- **`production` is only ever fast-forwarded.** Each Actions commit is parented
  on the previous `production` tip (and on `main`, for lineage), so cPanel's pull
  never hits a non-fast-forward. Nothing is force-pushed.

The addon domain's document root points at `<app>/public`, so `.env`, `.git`,
`vendor/`, `storage/`, and your source all sit one level *above* the web root and
are not reachable over HTTP. Nothing is symlinked and nothing is copied — the
document root simply is the app's own `public/` directory.

## Files in this repo

| File | Role |
|---|---|
| [.cpanel.yml](.cpanel.yml) | What cPanel runs on "Deploy HEAD Commit" — just calls `deploy/deploy.sh` and tails the log |
| [deploy/deploy.sh](deploy/deploy.sh) | The actual deploy. Idempotent, safe to run by hand |
| [deploy/server-setup.sh](deploy/server-setup.sh) | One-time server prep. Reports by default, changes things with `--apply` |
| [deploy/env.production.example](deploy/env.production.example) | Template for the server's `.env` |
| [.github/workflows/build-production-branch.yml](.github/workflows/build-production-branch.yml) | Builds assets, publishes `production` |
| `deploy/local.env` | Server-only overrides (PHP binary, paths). Gitignored, written by `server-setup.sh` |

---

## One-time setup

### 1. Push this kit to GitHub

```bash
git add .cpanel.yml deploy .github/workflows/build-production-branch.yml .gitignore DEPLOYMENT.md
git commit -m "Add cPanel git deployment pipeline."
git push origin main
```

Then check **Actions** on GitHub. If the run fails on the push step with a 403,
go to *Settings → Actions → General → Workflow permissions* and select
**Read and write permissions**, then re-run it. When it goes green you have a
`production` branch.

### 2. Create the MySQL database

cPanel → **MySQL Databases**: create database `fgnc` and user `fgnc` (cPanel
prefixes both with `daxutaxh_`), then **add the user to the database with ALL
PRIVILEGES**. Keep the password — it goes into `.env` in step 6.

The short name is deliberate: this account's existing databases (`daxutaxh_figno`,
`daxutaxh_figones`) show cPanel truncating at 16 characters including the prefix,
so anything longer comes back mangled. Give this site its **own** database — do
not point it at `daxutaxh_figno`, which belongs to the Django site; Laravel's
`migrate` would build its whole schema inside it.

Over SSH instead, if you prefer:

```bash
uapi Mysql create_database name=daxutaxh_fgnc
uapi Mysql create_user name=daxutaxh_fgnc password='<strong-password>'
uapi Mysql set_privileges_on_database user=daxutaxh_fgnc \
    database=daxutaxh_fgnc privileges=ALL
```

### 3. Clone the repo through cPanel

Check the path is free first — `~/fignoc` is **not**, it is the live Django site:

```bash
ls -d ~/fignoc-technologies 2>/dev/null || echo free
```

cPanel → **Git Version Control** → *Create*:

| Field | Value |
|---|---|
| Clone URL | `https://github.com/Priest4/fignoc-technologies.git` |
| Repository Path | `fignoc-technologies` |
| Repository Name | `fignoc-technologies` |

Leave *Clone a Repository* toggled on. cPanel refuses to clone into a non-empty
directory, which is the safety net that stops this from landing on another site.
If the repo is **private**, use `git@github.com:Priest4/fignoc-technologies.git`
and first copy the key from cPanel → *SSH Access → Manage SSH Keys* into GitHub →
*repo Settings → Deploy keys*.

After it clones, open the repo's **Pull or Deploy** tab and switch the checked-out
branch to `production`. If the UI will not offer it, do it over SSH:

```bash
cd ~/fignoc-technologies && git fetch origin production && git checkout production
```

### 4. Add the domain with its own document root

cPanel → **Domains** → *Create A Domain*:

| Field | Value |
|---|---|
| Domain | `fignoc.co.zw` |
| Share document root | **unticked** |
| Document Root | `fignoc-technologies/public` |

This is the step that keeps the other sites safe: `fignoc.co.zw` gets its own
document root and `~/public_html` — which serves `fignoconline.co.zw` — is never
involved. Do it *after* the clone, so `public/` already exists.

`fignoc.co.zw`'s DNS has to point at this server, or the domain will resolve
elsewhere no matter what cPanel says. Check with `dig +short fignoc.co.zw` and
compare against this account's IP.

### 5. Set PHP 8.3+ for the domain

cPanel → **MultiPHP Manager** → tick `fignoc.co.zw` → set **8.3** (or newer) →
Apply. Set it per-domain, not account-wide — the Django sites here have their own
requirements. Then cPanel → **Select PHP Version → Extensions**: `pdo_mysql`,
`mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `curl`, `dom`,
`bcmath`, `intl`, `zip`, `gd`. `server-setup.sh` verifies all of these and names
any that are missing.

### 6. Prepare the app

```bash
cd ~/fignoc-technologies
bash deploy/server-setup.sh              # report: what it would change
bash deploy/server-setup.sh --apply      # do it
nano .env                                # DB_PASSWORD, MAIL_PASSWORD, APP_URL
```

`--apply` writes `deploy/local.env`, creates `.env` from the template, generates
`APP_KEY`, and creates the writable storage directories. In the default
`docroot` layout it touches **nothing** outside the app directory — `public_html`
is left exactly as it is.

Quote any password containing `@` or `#` in `.env`: `DB_PASSWORD="pa@ss#word"`.
An unquoted `#` silently truncates the value at that character.

### 7. First deploy

```bash
bash ~/fignoc-technologies/deploy/deploy.sh
```

Expect: composer install, migrations, `optimize`, permissions fixed, and
`Deploy complete`. Then load <https://www.fignoc.co.zw>.

On a fresh database, seed the catalogue content and create the Filament admin
(the panel lives at `/admin`):

```bash
cd ~/fignoc-technologies
PHP=$(grep '^PHP_BIN=' deploy/local.env | cut -d= -f2)
$PHP artisan db:seed --force
$PHP artisan make:filament-user
```

### 8. Cron (optional but recommended)

cPanel → **Cron Jobs**, once per minute — the scheduler, which also drives log
rotation and any future queued work:

```
* * * * * /opt/cpanel/ea-php83/root/usr/bin/php /home/daxutaxh/fignoc-technologies/artisan schedule:run >> /dev/null 2>&1
```

The contact form sends mail synchronously via `Mail::raw`, so no queue worker is
required today. If mail moves to a queue, add:

```
* * * * * /opt/cpanel/ea-php83/root/usr/bin/php /home/daxutaxh/fignoc-technologies/artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
```

---

## Every deploy after that

1. `git push origin main`
2. Wait for the Actions run to go green (~1 min).
3. cPanel → Git Version Control → **Update from Remote**, then **Deploy HEAD Commit**.

Or skip the UI entirely:

```bash
ssh daxutaxh@host 'cd ~/fignoc-technologies && git pull --ff-only origin production && bash deploy/deploy.sh'
```

Deploy output is appended to `~/fignoc-technologies/storage/logs/deploy.log`; cPanel also
keeps its own copy under `~/.cpanel/logs/`.

## What survives a deploy

Untouched: `.env`, `deploy/local.env`, everything under `storage/`, the database,
and uploaded files in `storage/app/public`. Rebuilt every time: `vendor/`, the
config/route/view caches, `public_html` contents (copy layout only).

## Troubleshooting

**500 on every page.** `tail -50 ~/fignoc-technologies/storage/logs/laravel-*.log`. Usually a
`.env` credential or a missing PHP extension. Never flip `APP_DEBUG=true` on the
live domain to find out — read the log.

**Unstyled pages / "Vite manifest not found".** The deployed branch is `main`
instead of `production`, or the Actions run failed. Confirm on the server:
`ls ~/fignoc-technologies/public/build/manifest.json`.

**403 Forbidden, or the wrong site loads.** The domain's document root is not
where you think. Check what cPanel actually recorded:

```bash
uapi DomainInfo single_domain_data domain=fignoc.co.zw | grep -i documentroot
```

It must read `/home/daxutaxh/fignoc-technologies/public`. Fix it in cPanel →
Domains → `fignoc.co.zw` → *Document Root*. A 403 with the right document root
usually means `public/` lost its permissions: `chmod 755 ~/fignoc-technologies/public`.

**Never** point this site at `~/public_html`, and never delete or move that
directory — it is `fignoconline.co.zw`'s live document root. The `symlink` and
`copy` layouts in `server-setup.sh` exist for single-site accounts; on this
account, stay on the default `docroot` layout.

**Deploy failed and the site is stuck on the maintenance page.**

```bash
cd ~/fignoc-technologies && $(grep '^PHP_BIN=' deploy/local.env | cut -d= -f2) artisan up
```

(`deploy.sh` has an EXIT trap that does this automatically; this is for the case
where the script itself was killed.)

**"Wrong PHP version" / composer aborts on platform requirements.** cPanel's
`php` on `$PATH` is an old system build. `deploy.sh` probes for 8.3+ and pins the
result; override it in `deploy/local.env`:

```bash
ls -d /opt/cpanel/ea-php*/root/usr/bin/php
```

**cPanel pull fails: "Your local changes would be overwritten".** Something on
the server modified a tracked file.

```bash
cd ~/fignoc-technologies && git status --short      # look first
git checkout -- <the file>             # then discard
```

**Migration went wrong.** There is no automatic rollback. Take a database
snapshot before schema-heavy deploys: cPanel → *phpMyAdmin → Export*, or
`mysqldump -u daxutaxh_fgnc -p daxutaxh_fgnc > ~/backup-fignoc.sql`.

## Rolling back code

```bash
cd ~/fignoc-technologies
git log --oneline -10          # find the previous good production commit
git checkout <sha>
bash deploy/deploy.sh
```

Then `git checkout production` once the real fix has been pushed and built.
