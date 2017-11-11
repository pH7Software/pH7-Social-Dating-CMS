#!/bin/bash

##
# Title:           Useful Unix functions
# Description:     To work correctly, you have to execute this script when you're in the project root with your terminal (generally the parent folder of "_tools/").
#                  (e.g., you@you:/path/to/root-project$ bash _tools/pH7.sh).
#
# Author:          Pierre-Henry Soria <ph7software@gmail.com>
# Copyright:       (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
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

# Clean up the code
function clean-code() {
    _confirm "Are you sure you want to clean up the code?"
    if [ $? -eq 1 ]; then
        accepted_ext="-name '*.php' -or -name '*.css' -or -name '*.js' -or -name '*.html' -or -name '*.xml' -or -name '*.xsl' -or -name '*.xslt' -or -name '*.json' -or -name '*.yml' -or -name '*.tpl' -or -name '*.phs' -or -name '*.ph7' -or -name '*.sh' -or -name '*.sql' -or -name '*.ini' -or -name '*.md' -or -name '*.markdown' -or -name '.htaccess'"
        exec="find . -type f \( $accepted_ext \) -print0 | xargs -0 perl -wi -pe"
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
}

# Backup. Create a compressed archive of the project
function backup() {
    echo "Specify the full path ending with a SLASH where you want the archive will be stored"
    read path
    if [ ! -d $path ]; then
        echo "The path is not a valid directory."
        exit 1
    fi
    file="PH7CMS-backup.tar.bz2"
    full_path=$path$file
    if [ -e  $full_path ]; then
        _confirm "A backup already exists in this directory, do you want to delete it?"
        if [ $? -eq 1 ]; then
            rm $full_path
        else
            echo "Backup canceled. Please choose a different backup directory or delete the old one."
            exit 2
        fi
    fi
    # Remove cache data, tmp and log files before backing up the project
    clear-cache
    remove-tmp-file
    remove-log-file

    tar -jcvf $full_path .
    echo "Backup project successfully created into: $full_path"
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

# CHange permissions of the folders/files (CHMOD)
function _permissions() {
    find . -type f -print0 | sudo xargs -0 chmod $1 # First parameter for Files
    find . -type d -print0 | sudo xargs -0 chmod $2 # Second parameter for Folders

    sudo chmod 777 ./
    sudo chmod 777 -R ./_install/*
    sudo chmod 777 -R ./_repository/module/*
    sudo chmod 777 -R ./_repository/upgrade/*
    sudo chmod 777 -R ./_protected/app/configs/*
    sudo chmod 777 -R ./_protected/data/backup/*
    sudo chmod 777 -R ./_protected/data/tmp/*
    sudo chmod 777 -R ./_protected/data/log/*
}

# Cache permissions (CHMOD)
function _cache-permissions() {
    sudo chmod 777 -R ./_install/data/caches/*
    sudo chmod 777 -R ./_protected/data/cache/*
}

# Save a git project to the specified repo (e.g. github, bitbucket)
function _save-project-to-repo() {
    git remote rm origin
    git remote add origin $1
    git push
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
