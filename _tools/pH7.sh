#!/bin/bash

##
# Title:           Useful Unix functions
# Description:     To work correctly, you have to execute this script when you're in the project root with your terminal (generally the parent folder of "_tools/").
#                  (e.g., you@you:/path/to/root-project$ bash _tools/pH7.sh).
#
# Author:          Pierre-Henry Soria <hello@ph7builder.com>
# Copyright:       (c) 2012-2026, Pierre-Henry Soria and pH7Builder contributors.
# License:         MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
##

function init() {
    echo "Please enter a command, OPTION:"
    echo "1) clear cache"
    echo "2) remove tmp file"
    echo "3) remove log file"
    echo "4) clean code"
    echo "5) count line code"
    echo "6) count php line code"
    echo "7) count file"
    echo "8) count php file"
    echo "9) count dir"
    echo "10) show empty file"
    echo "11) show empty dir"
    echo "12) file permissions"
    echo "13) file strict permissions"
    echo "14) save code"
    echo "15) backup"
    echo "16) remove sensitive data"
    echo "17) git checkout"
    echo "18) install geoip db"


    read -r option
    case $option in
      "clear cache") clear-cache;;
      "remove tmp file") remove-tmp-file;;
      "remove log file") remove-log-file;;
      "clean code") clean-code;;
      "count line code") count-line-code;;
      "count php line code") count-php-line-code;;
      "count file") count-file;;
      "count php file") count-php-file;;
      "count dir") count-dir;;
      "show empty file") show-empty-file;;
      "show empty dir") show-empty-dir;;
      "file permissions") file-permissions;;
      "file strict permissions") file-strict-permissions;;
      "save code") save-code;;
      "backup") backup;;
      "remove sensitive data") remove-sensitive-data;;
      "git checkout") git-checkout;;
      "install geoip db"|"update geoip db") install-geoip-db;;
      *) _error
    esac
}

# Delete all caches
function clear-cache() {
    _confirm "Are you sure you want to delete caches?"
    if [ $? -eq 1 ]; then
        _cache-permissions

        # public
        rm -rf ./_install/data/caches/smarty_compile/*
        rm -rf ./_install/data/caches/smarty_cache/*

        # _protected
        rm -rf ./_protected/data/cache/pH7tpl_compile/*
        rm -rf ./_protected/data/cache/pH7tpl_cache/*
        rm -rf ./_protected/data/cache/pH7_static/*
        rm -rf ./_protected/data/cache/pH7_cache/*
        echo "Caches have been removed!"
    fi
}

# Deleting temporary files
function remove-tmp-file() {
    _confirm "Are you sure you want to remove the temporary files (e.g., file.pl~, ._file.php)?"
    if [ $? -eq 1 ]; then
        find . -type f \( -name '*~' -or -name '*.tmp' -or -name '*.swp' -or -name '.directory' -or -name '._*' -or -name '.DS_Store*' -or -name 'Thumbs.db' \) -exec rm {} \;
        echo "Temporary files have been removed!"
    fi
}

# Deleting log files
function remove-log-file() {
    _confirm "Are you sure you want to remove all log files (*.log)?"
    if [ $? -eq 1 ]; then
        find . -type f -name '*.log' -exec rm {} \;
        echo "Log files have been removed!"
    fi
}

# Remove the sensitive data (such as DB details, filesystem paths, ...)
function remove-sensitive-data() {
    _confirm "Are you sure to remove the config details? Your website won't work anymore after this action."
    if [ $? -eq 1 ]; then
        rm ./_protected/app/configs/config.ini
        rm ./_constants.php
        rm -rf ./_protected/data/backup/file/*
        rm -rf ./_protected/data/backup/sql/*

        echo "Sensitive data removed!"
    fi
}

# Clean up the code
function clean-code() {
    _confirm "Are you sure you want to clean up the code?"
    if [ $? -eq 1 ]; then
        accepted_extensions="-name '*.php' -or -name '*.css' -or -name '*.js' -or -name '*.html' -or -name '*.xml' -or -name '*.xsl' -or -name '*.xslt' -or -name '*.svg' -or -name '*.json' -or -name '*.yml' -or -name '*.tpl' -or -name '*.phs' -or -name '*.ph7' -or -name '*.sh' -or -name '*.sql' -or -name '*.ini' -or -name '*.md' -or -name '*.markdown' -or -name '.htaccess'"
        exec="find . -type f \( $accepted_extensions \) -print0 | xargs -0 perl -wi -pe"
        eval "$exec 's/\s+$/\n/'"
        eval "$exec 's/\t/    /g'"

        echo "Code has been cleaned!"
    fi
}

# Count all line of code in all files
function count-line-code() {
    find . -type f -exec wc -l {} +
}

# Count all line of code in PHP files
function count-php-line-code() {
    find . -type f -name '*.php' -exec wc -l {} +
}

# Count all files
function count-file() {
    find . -type f | wc -l
}

# Count all PHP files
function count-php-file() {
    find . -type f -name '*.php' | wc -l
}

# Count all directories
function count-dir() {
    find . -type d | wc -l
}

# Display all empty files (0 bytes)
function show-empty-file() {
    find . -type f -size 0
}

# Display all empty directories (useful for knowing what will be ignored by Git)
function show-empty-dir() {
    find . -type d -empty
}

# Check and correct file permissions (CHMOD)
# These permissions allow editing and creating files in the File Management admin module.
function file-permissions() {
    _permissions 664 775
    _cache-permissions
    echo "Permissions have been changed!"
}

# Check and correct file permissions (CHMOD)
# These permissions don't allow editing and creating files in the File Management admin module.
function file-strict-permissions() {
    _permissions 644 755
    _cache-permissions
    echo "Strict Permissions have been changed!"
}

# Push the project into the maintained GitHub and GitLab repositories
function save-code() {
    # GitLab repo
    _save-project-to-repo gitlab git@gitlab.com:pH-7/pH7Builder.git

    # GitHub repo
    _save-project-to-repo github git@github.com:pH7Software/pH7-Social-Dating-CMS.git

    echo "Yaaay! Changes successfully saved into remote repos!"

    # Save the latest GitHub changes on Internet Archive for the record
    _save-project-to-ia https://github.com/pH7Software/pH7-Social-Dating-CMS
    echo "GitHub repo also saved on Internet Archive.org"
}

# Backup. Create a compressed archive of the project
function backup() {
    echo "Specify the full path ending with a SLASH where you want the archive will be stored"
    read -r path
    if [ ! -d "$path" ]; then
        echo "The path is not a valid directory."
        exit 1
    fi
    filename="pH7Builder-backup.tar.bz2"
    full_path=$path$filename
    if [ -e "$full_path" ]; then
        _confirm "A backup already exists in this directory, do you want to delete it?"
        if [ $? -eq 1 ]; then
            rm "$full_path"
        else
            echo "Backup canceled. Please choose a different backup directory or delete the old one."
            exit 2
        fi
    fi
    # Remove sensitive data, cache data, tmp and log files before backing up the project
    remove-sensitive-data
    clear-cache
    remove-tmp-file
    remove-log-file

    tar -jcvf "$full_path" .
    echo "Backup project successfully created into: $full_path"
}

# Install or restore the bundled GeoIP database. No MaxMind account, licence key or other credential is needed:
# the bundled GeoLite2-City build (24 December 2019) is the last one MaxMind published under CC BY-SA 4.0, which is
# why it can still ship with the project. Newer GeoLite builds are only available to MaxMind account holders under
# the GeoLite EULA and cannot be redistributed here. See _protected/framework/Geo/Ip/update-geo-database-version.txt
function install-geoip-db() {
    geoip_path="./_protected/framework/Geo/Ip"
    geoip_db_path="$geoip_path/GeoLite2-City.mmdb"

    # Internet Archive capture of MaxMind's former public download URL, taken while that build was offered under CC BY-SA 4.0
    bundled_archive_url="https://web.archive.org/web/20191227182209id_/https://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz"
    bundled_archive_sha256="d1c309c4cf676884fe2314522de0819767ce44f6c776d60e2657e9eeaf93640c"
    bundled_db_sha256="a253d9cd68fe17b00087da24375f31f07cd4bb3852dc5fe3afe37b8f59e5abd0"
    bundled_db_label="bundled GeoLite2-City build (24 December 2019, CC BY-SA 4.0)"

    _require-sha256-tool

    echo "Path of a GeoLite2-City .mmdb or .tar.gz you already have, or leave empty to install the $bundled_db_label:"
    read -r geoip_source_path
    geoip_source_path="${geoip_source_path/#\~/$HOME}"

    if [ -n "$geoip_source_path" ]; then
        _install-geoip-db-from-file "$geoip_source_path"
        return
    fi

    if [ "$(_sha256 "$geoip_db_path")" = "$bundled_db_sha256" ]; then
        echo "The $bundled_db_label is already installed at $geoip_db_path"
        _restore-geoip-db-notices
        _show-geoip-db-info "$geoip_db_path"
        return
    fi

    if [ -f "$geoip_db_path" ] && _show-geoip-db-info "$geoip_db_path"; then
        _confirm "A different GeoIP database is installed at $geoip_db_path. Replace it with the $bundled_db_label?"
        if [ $? -ne 1 ]; then
            echo "Existing GeoIP database kept."
            return
        fi
    fi

    # Prefer the copy tracked by Git, otherwise download the archived build
    if git ls-files --error-unmatch "$geoip_db_path" >/dev/null 2>&1; then
        echo "Restoring the tracked copy from Git"
        git checkout -- "$geoip_db_path"
        _restore-geoip-db-notices force
    fi

    if [ "$(_sha256 "$geoip_db_path")" != "$bundled_db_sha256" ]; then
        _create-geoip-tmp-dir
        echo "Downloading the $bundled_db_label from $bundled_archive_url"
        _download "$bundled_archive_url" "$geoip_tmp_path/GeoLite2-City.tar.gz"
        if [ "$(_sha256 "$geoip_tmp_path/GeoLite2-City.tar.gz")" != "$bundled_archive_sha256" ]; then
            echo "The downloaded archive doesn't match its expected SHA-256 checksum. Nothing was installed."
            exit 1
        fi

        _extract-geoip-db-archive "$geoip_tmp_path/GeoLite2-City.tar.gz" "$geoip_tmp_path"
        if [ "$(_sha256 "$geoip_extracted_db_path")" != "$bundled_db_sha256" ]; then
            echo "The extracted database doesn't match its expected SHA-256 checksum. Nothing was installed."
            exit 1
        fi
        _install-geoip-db-file "$geoip_extracted_db_path" "$(dirname "$geoip_extracted_db_path")"
    fi

    echo "GeoIP DB successfully installed at $geoip_db_path"
    _show-geoip-db-info "$geoip_db_path"
}

# Clear caches to avoid wrong data when checking out to another git branch
function git-checkout() {
    echo "Give the name of the git branch you want to checkout"
    read -r branch_name
    if [ -n "$branch_name" ]; then
        echo "Removing cache files before checking out the branch. Please answer 'Y'"
        clear-cache
        git checkout "$branch_name"
    else
        echo "You need to enter the git branch name."
    fi
}


#### Private functions ####

# Change permissions of the folders/files (CHMOD)
function _permissions() {
    find . -type f -print0 | sudo xargs -0 chmod "$1" # First parameter for Files
    find . -type d -print0 | sudo xargs -0 chmod "$2" # Second parameter for Folders

    _make-group-writable ./_install/data/logs/
    _make-group-writable ./data/system/modules/
    _make-group-writable ./_repository/module/
    _make-group-writable ./_repository/upgrade/
    _make-group-writable ./_protected/app/configs/
    _make-group-writable ./_protected/data/backup/
    _make-group-writable ./_protected/data/tmp/
    _make-group-writable ./_protected/data/log/
}

# Cache permissions (CHMOD)
function _cache-permissions() {
    _make-group-writable ./_install/data/caches/
    _make-group-writable ./_protected/data/cache/
}

function _make-group-writable() {
    if [ -d "$1" ]; then
        sudo find "$1" -type d -exec chmod 775 {} +
        sudo find "$1" -type f -exec chmod 664 {} +
    fi
}

# Save a git project to the specified repo (e.g. github, bitbucket)
function _save-project-to-repo() {
    git remote rm "$1" # Remove remote name if it already exists
    git remote add "$1" "$2"
    git push "$1"
}

# Save repo on Internet Archive
function _save-project-to-ia() {
    ia_saver_url="https://web.archive.org/save/"

    curl -s "$ia_saver_url$1" > /dev/null
}

# Install a GeoLite2-City .mmdb or MaxMind .tar.gz the operator already has (e.g. a newer build obtained under MaxMind's GeoLite EULA)
function _install-geoip-db-from-file() {
    if [ ! -f "$1" ]; then
        echo "$1 is not a file."
        exit 1
    fi

    case "$1" in
        *.tar.gz|*.tgz)
            _create-geoip-tmp-dir
            _extract-geoip-db-archive "$1" "$geoip_tmp_path"
            _install-geoip-db-file "$geoip_extracted_db_path" "$(dirname "$geoip_extracted_db_path")"
            ;;
        *.mmdb)
            _install-geoip-db-file "$1"
            ;;
        *)
            echo "$1 must be a .mmdb or .tar.gz file."
            exit 1
            ;;
    esac

    echo "GeoIP DB successfully installed at ./_protected/framework/Geo/Ip/GeoLite2-City.mmdb"
    _show-geoip-db-info ./_protected/framework/Geo/Ip/GeoLite2-City.mmdb
    _show-geoip-db-licence-reminder
}

# Create the temporary directory for GeoIP archives in geoip_tmp_path; it is removed whenever the script exits
function _create-geoip-tmp-dir() {
    geoip_tmp_path=$(mktemp -d "${TMPDIR:-/tmp}/ph7-geoip.XXXXXX") || exit 1
    trap 'rm -rf "$geoip_tmp_path"' EXIT
}

# Extract GeoLite2-City.mmdb (with MaxMind's notice files) from a MaxMind .tar.gz into $2; sets geoip_extracted_db_path
function _extract-geoip-db-archive() {
    echo "Extracting $1"
    tar -xzf "$1" -C "$2" || exit 1

    geoip_extracted_db_path=$(find "$2" -type f -name 'GeoLite2-City.mmdb' | head -n 1)
    if [ -z "$geoip_extracted_db_path" ]; then
        echo "No GeoLite2-City.mmdb was found in $1"
        exit 1
    fi
}

# Validate a MaxMind City database and atomically install it, along with MaxMind's notice files from directory $2 when given
function _install-geoip-db-file() {
    geoip_path="./_protected/framework/Geo/Ip"

    if ! _read-geoip-db-info "$1"; then
        echo "$1 is not a readable MaxMind City database. Nothing was installed."
        exit 1
    fi

    if ! cp "$1" "$geoip_path/GeoLite2-City.mmdb.tmp" || ! mv -f "$geoip_path/GeoLite2-City.mmdb.tmp" "$geoip_path/GeoLite2-City.mmdb"; then
        rm -f "$geoip_path/GeoLite2-City.mmdb.tmp"
        echo "Unable to write $geoip_path/GeoLite2-City.mmdb"
        exit 1
    fi
    chmod 644 "$geoip_path/GeoLite2-City.mmdb"

    # CC BY-SA 4.0 and the GeoLite EULA both require keeping the notices shipped with the database
    if [ -n "$2" ]; then
        for notice_file in LICENSE.txt COPYRIGHT.txt README.txt; do
            if [ -f "$2/$notice_file" ]; then
                cp "$2/$notice_file" "$geoip_path/$notice_file"
            fi
        done
    fi
}

# Put back MaxMind's notice files for the bundled database from Git; "force" also overwrites modified ones
function _restore-geoip-db-notices() {
    for notice_file in LICENSE.txt COPYRIGHT.txt README.txt Maxmind-GeoLite2.license.txt; do
        notice_path="./_protected/framework/Geo/Ip/$notice_file"
        if { [ ! -f "$notice_path" ] || [ "$1" = "force" ]; } && git ls-files --error-unmatch "$notice_path" >/dev/null 2>&1; then
            git checkout -- "$notice_path"
        fi
    done
}

# Read a MaxMind City database's type and build date into geoip_db_type, geoip_db_build_epoch and geoip_db_build_date;
# fails when the file isn't a readable City database
function _read-geoip-db-info() {
    geoip_db_type=""
    geoip_db_build_epoch=""
    geoip_db_build_date=""

    if command -v php >/dev/null 2>&1 && [ -f ./_protected/vendor/autoload.php ]; then
        # shellcheck disable=SC2016 # the single-quoted PHP code is meant to reach PHP unexpanded
        geoip_db_info=$(PH7_GEOIP_DB_PATH="$1" php -r '
            require "./_protected/vendor/autoload.php";
            try {
                $oReader = new MaxMind\Db\Reader(getenv("PH7_GEOIP_DB_PATH"));
                $oMetadata = $oReader->metadata();
                $oReader->close();
            } catch (Throwable $oE) {
                fwrite(STDERR, $oE->getMessage() . PHP_EOL);
                exit(1);
            }
            echo $oMetadata->databaseType, "|", $oMetadata->buildEpoch, "|", gmdate("j F Y", $oMetadata->buildEpoch);
        ') || return 1
        IFS='|' read -r geoip_db_type geoip_db_build_epoch geoip_db_build_date <<< "$geoip_db_info"

        case "$geoip_db_type" in
            *City*) return 0;;
            *) echo "$geoip_db_type is not a City database."; return 1;;
        esac
    fi

    # Without PHP, only the MaxMind DB metadata marker can be checked
    if grep -q -a 'MaxMind.com' "$1" 2>/dev/null; then
        geoip_db_type="MaxMind"
        return 0
    fi
    return 1
}

# Print a MaxMind City database's type and build date; fails when the file isn't a readable City database
function _show-geoip-db-info() {
    _read-geoip-db-info "$1" || return 1

    if [ -n "$geoip_db_build_date" ]; then
        echo "$geoip_db_type database built on $geoip_db_build_date"
    else
        echo "$geoip_db_type database (install the PHP dependencies to see its build date)"
    fi
}

# Builds before 30 December 2019 are under CC BY-SA 4.0; later ones are governed by MaxMind's GeoLite EULA
function _show-geoip-db-licence-reminder() {
    if [ -n "$geoip_db_build_epoch" ] && [ "$geoip_db_build_epoch" -lt 1577664000 ]; then
        echo "This build is licensed under CC BY-SA 4.0. Keep MaxMind's notice files next to it."
    else
        echo "This build is governed by MaxMind's GeoLite EULA (https://www.maxmind.com/en/geolite2/eula):"
        echo "keep MaxMind's attribution and notice files, replace it within 30 days of each MaxMind release,"
        echo "and don't commit it to the repository or redistribute it with the project."
    fi
}

# Download $1 to $2 with curl or wget
function _download() {
    if command -v curl >/dev/null 2>&1; then
        curl -fL --retry 3 --progress-bar -o "$2" "$1" && return
    elif command -v wget >/dev/null 2>&1; then
        wget -O "$2" "$1" && return
    else
        echo "Neither curl nor wget was found. Please install one of them."
        exit 1
    fi

    echo "Downloading $1 failed. Nothing was installed."
    exit 1
}

function _require-sha256-tool() {
    if ! command -v sha256sum >/dev/null 2>&1 && ! command -v shasum >/dev/null 2>&1 && ! command -v openssl >/dev/null 2>&1; then
        echo "No SHA-256 tool (sha256sum, shasum or openssl) was found. Please install one of them."
        exit 1
    fi
}

# Print the SHA-256 checksum of a file (nothing when the file is missing)
function _sha256() {
    if [ ! -f "$1" ]; then
        return 1
    elif command -v sha256sum >/dev/null 2>&1; then
        sha256sum "$1" | cut -d ' ' -f 1
    elif command -v shasum >/dev/null 2>&1; then
        shasum -a 256 "$1" | cut -d ' ' -f 1
    else
        openssl dgst -sha256 -r "$1" | cut -d ' ' -f 1
    fi
}

# Confirmation of orders entered
function _confirm() {
    echo "$1" "(Y/N)"
    read -r input
    input=$(_to-lower "$input") # Case-insensitive
    if [ "$input" == "y" ]; then
        return 1
    else
        return 0
    fi
}

# To lower
function _to-lower() {
    echo "$1" | tr '[:upper:]' '[:lower:]'
}

function _error() {
    echo "ERROR!"
}

init
