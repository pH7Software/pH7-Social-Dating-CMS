#!/usr/bin/env bash

set -euo pipefail

readonly PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly AUDIT_HOST="127.0.0.1"
readonly AUDIT_PORT="${PH7_BROWSER_AUDIT_PORT:-8642}"
readonly AUDIT_ORIGIN="http://${AUDIT_HOST}:${AUDIT_PORT}"
readonly AUDIT_DIRECTORY="$(mktemp -d "${TMPDIR:-/tmp}/ph7-browser-audit.XXXXXX")"

server_pid=''

cleanup() {
    if [[ -n "$server_pid" ]]; then
        kill "$server_pid" 2>/dev/null || true
        wait "$server_pid" 2>/dev/null || true
    fi
    rm -rf "$AUDIT_DIRECTORY"
}
trap cleanup EXIT

find_chrome() {
    if [[ -n "${CHROME_BIN:-}" && -x "$CHROME_BIN" ]]; then
        printf '%s\n' "$CHROME_BIN"
        return
    fi

    local candidate
    for candidate in google-chrome google-chrome-stable chromium chromium-browser; do
        if command -v "$candidate" >/dev/null 2>&1; then
            command -v "$candidate"
            return
        fi
    done

    candidate='/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
    if [[ -x "$candidate" ]]; then
        printf '%s\n' "$candidate"
        return
    fi

    printf 'Chrome is required for the browser audit. Set CHROME_BIN to its executable.\n' >&2
    return 1
}

audit_page() {
    local path="$1"
    local viewport="$2"
    local name="$3"
    local output_file="$AUDIT_DIRECTORY/${name//[^a-zA-Z0-9]/_}.html"

    "$CHROME" \
        --headless=new \
        --no-sandbox \
        --disable-gpu \
        --disable-background-networking \
        --disable-extensions \
        --no-first-run \
        --user-data-dir="$AUDIT_DIRECTORY/chrome-profile" \
        --window-size="$viewport" \
        --virtual-time-budget=12000 \
        --dump-dom \
        "$AUDIT_ORIGIN/$path" > "$output_file" 2> "$AUDIT_DIRECTORY/chrome-errors.log"

    if ! grep -q 'data-audit-status="passed"' "$output_file"; then
        printf 'FAIL: %s\n' "$name" >&2
        grep -o 'data-audit-failures="[^"]*"' "$output_file" >&2 || true
        grep -o 'data-audit-status="[^"]*"' "$output_file" >&2 || true
        grep -o '<title>[^<]*</title>' "$output_file" >&2 || true
        grep -o 'Fatal error[^<]*' "$output_file" >&2 || true
        sed -n '1,40p' "$AUDIT_DIRECTORY/chrome-errors.log" >&2
        tail -n 20 "$AUDIT_DIRECTORY/php-server.log" >&2
        return 1
    fi

    printf 'PASS: %s\n' "$name"
}

readonly CHROME="$(find_chrome)"

php -S "$AUDIT_HOST:$AUDIT_PORT" -t "$PROJECT_ROOT" > "$AUDIT_DIRECTORY/php-server.log" 2>&1 &
server_pid=$!

for _ in {1..40}; do
    if curl --fail --silent "$AUDIT_ORIGIN/_tools/theme-preview.html" >/dev/null; then
        break
    fi
    sleep 0.25
done

if ! curl --fail --silent "$AUDIT_ORIGIN/_tools/theme-preview.html" >/dev/null; then
    printf 'The local browser-audit server did not start.\n' >&2
    sed -n '1,80p' "$AUDIT_DIRECTORY/php-server.log" >&2
    exit 1
fi

audit_page '_tools/theme-preview.html?theme=base&audit=1' '1280,900' 'base theme desktop'
audit_page '_tools/theme-preview.html?theme=base&audit=1' '390,844' 'base theme mobile'
audit_page '_tools/theme-preview.html?theme=premium&audit=1' '390,844' 'premium theme mobile'
audit_page '_tools/theme-preview.html?theme=cartoon&audit=1' '390,844' 'cartoon theme mobile'
audit_page '_tools/theme-preview.html?theme=datelove&audit=1' '390,844' 'datelove theme mobile'
audit_page '_tools/theme-preview.html?theme=zendate&audit=1' '390,844' 'zendate theme mobile'

audit_page '_tools/pfbc-preview.php?theme=base&scheme=light&audit=1' '390,844' 'base forms mobile'
audit_page '_tools/pfbc-preview.php?theme=base&scheme=dark&audit=1' '1280,900' 'base forms dark desktop'
audit_page '_tools/pfbc-preview.php?theme=premium&scheme=light&audit=1' '390,844' 'premium forms mobile'
audit_page '_tools/pfbc-preview.php?theme=premium&scheme=dark&audit=1' '1280,900' 'premium forms dark desktop'

audit_page '_tools/autocomplete-preview.html?audit=1' '390,844' 'autocomplete mobile'
audit_page '_tools/autocomplete-preview.html?audit=1' '1280,900' 'autocomplete desktop'
