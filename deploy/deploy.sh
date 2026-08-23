#!/usr/bin/env bash
#
# Fignoc Technologies -- cPanel deploy.
#
# Runs on the server after every "Deploy HEAD Commit" (see ../.cpanel.yml), and
# is safe to run by hand over SSH:  bash ~/fignoc/deploy/deploy.sh
#
# Idempotent: composer install, migrate, cache warm, web-root placement.
# It never touches .env or the contents of storage/.
#
# Overrides live in deploy/local.env (gitignored, written by server-setup.sh):
#   PHP_BIN=/opt/cpanel/ea-php83/root/usr/bin/php
#   COMPOSER_BIN=/usr/local/bin/composer
#   PUBLIC_HTML=/home/USER/public_html
#   SKIP_MIGRATIONS=0
#   PUBLIC_HTML_PRUNE=0     # 1 = rsync --delete when running in copy layout
#
set -Eeuo pipefail

APP_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

log()  { printf '\n[deploy %s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"; }
warn() { printf '[deploy]  ! %s\n' "$*" >&2; }
die()  { printf '[deploy] FATAL: %s\n' "$*" >&2; exit 1; }

if [ -f "$APP_DIR/deploy/local.env" ]; then
    # shellcheck disable=SC1091
    . "$APP_DIR/deploy/local.env"
fi

PUBLIC_HTML="${PUBLIC_HTML:-$HOME/public_html}"
SKIP_MIGRATIONS="${SKIP_MIGRATIONS:-0}"
PUBLIC_HTML_PRUNE="${PUBLIC_HTML_PRUNE:-0}"

log "Deploying $(git rev-parse --short HEAD 2>/dev/null || echo unknown) (branch $(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo '?')) in $APP_DIR"

# ---------------------------------------------------------------- toolchain ---
# The `php` on cPanel's PATH is often an old system build while the site itself
# runs a newer MultiPHP version, so probe for a >= 8.3 binary rather than
# trusting PATH.
find_php() {
    local candidate
    for candidate in \
        "${PHP_BIN:-}" \
        /opt/cpanel/ea-php85/root/usr/bin/php \
        /opt/cpanel/ea-php84/root/usr/bin/php \
        /opt/cpanel/ea-php83/root/usr/bin/php \
        /usr/local/bin/php \
        "$(command -v php 2>/dev/null || true)"
    do
        [ -n "$candidate" ] && [ -x "$candidate" ] || continue
        if "$candidate" -r 'exit(PHP_VERSION_ID >= 80300 ? 0 : 1);' >/dev/null 2>&1; then
            printf '%s' "$candidate"
            return 0
        fi
    done
    return 1
}

PHP="$(find_php)" || die "no PHP >= 8.3 found. Set PHP_BIN in deploy/local.env (candidates: ls -d /opt/cpanel/ea-php*/root/usr/bin/php)."
log "PHP: $PHP ($("$PHP" -r 'echo PHP_VERSION;'))"

find_composer() {
    local candidate
    for candidate in \
        "${COMPOSER_BIN:-}" \
        /usr/local/bin/composer \
        "$HOME/bin/composer" \
        "$HOME/composer.phar" \
        "$(command -v composer 2>/dev/null || true)"
    do
        [ -n "$candidate" ] && [ -r "$candidate" ] || continue
        printf '%s' "$candidate"
        return 0
    done
    return 1
}

if ! COMPOSER="$(find_composer)"; then
    warn "composer not found -- installing a private copy at $HOME/bin/composer"
    mkdir -p "$HOME/bin"
    if command -v curl >/dev/null 2>&1; then
        curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
    else
        "$PHP" -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');"
    fi
    "$PHP" /tmp/composer-setup.php --install-dir="$HOME/bin" --filename=composer --quiet
    rm -f /tmp/composer-setup.php
    COMPOSER="$HOME/bin/composer"
fi
log "Composer: $COMPOSER"

artisan() { "$PHP" "$APP_DIR/artisan" "$@"; }

# ------------------------------------------------------------------- guards ---
[ -f "$APP_DIR/.env" ] || die ".env is missing. Run deploy/server-setup.sh --apply, then fill in the DB and mail credentials."
grep -q '^APP_KEY=base64:' "$APP_DIR/.env" || die "APP_KEY is empty in .env. Run: $PHP artisan key:generate --force"

# ------------------------------------------------------------- dependencies ---
log "Installing PHP dependencies (production)"
COMPOSER_HOME="${COMPOSER_HOME:-$HOME/.composer}" \
    "$PHP" -d memory_limit=-1 "$COMPOSER" install \
    --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

if [ ! -f "$APP_DIR/public/build/manifest.json" ]; then
    warn "public/build/manifest.json is absent -- @vite() will fail."
    warn "Deploy the 'production' branch (GitHub Actions builds assets onto it), or run 'npm ci && npm run build' locally and commit public/build."
fi

# -------------------------------------------------------------- app changes ---
# From here the site is in maintenance mode; the trap guarantees it comes back
# up even if a migration explodes.
restore_up() { artisan up >/dev/null 2>&1 || true; }
trap restore_up EXIT

log "Enabling maintenance mode"
artisan down --retry=15 >/dev/null 2>&1 || warn "could not enter maintenance mode (continuing)"

if [ "$SKIP_MIGRATIONS" != "1" ]; then
    log "Running migrations"
    artisan migrate --force --no-interaction
else
    log "Skipping migrations (SKIP_MIGRATIONS=1)"
fi

if [ ! -e "$APP_DIR/public/storage" ]; then
    log "Creating public/storage symlink"
    artisan storage:link || warn "storage:link failed"
fi

log "Rebuilding caches"
artisan optimize:clear >/dev/null
artisan optimize
artisan filament:optimize >/dev/null 2>&1 || true

# ------------------------------------------------------------- web root sync ---
# Preferred layout: public_html is a symlink to $APP_DIR/public, or the domain's
# document root points straight at it -- nothing to copy, and .git / .env / app
# code sit outside the web root. Only the fallback copy layout needs a sync.
app_public_real="$(readlink -f "$APP_DIR/public")"
pub_real="$(readlink -f "$PUBLIC_HTML" 2>/dev/null || true)"

if [ "$pub_real" = "$app_public_real" ]; then
    log "Web root already resolves to $APP_DIR/public -- no asset copy needed"
elif [ -d "$PUBLIC_HTML" ]; then
    log "Copy layout detected -- syncing public/ into $PUBLIC_HTML"
    rsync_flags=(-a --no-perms --no-owner --no-group
        --exclude ".well-known/" --exclude "cgi-bin/" --exclude ".ftpquota"
        --exclude "index.php")
    if [ "$PUBLIC_HTML_PRUNE" = "1" ]; then
        rsync_flags+=(--delete)
    fi
    if command -v rsync >/dev/null 2>&1; then
        rsync "${rsync_flags[@]}" "$APP_DIR/public/" "$PUBLIC_HTML/"
    else
        warn "rsync missing -- falling back to cp -R (stale files are not pruned)"
        cp -R "$APP_DIR/public/." "$PUBLIC_HTML/"
    fi

    # The front controller has to reach one level further out than stock Laravel.
    {
        echo "<?php"
        echo ""
        echo "// GENERATED by deploy/deploy.sh -- do not edit. The application lives"
        echo "// outside the web root; this shim points the front controller at it."
        echo "use Illuminate\\Foundation\\Application;"
        echo "use Illuminate\\Http\\Request;"
        echo ""
        echo "define('LARAVEL_START', microtime(true));"
        echo ""
        echo "\$base = '$APP_DIR';"
        echo ""
        echo "if (file_exists(\$maintenance = \$base.'/storage/framework/maintenance.php')) {"
        echo "    require \$maintenance;"
        echo "}"
        echo ""
        echo "require \$base.'/vendor/autoload.php';"
        echo ""
        echo "/** @var Application \$app */"
        echo "\$app = require_once \$base.'/bootstrap/app.php';"
        echo ""
        echo "\$app->handleRequest(Request::capture());"
    } > "$PUBLIC_HTML/index.php"
    log "Wrote front-controller shim to $PUBLIC_HTML/index.php"
else
    warn "$PUBLIC_HTML does not exist -- assuming the document root points at $APP_DIR/public"
fi

# -------------------------------------------------------------- permissions ---
log "Fixing writable paths"
chmod -R ug+rwX "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || warn "chmod on storage/bootstrap-cache partially failed"

# Best-effort opcode-cache reset. Harmless where the tooling is absent; PHP's
# default validate_timestamps=On means fresh mtimes are normally enough.
if command -v cloudlinux-selector >/dev/null 2>&1; then
    cloudlinux-selector restart --json --interpreter php >/dev/null 2>&1 || true
fi

log "Leaving maintenance mode"
trap - EXIT
artisan up

log "Deploy complete: $(git log -1 --pretty='%h %s' 2>/dev/null || echo 'unknown commit')"
