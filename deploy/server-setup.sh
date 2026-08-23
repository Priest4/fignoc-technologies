#!/usr/bin/env bash
#
# Fignoc Technologies -- one-time cPanel server preparation.
#
# Run it over SSH from inside the cloned repo:
#
#   bash ~/fignoc/deploy/server-setup.sh            # report only, changes nothing
#   bash ~/fignoc/deploy/server-setup.sh --apply    # actually make the changes
#
# What --apply does:
#   1. writes deploy/local.env pinning the PHP binary it found
#   2. creates .env from deploy/env.production.example and generates APP_KEY
#   3. points public_html at this app's public/ directory (symlink), backing up
#      whatever was there first -- or falls back to the copy layout
#   4. creates storage/ subdirectories and the public/storage symlink
#
# It never drops a database and never deletes public_html without backing it up.
#
set -Eeuo pipefail

APP_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
APPLY=0
LAYOUT="symlink"

for arg in "$@"; do
    case "$arg" in
        --apply) APPLY=1 ;;
        --layout=symlink) LAYOUT="symlink" ;;
        --layout=copy)    LAYOUT="copy" ;;
        -h|--help)
            sed -n '2,20p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'
            exit 0
            ;;
        *) printf 'unknown argument: %s\n' "$arg" >&2; exit 2 ;;
    esac
done

PUBLIC_HTML="${PUBLIC_HTML:-$HOME/public_html}"

say()  { printf '  %s\n' "$*"; }
head2() { printf '\n== %s\n' "$*"; }
ok()   { printf '  [ok]   %s\n' "$*"; }
bad()  { printf '  [MISS] %s\n' "$*"; }
act()  { printf '  [do]   %s\n' "$*"; }

printf '\nFignoc cPanel server setup -- %s\n' "$( [ "$APPLY" = 1 ] && echo 'APPLY mode' || echo 'report only (pass --apply to execute)')"
say "app dir     : $APP_DIR"
say "public_html : $PUBLIC_HTML"
say "layout      : $LAYOUT"

# ------------------------------------------------------------------ toolchain --
head2 "Toolchain"

PHP=""
for candidate in \
    /opt/cpanel/ea-php85/root/usr/bin/php \
    /opt/cpanel/ea-php84/root/usr/bin/php \
    /opt/cpanel/ea-php83/root/usr/bin/php \
    /usr/local/bin/php \
    "$(command -v php 2>/dev/null || true)"
do
    [ -n "$candidate" ] && [ -x "$candidate" ] || continue
    if "$candidate" -r 'exit(PHP_VERSION_ID >= 80300 ? 0 : 1);' >/dev/null 2>&1; then
        PHP="$candidate"
        break
    fi
done

if [ -n "$PHP" ]; then
    ok "PHP $("$PHP" -r 'echo PHP_VERSION;') at $PHP"
else
    bad "no PHP >= 8.3 found. In cPanel: MultiPHP Manager -> set the domain to 8.3+."
    say "     available: $(ls -d /opt/cpanel/ea-php*/root/usr/bin/php 2>/dev/null | tr '\n' ' ')"
fi

# Laravel 13 + Filament 4 need these.
if [ -n "$PHP" ]; then
    missing=""
    for ext in openssl pdo pdo_mysql mbstring tokenizer xml ctype json fileinfo curl dom filter hash session bcmath intl zip gd; do
        "$PHP" -m 2>/dev/null | grep -qix "$ext" || missing="$missing $ext"
    done
    if [ -n "$missing" ]; then
        bad "missing PHP extensions:$missing  (cPanel -> Select PHP Version -> Extensions)"
    else
        ok "all required PHP extensions present"
    fi
fi

for tool in git rsync; do
    if command -v "$tool" >/dev/null 2>&1; then ok "$tool: $(command -v "$tool")"; else bad "$tool not on PATH"; fi
done

if command -v composer >/dev/null 2>&1 || [ -r /usr/local/bin/composer ] || [ -r "$HOME/bin/composer" ]; then
    ok "composer available"
else
    say "  composer absent -- deploy.sh will install a private copy into ~/bin on first run"
fi

# ------------------------------------------------------------------ local.env --
head2 "deploy/local.env"

if [ -f "$APP_DIR/deploy/local.env" ]; then
    ok "already exists (left untouched)"
else
    act "write deploy/local.env"
    if [ "$APPLY" = 1 ]; then
        {
            echo "# Written by deploy/server-setup.sh -- machine-local, never committed."
            echo "PHP_BIN=${PHP:-php}"
            echo "PUBLIC_HTML=$PUBLIC_HTML"
            echo "SKIP_MIGRATIONS=0"
            echo "# 1 = let the copy layout prune files that no longer exist in public/"
            echo "PUBLIC_HTML_PRUNE=0"
        } > "$APP_DIR/deploy/local.env"
        ok "written"
    fi
fi

# ------------------------------------------------------------------------ env --
head2 ".env"

if [ -f "$APP_DIR/.env" ]; then
    ok ".env exists (left untouched)"
else
    act "copy deploy/env.production.example -> .env and generate APP_KEY"
    if [ "$APPLY" = 1 ]; then
        cp "$APP_DIR/deploy/env.production.example" "$APP_DIR/.env"
        chmod 600 "$APP_DIR/.env"
        if [ -n "$PHP" ] && [ -f "$APP_DIR/vendor/autoload.php" ]; then
            "$PHP" "$APP_DIR/artisan" key:generate --force
            ok "APP_KEY generated"
        else
            bad "vendor/ not installed yet -- run deploy/deploy.sh, then: $PHP artisan key:generate --force"
        fi
        printf '\n  >>> EDIT %s NOW: DB_DATABASE / DB_USERNAME / DB_PASSWORD and the MAIL_* block.\n' "$APP_DIR/.env"
    fi
fi

# -------------------------------------------------------------------- storage --
head2 "Writable paths"

act "ensure storage subdirectories exist and are writable"
if [ "$APPLY" = 1 ]; then
    mkdir -p "$APP_DIR/storage/framework/"{cache/data,sessions,views} \
             "$APP_DIR/storage/logs" \
             "$APP_DIR/storage/app/public" \
             "$APP_DIR/bootstrap/cache"
    chmod -R ug+rwX "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
    ok "done"
fi

# ------------------------------------------------------------------ web root --
head2 "Web root"

app_public_real="$(readlink -f "$APP_DIR/public")"
pub_real="$(readlink -f "$PUBLIC_HTML" 2>/dev/null || true)"

if [ "$pub_real" = "$app_public_real" ]; then
    ok "$PUBLIC_HTML already resolves to $APP_DIR/public -- nothing to do"
elif [ "$LAYOUT" = "copy" ]; then
    say "copy layout selected -- deploy.sh will rsync public/ into $PUBLIC_HTML on every deploy"
    say "and write a front-controller shim there. Nothing to prepare now."
else
    if [ -e "$PUBLIC_HTML" ]; then
        backup="$PUBLIC_HTML.bak.$(date '+%Y%m%d%H%M%S')"
        act "move $PUBLIC_HTML -> $backup"
        act "symlink $PUBLIC_HTML -> $APP_DIR/public"
        if [ "$APPLY" = 1 ]; then
            mv "$PUBLIC_HTML" "$backup"
            ln -s "$APP_DIR/public" "$PUBLIC_HTML"
            ok "linked (old web root kept at $backup -- delete it once the site is verified)"
        fi
    else
        act "symlink $PUBLIC_HTML -> $APP_DIR/public"
        if [ "$APPLY" = 1 ]; then
            ln -s "$APP_DIR/public" "$PUBLIC_HTML"
            ok "linked"
        fi
    fi
    say "If the site 403s after this, the host forbids symlinked doc roots:"
    say "  re-run with --layout=copy, or set the domain document root to"
    say "  ${APP_DIR#"$HOME"/}/public in cPanel -> Domains."
fi

if [ "$APPLY" = 1 ] && [ ! -e "$APP_DIR/public/storage" ] && [ -n "$PHP" ] && [ -f "$APP_DIR/vendor/autoload.php" ]; then
    "$PHP" "$APP_DIR/artisan" storage:link >/dev/null 2>&1 && ok "public/storage symlink created" || true
fi

# --------------------------------------------------------------------- next ---
head2 "Next"
if [ "$APPLY" = 1 ]; then
    say "1. edit $APP_DIR/.env   (DB_*, MAIL_*, APP_URL)"
    say "2. create the MySQL database and user in cPanel if you have not yet"
    say "3. bash $APP_DIR/deploy/deploy.sh"
    say "4. back in cPanel -> Git Version Control, use 'Deploy HEAD Commit' from now on"
else
    say "Re-run with --apply to make the changes above."
fi
printf '\n'
