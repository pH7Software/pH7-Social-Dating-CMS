#!/usr/bin/env bash

##
# Title:           pH7Builder Release Gate
# Description:     Proves code quality, release packaging, installation, upgrade, and recovery before publication.
# Usage:           bash _tools/release-gate.sh
#
# Author:          Pierre-Henry Soria <hello@ph7builder.com>
# Copyright:       (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
# License:         MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
##

set -euo pipefail
umask 077

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly PROJECT_ROOT
readonly TEMP_DIRECTORY_ROOT="${TMPDIR:-/tmp}"
WORK_DIRECTORY="$(mktemp -d "${TEMP_DIRECTORY_ROOT%/}/ph7-release-gate.XXXXXX")"
readonly WORK_DIRECTORY
# The single quotes deliberately keep the embedded PHP variables away from Bash expansion.
# shellcheck disable=SC2016
RELEASE_VERSION="$(php -r '
    $sContents = file_get_contents($argv[1]);
    if (!is_string($sContents) || !preg_match("/KERNEL_VERSION\\s*=\\s*\x27([^\x27]+)\x27/", $sContents, $aMatches)) {
        exit(1);
    }
    echo $aMatches[1];
' "$PROJECT_ROOT/_protected/framework/Security/Version.class.php")"
readonly RELEASE_VERSION
readonly MYSQL_CONFIG="$WORK_DIRECTORY/mysql.cnf"
readonly INSTALL_TOKEN='release-gate-access-token-release-gate'
readonly DATABASE_HOST="${PH7_RELEASE_DB_HOST:-127.0.0.1}"
readonly DATABASE_PORT="${PH7_RELEASE_DB_PORT:-3306}"
readonly DATABASE_ADMIN_USER="${PH7_RELEASE_DB_ADMIN_USER:-root}"
readonly DATABASE_ADMIN_PASSWORD="${PH7_RELEASE_DB_ADMIN_PASSWORD:-}"
readonly DATABASE_SUFFIX="${PH7_RELEASE_DB_SUFFIX:-$$}"
readonly INSTALL_DATABASE="ph7_release_install_${DATABASE_SUFFIX}"
readonly INSTALL_DATABASE_USER="ph7_gate_${DATABASE_SUFFIX:0:20}"
readonly INSTALL_DATABASE_PASSWORD="Gate-${DATABASE_SUFFIX}-Blue-Quartz!72"
readonly OUTPUT_DIRECTORY="${PH7_RELEASE_OUTPUT_DIRECTORY:-$(mktemp -d "${TEMP_DIRECTORY_ROOT%/}/ph7-release-artifacts.XXXXXX")}"

server_pid=''
database_created='false'

cleanup() {
    if [[ -n "$server_pid" ]]; then
        kill "$server_pid" > /dev/null 2>&1 || true
        wait "$server_pid" > /dev/null 2>&1 || true
    fi

    if [[ "$database_created" == 'true' && -f "$MYSQL_CONFIG" ]]; then
        mysql_admin --execute="DROP DATABASE IF EXISTS \`$INSTALL_DATABASE\`; DROP USER IF EXISTS '$INSTALL_DATABASE_USER'@'%';" > /dev/null 2>&1 || true
    fi

    rm -rf -- "$WORK_DIRECTORY"
}
trap cleanup EXIT INT TERM

require_command() {
    if ! command -v "$1" > /dev/null 2>&1; then
        echo "Required command not found: $1" >&2
        exit 1
    fi
}

mysql_admin() {
    mysql --defaults-extra-file="$MYSQL_CONFIG" --batch --skip-column-names "$@"
}

run_step() {
    local sDescription="$1"
    shift
    printf '\n==> %s\n' "$sDescription"
    "$@"
}

if [[ ! "$DATABASE_PORT" =~ ^[0-9]{1,5}$ ]] || [[ ! "$DATABASE_SUFFIX" =~ ^[A-Za-z0-9_]+$ ]]; then
    echo 'The release database port or suffix is invalid.' >&2
    exit 1
fi

for sCommand in composer curl git mysql mysqldump php tar unzip zip; do
    require_command "$sCommand"
done

if [[ -n "$(git -C "$PROJECT_ROOT" status --porcelain --untracked-files=all --ignore-submodules)" ]]; then
    echo 'The complete release gate requires a clean source tree, including untracked files.' >&2
    exit 1
fi

cat > "$MYSQL_CONFIG" <<EOF
[client]
host=$DATABASE_HOST
port=$DATABASE_PORT
user=$DATABASE_ADMIN_USER
password=$DATABASE_ADMIN_PASSWORD
protocol=tcp
EOF

mkdir -p "$OUTPUT_DIRECTORY"

run_step 'Validate the runtime dependency definition' composer validate --strict --working-dir "$PROJECT_ROOT"
run_step 'Validate the installer dependency definition' composer validate --strict --working-dir "$PROJECT_ROOT/_install"
run_step 'Install the locked development dependencies' composer install --working-dir "$PROJECT_ROOT" --no-interaction --no-progress --prefer-dist
run_step 'Audit the locked runtime dependencies' composer audit --locked --working-dir "$PROJECT_ROOT"
run_step 'Audit the locked installer dependencies' composer audit --locked --working-dir "$PROJECT_ROOT/_install"
run_step 'Run static analysis' composer analyse --working-dir "$PROJECT_ROOT"
run_step 'Run deterministic application tests' composer test --working-dir "$PROJECT_ROOT"
run_step 'Run browser smoke journeys' "$PROJECT_ROOT/_tools/browser-audit.sh"
run_step 'Prove fresh schema, migration idempotency, and restoration' "$PROJECT_ROOT/_tools/release-database-check.sh"
run_step 'Build the release archive and provenance' "$PROJECT_ROOT/_tools/packaging.sh" '' "$OUTPUT_DIRECTORY"

readonly ARCHIVE_NAME="pH7Builder-v$RELEASE_VERSION.zip"
readonly ARCHIVE_PATH="$OUTPUT_DIRECTORY/$ARCHIVE_NAME"
readonly PACKAGE_DIRECTORY="$WORK_DIRECTORY/pH7Builder-v$RELEASE_VERSION"

run_step 'Verify the release checksum' sh -c "cd \"$OUTPUT_DIRECTORY\" && { sha256sum -c \"$ARCHIVE_NAME.sha256\" 2>/dev/null || shasum -a 256 -c \"$ARCHIVE_NAME.sha256\"; }"

# shellcheck disable=SC2016
php -r '
    foreach (array_slice($argv, 1) as $sPath) {
        $sJson = file_get_contents($sPath);
        if (!is_string($sJson) || !is_array(json_decode($sJson, true, 512, JSON_THROW_ON_ERROR))) {
            fwrite(STDERR, "Invalid release metadata: {$sPath}\n");
            exit(1);
        }
    }
' \
    "$OUTPUT_DIRECTORY/pH7Builder-v$RELEASE_VERSION.spdx.json" \
    "$OUTPUT_DIRECTORY/pH7Builder-v$RELEASE_VERSION.build.json"

unzip -Z1 "$ARCHIVE_PATH" > "$WORK_DIRECTORY/archive-files.txt"
if grep -Eq '/(_tests|\.github)/|/(\.env|auth\.json)$|\.(pem|key)$|/id_(rsa|ed25519)$' "$WORK_DIRECTORY/archive-files.txt"; then
    echo 'The release archive contains development files or possible secrets.' >&2
    exit 1
fi
if grep -E '/_tools/' "$WORK_DIRECTORY/archive-files.txt" | grep -Ev '/_tools/($|cli(/|$))' > /dev/null; then
    echo 'The release archive contains unsupported development tools.' >&2
    exit 1
fi
if grep -Eq '/_protected/vendor/(phpunit|phpstan|phake)/' "$WORK_DIRECTORY/archive-files.txt"; then
    echo 'The release archive contains development dependencies.' >&2
    exit 1
fi

unzip -q "$ARCHIVE_PATH" -d "$WORK_DIRECTORY"

mysql_admin --execute="CREATE DATABASE \`$INSTALL_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER '$INSTALL_DATABASE_USER'@'%' IDENTIFIED BY '$INSTALL_DATABASE_PASSWORD'; GRANT ALL PRIVILEGES ON \`$INSTALL_DATABASE\`.* TO '$INSTALL_DATABASE_USER'@'%';"
database_created='true'

INSTALL_PORT="${PH7_RELEASE_INSTALL_PORT:-}"
if [[ -z "$INSTALL_PORT" ]]; then
    # shellcheck disable=SC2016
    INSTALL_PORT="$(php -r '
    $rSocket = stream_socket_server("tcp://127.0.0.1:0", $iErrorCode, $sErrorMessage);
    if ($rSocket === false) {
        exit(1);
    }
    $sAddress = stream_socket_get_name($rSocket, false);
    fclose($rSocket);
    echo substr((string)$sAddress, strrpos((string)$sAddress, ":") + 1);
')"
fi
readonly INSTALL_PORT
readonly INSTALL_ORIGIN="http://127.0.0.1:$INSTALL_PORT"

PH7_INSTALL_TOKEN="$INSTALL_TOKEN" PH7_MAILER_DSN='null://null' \
    php -S "127.0.0.1:$INSTALL_PORT" -t "$PACKAGE_DIRECTORY" > "$WORK_DIRECTORY/php-server.log" 2>&1 &
server_pid=$!

for _ in {1..40}; do
    if curl --fail --silent "$INSTALL_ORIGIN/_install/?a=index" > /dev/null; then
        break
    fi
    sleep 0.25
done
if ! curl --fail --silent "$INSTALL_ORIGIN/_install/?a=index" > /dev/null; then
    echo 'The packaged installer did not start.' >&2
    sed -n '1,120p' "$WORK_DIRECTORY/php-server.log" >&2
    exit 1
fi

run_step 'Complete a fresh installation from the release archive' env \
    PH7_INSTALLER_BASE_URL="$INSTALL_ORIGIN" \
    PH7_INSTALL_TOKEN="$INSTALL_TOKEN" \
    PH7_INSTALLER_PACKAGE_ROOT="$PACKAGE_DIRECTORY" \
    PH7_RELEASE_DB_HOST="$DATABASE_HOST" \
    PH7_RELEASE_DB_PORT="$DATABASE_PORT" \
    PH7_RELEASE_DB_USER="$INSTALL_DATABASE_USER" \
    PH7_RELEASE_DB_PASSWORD="$INSTALL_DATABASE_PASSWORD" \
    PH7_RELEASE_INSTALL_DB_NAME="$INSTALL_DATABASE" \
    php "$PROJECT_ROOT/_tools/installer-smoke.php"

run_step 'Verify packaged operator diagnostics' env \
    PH7_MAILER_DSN='null://null' \
    php "$PACKAGE_DIRECTORY/_tools/cli/ph7cms" health:report --json

printf '\nRelease gate passed for pH7Builder %s.\n' "$RELEASE_VERSION"
printf 'Release artefacts: %s\n' "$OUTPUT_DIRECTORY"
