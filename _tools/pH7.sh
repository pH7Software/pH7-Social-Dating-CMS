#!/bin/bash

##
# Title:           Useful Unix functions
# Description:     To work correctly, you have to execute this script when you're in the project root with your terminal (generally the parent folder of "_tools/").
#                  (e.g., you@you:/path/to/root-project$ bash _tools/pH7.sh).
#
# Author:          Pierre-Henry Soria <hello@ph7cms.com>
# Copyright:       (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
# License:         GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
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
    echo "17) update geoip db"
    echo "18) git checkout"


    read option
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
      "update geoip db") update-geoip-db;;
      "git checkout") git-checkout;;
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

        #_clean-indent
        echo "Code has been cleaned!"
    fi
}

# Count all line of code in all files
function count-line-code() {
    find . -type f | xargs wc -l
}

# Count all line of code in PHP files
function count-php-line-code() {
    find . -type f -name '*.php' | xargs wc -l
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
    _permissions 666 777
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

# Push the project into GitHub and Bitbucket repos
function save-code() {
    # Bitbucket repo
    _save-project-to-repo git@bitbucket.org:pH_7/ph7cms-social-dating-app-site-builder.git

    # GitLab repo
    _save-project-to-repo git@gitlab.com:pH-7/pH7CMS.git

    # GitHub repo
    _save-project-to-repo git@github.com:pH7Software/pH7-Social-Dating-CMS.git

    echo "Yaaay! Changes successfully saved into remote repos!"

    # Save the latest GitHub changes on Internet Archive for the record
    _save-project-to-ia https://github.com/pH7Software/pH7-Social-Dating-CMS
    echo "GitHub repo also saved on Internet Archive.org"
}

# Backup. Create a compressed archive of the project
function backup() {
    echo "Specify the full path ending with a SLASH where you want the archive will be stored"
    read path
    if [ ! -d "$path" ]; then
        echo "The path is not a valid directory."
        exit 1
    fi
    filename="pH7Builder-backup.tar.bz2"
    full_path=$path$filename
    if [ -e "$full_path" ]; then
        _confirm "A backup already exists in this directory, do you want to delete it?"
        if [ $? -eq 1 ]; then
            rm $full_path
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

    tar -jcvf $full_path .
    echo "Backup project successfully created into: $full_path"
}

# Update GeoIP database
function update-geoip-db() {
    geo_archive_filename="GeoLite2-City.tar.gz"
    database_geo_lite_url="http://geolite.maxmind.com/download/geoip/database/$geo_archive_filename"
    target_path="./_protected/framework/Geo/Ip/"
    db_filename="GeoLite2-City.mmdb"
    tmp_filename="tmp_db.tar.gz"
    full_tmp_path=$target_path$tmp_filename
    full_db_path=$target_path$db_filename

    echo "Downloading GeoIP Lite DB from $database_geo_lite_url"
    echo "Temporary saving GeoIp DB to $full_tmp_path"
    wget $database_geo_lite_url -O $full_tmp_path

    if [ ! -f $full_tmp_path ]; then
        echo "$full_tmp_path wasn't found."
        exit 1
    fi

    if [ -f $full_db_path ]; then
        echo "Removing previous Geo DB version at $full_db_path"
        rm $full_db_path
    fi

    echo "Extracting archive to $full_tmp_path"
    tar -xvzf $full_tmp_path -C $target_path --strip-components 1

    if [ ! -f $full_db_path ]; then
        echo "$full_db_path not found! Please try to install GeoIP DB manually (${database_geo_lite_url})"
        exit 1
    fi

    echo "Removing temporary file $full_tmp_path"
    rm $full_tmp_path

    if [ -f ${target_path}LICENSE.txt ] && [ -f ${target_path}COPYRIGHT.txt ] && [ -f ${target_path}README.txt ]; then
    echo "Removing not necessary txt files."
        rm ${target_path}LICENSE.txt ${target_path}COPYRIGHT.txt ${target_path}README.txt
    fi

    echo "GeoIP DB successfully updated at $full_db_path"
}

# Clear caches to avoid wrong data when checking out to another git branch
function git-checkout() {
    echo "Give the name of the git branch you want to checkout"
    read branch_name
    if [ ! -z "$branch_name" ]; then
        echo "Removing cache files before checking out the branch. Please answer 'Y'"
        clear-cache
        git checkout $branch_name
    else
        echo "You need to enter the git branch name."
    fi
}


#### Private functions ####

# Clean coding-style. Set PSR-* Ident Style (http://cs.sensiolabs.org)
function _clean-indent() {
    indents=indentation,function_declaration,function_typehint_space,
method_argument_space,line_after_namespace,empty_return,linefeed,trailing_spaces,eof_ending,php_closing_tag,multiple_use,parenthesis,extra_empty_lines,short_tag,php4_constructor,phpdoc_scalar,
lowercase_keywords,lowercase_constants,array_element_no_space_before_comma,array_element_white_space_after_comma,
extra_empty_lines,encoding

    cs_script="./_tools/php-cs-fixer.phar"

    find . -type f -name "*.php" -exec php $cs_script fix {} --fixers=$indents \;
}

# Change permissions of the folders/files (CHMOD)
function _permissions() {
    find . -type f -print0 | sudo xargs -0 chmod $1 # First parameter for Files
    find . -type d -print0 | sudo xargs -0 chmod $2 # Second parameter for Folders

    sudo chmod -R 777 ./_install/data/logs/
    sudo chmod -R 777 ./data/system/modules/*
    sudo chmod -R 777 ./_repository/module/*
    sudo chmod -R 777 ./_repository/upgrade/*
    sudo chmod -R 777 ./_protected/app/configs/*
    sudo chmod -R 777 ./_protected/data/backup/*
    sudo chmod -R 777 ./_protected/data/tmp/*
    sudo chmod -R 777 ./_protected/data/log/*
}

# Cache permissions (CHMOD)
function _cache-permissions() {
    sudo chmod -R 777 ./_install/data/caches/*
    sudo chmod -R 777 ./_protected/data/cache/*
}

# Save a git project to the specified repo (e.g. github, bitbucket)
function _save-project-to-repo() {
    git remote rm origin
    git remote add origin $1
    git push
}

# Save repo on Internet Archive
function _save-project-to-ia() {
    ia_saver_url="https://web.archive.org/save/"

    curl -s $ia_saver_url$1 > /dev/null
}

# Confirmation of orders entered
function _confirm() {
    echo $1 "(Y/N)"
    read input
    input=$(_to-lower $input) # Case-insensitive
    if [ "$input" == "y" ]; then
        return 1
    else
        return 0
    fi
}

# To lower
function _to-lower() {
    echo $1 | tr '[:upper:]' '[:lower:]'
}

function _error() {
    echo "ERROR!"
}

init
