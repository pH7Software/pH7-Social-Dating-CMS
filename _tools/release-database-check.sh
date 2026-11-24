#!/usr/bin/env bash

##
# Title:           pH7Builder Release Database Check
# Description:     Proves fresh schema loading, the 19.4-to-20.0 migration, idempotency, and backup restoration.
#
# Author:          Pierre-Henry Soria <hello@ph7builder.com>
# Copyright:       (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
# License:         MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
##

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly PROJECT_ROOT
readonly DATABASE_HOST="${PH7_RELEASE_DB_HOST:-127.0.0.1}"
readonly DATABASE_PORT="${PH7_RELEASE_DB_PORT:-3306}"
readonly DATABASE_USER="${PH7_RELEASE_DB_ADMIN_USER:-root}"
readonly DATABASE_PASSWORD="${PH7_RELEASE_DB_ADMIN_PASSWORD:-}"
readonly DATABASE_SUFFIX="${PH7_RELEASE_DB_SUFFIX:-$$}"
readonly FRESH_DATABASE="ph7_release_fresh_${DATABASE_SUFFIX}"
readonly UPGRADE_DATABASE="ph7_release_upgrade_${DATABASE_SUFFIX}"
readonly RESTORE_DATABASE="ph7_release_restore_${DATABASE_SUFFIX}"
TEMP_DIRECTORY="$(mktemp -d "${TMPDIR:-/tmp}/ph7-release-db.XXXXXX")"
readonly TEMP_DIRECTORY
readonly MYSQL_CONFIG="$TEMP_DIRECTORY/mysql.cnf"
readonly PREVIOUS_SCHEMA="$TEMP_DIRECTORY/pH7_Core-19.4.0.sql"
readonly BACKUP_FILE="$TEMP_DIRECTORY/pre-upgrade.sql"

if [[ ! "$DATABASE_PORT" =~ ^[0-9]{1,5}$ ]] || [[ ! "$DATABASE_SUFFIX" =~ ^[A-Za-z0-9_]+$ ]]; then
    echo "The release database port or suffix is invalid." >&2
    exit 1
fi

for sCommand in git mysql mysqldump; do
    if ! command -v "$sCommand" > /dev/null 2>&1; then
        echo "Required command not found: $sCommand" >&2
        exit 1
    fi
done

cleanup() {
    mysql_command --execute="DROP DATABASE IF EXISTS \`$FRESH_DATABASE\`; DROP DATABASE IF EXISTS \`$UPGRADE_DATABASE\`; DROP DATABASE IF EXISTS \`$RESTORE_DATABASE\`;" > /dev/null 2>&1 || true
    rm -rf -- "$TEMP_DIRECTORY"
}
trap cleanup EXIT INT TERM

cat > "$MYSQL_CONFIG" <<EOF
[client]
host=$DATABASE_HOST
port=$DATABASE_PORT
user=$DATABASE_USER
password=$DATABASE_PASSWORD
protocol=tcp
EOF
chmod 0600 "$MYSQL_CONFIG"

mysql_command() {
    mysql --defaults-extra-file="$MYSQL_CONFIG" --batch --skip-column-names "$@"
}

assert_query() {
    local sDatabase="$1"
    local sQuery="$2"
    local sExpected="$3"
    local sActual
    sActual="$(mysql_command "$sDatabase" --execute="$sQuery")"
    if [[ "$sActual" != "$sExpected" ]]; then
        echo "Database assertion failed." >&2
        echo "Expected: $sExpected" >&2
        echo "Actual: $sActual" >&2
        exit 1
    fi
}

git -C "$PROJECT_ROOT" show "v19.4.0:_install/data/sql/MySQL/pH7_Core.sql" > "$PREVIOUS_SCHEMA"

mysql_command --execute="CREATE DATABASE \`$FRESH_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql_command "$FRESH_DATABASE" < "$PROJECT_ROOT/_install/data/sql/MySQL/pH7_Core.sql"
assert_query "$FRESH_DATABASE" "SELECT version FROM ph7_modules WHERE vendorName = 'pH7Builder' LIMIT 1;" "1.6.7"
assert_query "$FRESH_DATABASE" "SELECT COUNT(*) FROM ph7_sys_mods_enabled WHERE folderName IN ('milestone-celebration', 'profile-faker');" "2"

mysql_command --execute="CREATE DATABASE \`$UPGRADE_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql_command "$UPGRADE_DATABASE" < "$PREVIOUS_SCHEMA"
mysql_command "$UPGRADE_DATABASE" --execute="UPDATE ph7_sys_mods_enabled SET enabled = '0' WHERE folderName = 'blog';"

mysqldump \
    --defaults-extra-file="$MYSQL_CONFIG" \
    --single-transaction \
    --skip-lock-tables \
    --no-tablespaces \
    "$UPGRADE_DATABASE" > "$BACKUP_FILE"

mysql_command "$UPGRADE_DATABASE" < "$PROJECT_ROOT/_repository/upgrade/19.4.0-20.0.0/data/sql/MySQL/upgrade.sql"
mysql_command "$UPGRADE_DATABASE" < "$PROJECT_ROOT/_repository/upgrade/19.4.0-20.0.0/data/sql/MySQL/upgrade.sql"

assert_query "$UPGRADE_DATABASE" "SELECT version FROM ph7_modules WHERE vendorName = 'pH7Builder' LIMIT 1;" "1.6.7"
assert_query "$UPGRADE_DATABASE" "SELECT COUNT(*) FROM ph7_sys_mods_enabled WHERE folderName = 'milestone-celebration';" "1"
assert_query "$UPGRADE_DATABASE" "SELECT COUNT(*) FROM ph7_sys_mods_enabled WHERE folderName = 'profile-faker';" "1"
assert_query "$UPGRADE_DATABASE" "SELECT enabled FROM ph7_sys_mods_enabled WHERE folderName = 'blog' LIMIT 1;" "0"
assert_query "$UPGRADE_DATABASE" "SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '$UPGRADE_DATABASE' AND TABLE_NAME = 'ph7_sys_mods_enabled' AND COLUMN_NAME = 'folderName';" "64"

mysql_command --execute="CREATE DATABASE \`$RESTORE_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql_command "$RESTORE_DATABASE" < "$BACKUP_FILE"
assert_query "$RESTORE_DATABASE" "SELECT version FROM ph7_modules WHERE vendorName = 'pH7Builder' LIMIT 1;" "1.6.6"
assert_query "$RESTORE_DATABASE" "SELECT enabled FROM ph7_sys_mods_enabled WHERE folderName = 'blog' LIMIT 1;" "0"
assert_query "$RESTORE_DATABASE" "SELECT COUNT(*) FROM ph7_sys_mods_enabled WHERE folderName IN ('milestone-celebration', 'profile-faker');" "0"

echo "Fresh schema, repeated migration, data preservation, and backup restoration checks passed."
