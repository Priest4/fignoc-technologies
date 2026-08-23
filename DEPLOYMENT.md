# Deploying to cPanel with Git

Laravel 13 + Filament 4 on shared cPanel hosting, deployed by cPanel's **Git
Version Control** pulling from GitHub.

Account facts assumed below (change them if wrong):

| | |
|---|---|
| cPanel user | `daxutaxh` |
| Home | `/home/daxutaxh` |
| App directory | `/home/daxutaxh/fignoc` |
| Web root | `/home/daxutaxh/public_html` → symlink to `/home/daxutaxh/fignoc/public` |
| GitHub repo | `github.com/Priest4/fignoc-technologies` |
| Deployed branch | `production` (built by GitHub Actions from `main`) |

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

The app directory sits **outside** the web root, so `.env`, `.git`, `vendor/`,
`storage/`, and your source are not reachable over HTTP. That is the whole reason
for the symlink layout.

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

### 2. Set PHP 8.3+ for the domain

cPanel → **MultiPHP Manager** → tick `fignoc.co.zw` → set **8.3** (or newer) →
Apply. Then cPanel → **Select PHP Version → Extensions** and make sure these are
on: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`,
`curl`, `dom`, `bcmath`, `intl`, `zip`, `gd`. `server-setup.sh` verifies all of
these and names any that are missing.

### 3. Create the MySQL database

cPanel → **MySQL Databases**: create database `fignoc` and user `fignoc` (cPanel
prefixes both with `daxutaxh_`), then **add the user to the database with ALL
PRIVILEGES**. Keep the password — it goes into `.env` in step 5.

Over SSH instead, if you prefer:

```bash
uapi Mysql create_database name=daxutaxh_fignoc
uapi Mysql create_user name=daxutaxh_fignoc password='<strong-password>'
uapi Mysql set_privileges_on_database user=daxutaxh_fignoc \
    database=daxutaxh_fignoc privileges=ALL
```

### 4. Clone the repo through cPanel

cPanel → **Git Version Control** → *Create*:

| Field | Value |
|---|---|
| Clone URL | `https://github.com/Priest4/fignoc-technologies.git` |
| Repository Path | `fignoc` |
| Repository Name | `fignoc` |

Leave *Clone a Repository* toggled on. If the repo is **private**, use
`git@github.com:Priest4/fignoc-technologies.git` and first copy the key from
cPanel → *SSH Access → Manage SSH Keys* into GitHub → *repo Settings → Deploy
keys*.

After it clones, open the repo's **Pull or Deploy** tab and switch the checked-out
branch to `production`. If the UI will not offer it, do it over SSH:

```bash
cd ~/fignoc && git fetch origin production && git checkout production
```

### 5. Prepare the server

```bash
cd ~/fignoc
bash deploy/server-setup.sh              # report: what it would change
bash deploy/server-setup.sh --apply      # do it
nano .env                                # DB_PASSWORD, MAIL_PASSWORD, APP_URL
```

`--apply` writes `deploy/local.env`, creates `.env` from the template, creates
the writable storage dirs, and replaces `public_html` with a symlink to
`~/fignoc/public` — moving the old `public_html` aside to
`public_html.bak.<timestamp>` first. Delete that backup once the site is
verified.

### 6. First deploy

```bash
bash ~/fignoc/deploy/deploy.sh
```

Expect: composer install, migrations, `optimize`, permissions fixed, and
`Deploy complete`. Then load <https://www.fignoc.co.zw>.

On a fresh database, seed the catalogue content and create the Filament admin
(the panel lives at `/admin`):

```bash
cd ~/fignoc
PHP=$(grep '^PHP_BIN=' deploy/local.env | cut -d= -f2)
$PHP artisan db:seed --force
$PHP artisan make:filament-user
```

### 7. Cron (optional but recommended)

cPanel → **Cron Jobs**, once per minute — the scheduler, which also drives log
rotation and any future queued work:

```
* * * * * /opt/cpanel/ea-php83/root/usr/bin/php /home/daxutaxh/fignoc/artisan schedule:run >> /dev/null 2>&1
```

The contact form sends mail synchronously via `Mail::raw`, so no queue worker is
required today. If mail moves to a queue, add:

```
* * * * * /opt/cpanel/ea-php83/root/usr/bin/php /home/daxutaxh/fignoc/artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1
```

---

## Every deploy after that

1. `git push origin main`
2. Wait for the Actions run to go green (~1 min).
3. cPanel → Git Version Control → **Update from Remote**, then **Deploy HEAD Commit**.

Or skip the UI entirely:

```bash
ssh daxutaxh@host 'cd ~/fignoc && git pull --ff-only origin production && bash deploy/deploy.sh'
```

Deploy output is appended to `~/fignoc/storage/logs/deploy.log`; cPanel also
keeps its own copy under `~/.cpanel/logs/`.

## What survives a deploy

Untouched: `.env`, `deploy/local.env`, everything under `storage/`, the database,
and uploaded files in `storage/app/public`. Rebuilt every time: `vendor/`, the
config/route/view caches, `public_html` contents (copy layout only).

## Troubleshooting

**500 on every page.** `tail -50 ~/fignoc/storage/logs/laravel-*.log`. Usually a
`.env` credential or a missing PHP extension. Never flip `APP_DEBUG=true` on the
live domain to find out — read the log.

**Unstyled pages / "Vite manifest not found".** The deployed branch is `main`
instead of `production`, or the Actions run failed. Confirm on the server:
`ls ~/fignoc/public/build/manifest.json`.

**403 Forbidden after setup.** The host refuses symlinked document roots. Either
point the domain's document root at `fignoc/public` (cPanel → Domains → the
domain → *Document Root*), or fall back to the copy layout:

```bash
rm ~/public_html && mkdir ~/public_html
sed -i 's/^PUBLIC_HTML_PRUNE=.*/PUBLIC_HTML_PRUNE=1/' ~/fignoc/deploy/local.env
bash ~/fignoc/deploy/deploy.sh
```

`deploy.sh` detects that layout on its own, rsyncs `public/` into `public_html`,
and writes a front-controller shim pointing back at `~/fignoc`.

**Deploy failed and the site is stuck on the maintenance page.**

```bash
cd ~/fignoc && $(grep '^PHP_BIN=' deploy/local.env | cut -d= -f2) artisan up
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
cd ~/fignoc && git status --short      # look first
git checkout -- <the file>             # then discard
```

**Migration went wrong.** There is no automatic rollback. Take a database
snapshot before schema-heavy deploys: cPanel → *phpMyAdmin → Export*, or
`mysqldump -u daxutaxh_fignoc -p daxutaxh_fignoc > ~/backup-fignoc.sql`.

## Rolling back code

```bash
cd ~/fignoc
git log --oneline -10          # find the previous good production commit
git checkout <sha>
bash deploy/deploy.sh
```

Then `git checkout production` once the real fix has been pushed and built.
