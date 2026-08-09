#!/usr/bin/env bash

##
# Title:           pH7Builder Release Packager
# Description:     Builds a production ZIP from the committed tree without modifying the source checkout.
# Usage:           bash _tools/packaging.sh [version] [output-directory]
#
# Author:          Pierre-Henry Soria <hello@ph7builder.com>
# Copyright:       (c) 2014-2026, Pierre-Henry Soria. All Rights Reserved.
# License:         MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
##

set -euo pipefail

SCRIPT_DIRECTORY="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
readonly SCRIPT_DIRECTORY
PROJECT_ROOT="$(cd "$SCRIPT_DIRECTORY/.." && pwd)"
readonly PROJECT_ROOT
readonly VERSION_FILE="$PROJECT_ROOT/_protected/framework/Security/Version.class.php"
readonly INSTALLER_CONTROLLER="$PROJECT_ROOT/_install/library/Controller.class.php"
TEMP_DIRECTORY_ROOT="${TMPDIR:-/tmp}"
TEMP_DIRECTORY_ROOT="${TEMP_DIRECTORY_ROOT%/}"
readonly TEMP_DIRECTORY_ROOT="${TEMP_DIRECTORY_ROOT:-/}"

function read_version() {
    # The single quotes deliberately keep the embedded PHP variables away from Bash expansion.
    # shellcheck disable=SC2016
    php -r '
        $sContents = file_get_contents($argv[1]);
        if (!is_string($sContents) || !preg_match("/(?:KERNEL_|SOFTWARE_)VERSION\\s*=\\s*\x27([^\x27]+)\x27/", $sContents, $aMatches)) {
            fwrite(STDERR, "Unable to read the software version from {$argv[1]}.\n");
            exit(1);
        }
        echo $aMatches[1];
    ' "$1"
}

function require_command() {
    if ! command -v "$1" > /dev/null 2>&1; then
        echo "Required command not found: $1" >&2
        exit 1
    fi
}

function remove_staging_directory() {
    if [[ -n "${STAGING_DIRECTORY:-}" && "$STAGING_DIRECTORY" == "$TEMP_DIRECTORY_ROOT"/ph7builder-package.* ]]; then
        rm -rf -- "$STAGING_DIRECTORY"
    fi
}

for sCommand in composer git php tar zip; do
    require_command "$sCommand"
done

FRAMEWORK_VERSION="$(read_version "$VERSION_FILE")"
readonly FRAMEWORK_VERSION
INSTALLER_VERSION="$(read_version "$INSTALLER_CONTROLLER")"
readonly INSTALLER_VERSION
readonly REQUESTED_VERSION="${1:-$FRAMEWORK_VERSION}"
readonly RELEASE_VERSION="${REQUESTED_VERSION#v}"
readonly OUTPUT_DIRECTORY="${2:-$TEMP_DIRECTORY_ROOT/ph7builder-release}"

if [[ ! "$RELEASE_VERSION" =~ ^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}$ ]]; then
    echo "Invalid release version: $RELEASE_VERSION" >&2
    exit 1
fi

if [[ "$RELEASE_VERSION" != "$FRAMEWORK_VERSION" ]]; then
    echo "Release and framework versions must match." >&2
    echo "Requested: $RELEASE_VERSION; framework: $FRAMEWORK_VERSION" >&2
    exit 1
fi

if [[ "$RELEASE_VERSION" != "$INSTALLER_VERSION" ]]; then
    echo "Release, framework, and installer versions must match." >&2
    echo "Requested: $RELEASE_VERSION; framework: $FRAMEWORK_VERSION; installer: $INSTALLER_VERSION" >&2
    exit 1
fi

if [[ -n "$(git -C "$PROJECT_ROOT" status --porcelain --untracked-files=all --ignore-submodules)" ]]; then
    echo "The source tree, including untracked files, must be clean before packaging." >&2
    exit 1
fi

if ! git -C "$PROJECT_ROOT" ls-files --error-unmatch composer.lock > /dev/null 2>&1; then
    echo "The release dependency lock is not tracked: composer.lock" >&2
    exit 1
fi

readonly RELEASE_DIRECTORY_NAME="pH7Builder-v$RELEASE_VERSION"
readonly ARCHIVE_NAME="$RELEASE_DIRECTORY_NAME.zip"
readonly CHECKSUM_NAME="$ARCHIVE_NAME.sha256"

mkdir -p "$OUTPUT_DIRECTORY"
readonly ARCHIVE_PATH="$OUTPUT_DIRECTORY/$ARCHIVE_NAME"
readonly CHECKSUM_PATH="$OUTPUT_DIRECTORY/$CHECKSUM_NAME"

if [[ -e "$ARCHIVE_PATH" || -e "$CHECKSUM_PATH" ]]; then
    echo "Release output already exists. Move it away or choose another output directory." >&2
    exit 1
fi

STAGING_DIRECTORY="$(mktemp -d "$TEMP_DIRECTORY_ROOT/ph7builder-package.XXXXXX")"
readonly STAGING_DIRECTORY
readonly PACKAGE_DIRECTORY="$STAGING_DIRECTORY/$RELEASE_DIRECTORY_NAME"
readonly STAGED_ARCHIVE_PATH="$STAGING_DIRECTORY/$ARCHIVE_NAME"
trap remove_staging_directory EXIT INT TERM

git -C "$PROJECT_ROOT" archive --format=tar --prefix="$RELEASE_DIRECTORY_NAME/" HEAD |
    tar -xf - -C "$STAGING_DIRECTORY"

composer install \
    --working-dir="$PACKAGE_DIRECTORY" \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

# Composer uses project-local cache paths. They are build artifacts, not release contents.
rm -rf -- \
    "$PACKAGE_DIRECTORY/_protected/vendor/cache" \
    "$PACKAGE_DIRECTORY/_install/vendor/cache"

# Start from restrictive, predictable modes.
find "$PACKAGE_DIRECTORY" -type d -exec chmod 0755 {} +
find "$PACKAGE_DIRECTORY" -type f -exec chmod 0644 {} +

if [[ -d "$PACKAGE_DIRECTORY/_protected/vendor/bin" ]]; then
    find "$PACKAGE_DIRECTORY/_protected/vendor/bin" -type f -exec chmod 0755 {} +
fi

# The installer creates _constants.php at the root, atomically replaces the
# application config, and removes its own tree on completion. The application
# config directory is writable only for that installation step. Runtime data
# directories and explicitly supported editor targets remain group-writable;
# executable configuration and source directories do not.
chmod 0775 "$PACKAGE_DIRECTORY"
find "$PACKAGE_DIRECTORY/_install" -type d -exec chmod 0775 {} +
chmod 0775 "$PACKAGE_DIRECTORY/_protected/app/configs"

readonly WRITABLE_DIRECTORIES=(
    "_install/data/caches"
    "_install/data/logs"
    "data"
    "_repository/module"
    "_protected/data/backup"
    "_protected/data/cache"
    "_protected/data/log"
    "_protected/data/tmp"
)

for sRelativeDirectory in "${WRITABLE_DIRECTORIES[@]}"; do
    sWritableDirectory="$PACKAGE_DIRECTORY/$sRelativeDirectory"
    if [[ -d "$sWritableDirectory" ]]; then
        find "$sWritableDirectory" -type d -exec chmod 0775 {} +
        find "$sWritableDirectory" -type f -exec chmod 0664 {} +
    fi
done

chmod 2775 "$PACKAGE_DIRECTORY/_install/data/caches"

find \
    "$PACKAGE_DIRECTORY/_protected/app/configs/banned" \
    "$PACKAGE_DIRECTORY/_protected/app/configs/suggestions" \
    -type f -name '*.txt' -exec chmod 0664 {} +
find "$PACKAGE_DIRECTORY/_protected/app/configs/routes" -type f -name '*.xml' -exec chmod 0664 {} +

find \
    "$PACKAGE_DIRECTORY/_protected/app/system/global/views/base/tpl/mail" \
    "$PACKAGE_DIRECTORY/_protected/app/system/modules/page/views/base" \
    -type f -name '*.tpl' -exec chmod 0664 {} +
find "$PACKAGE_DIRECTORY/templates/themes" -type f \
    \( -name '*.tpl' -o -name '*.css' -o -name '*.js' \) \
    -exec chmod 0664 {} +

readonly MUTABLE_MODULE_CONFIGS=(
    "affiliate"
    "payment"
    "sms-verification"
    "video"
)

for sModuleName in "${MUTABLE_MODULE_CONFIGS[@]}"; do
    sModuleConfig="$PACKAGE_DIRECTORY/_protected/app/system/modules/$sModuleName/config/config.ini"
    if [[ -f "$sModuleConfig" ]]; then
        chmod 0664 "$sModuleConfig"
    fi
done

# Normalize timestamps and ordering so the same commit and build toolchain produce the same ZIP.
ARCHIVE_TIMESTAMP="$(TZ=UTC git -C "$PROJECT_ROOT" show -s --format=%cd --date=format-local:%Y%m%d%H%M.%S HEAD)"
readonly ARCHIVE_TIMESTAMP
find "$PACKAGE_DIRECTORY" -depth -exec touch -h -t "$ARCHIVE_TIMESTAMP" {} +

(
    cd "$STAGING_DIRECTORY"
    LC_ALL=C find "$RELEASE_DIRECTORY_NAME" -print |
        LC_ALL=C sort |
        zip -X -y -q "$STAGED_ARCHIVE_PATH" -@
)

mv "$STAGED_ARCHIVE_PATH" "$ARCHIVE_PATH"

if command -v sha256sum > /dev/null 2>&1; then
    (
        cd "$OUTPUT_DIRECTORY"
        sha256sum "$ARCHIVE_NAME" > "$CHECKSUM_NAME"
    )
elif command -v shasum > /dev/null 2>&1; then
    (
        cd "$OUTPUT_DIRECTORY"
        shasum -a 256 "$ARCHIVE_NAME" > "$CHECKSUM_NAME"
    )
else
    echo "Neither sha256sum nor shasum is available." >&2
    exit 1
fi

echo "Release archive: $ARCHIVE_PATH"
echo "SHA-256 file: $CHECKSUM_PATH"
