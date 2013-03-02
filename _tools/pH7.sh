#!/bin/bash

##
# Title:           Useful Unix functions
# Description:     For this script to work correctly, you should use it when you're at the root of the project with the terminal (e.g., you@you:/path/to/root-project$ bash _tools/pH7.sh).
# Author:          By Pierre-Henry Soria <pierrehs@hotmail.com>
# Copyright:       (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
##

function init() {
    echo "Please enter a command, OPTION:"
    echo "1) clear cache"
    echo "2) remove tmp file"
    echo "3) clean code"
    echo "4) count line code"
    echo "5) count php line code"
    echo "6) count file"
    echo "7) count php file"
    echo "8) count dir"
    echo "9) show empty file"
    echo "10) backup"


    read option
    case $option in
      "clear cache") clear-cache;;
      "remove tmp file") remove-tmp-file;;
      "clean code") clean-code;;
      "count line code") count-line-code;;
      "count php line code") count-php-line-code;;
      "count file") count-file;;
      "count php file") count-php-file;;
      "count dir") count-dir;;
      "show empty file") show-empty-file;;
      "backup") backup;;
      *) _error
    esac
}

# Delete all caches
function clear-cache() {
    _confirm "Are you sure you want to delete caches?"
    if [ $? -eq 1 ]; then
        sudo chmod 777 -R ./_protected/data/cache/*
        sudo chmod 777 -R ./public/_install/data/caches/*

        # public
        rm -rf ./public/_install/data/caches/smarty_compile/*
        rm -rf ./public/_install/data/caches/smarty_cache/*

        # _protected
        rm -rf ./_protected/data/cache/pH7tpl_compile/*
        rm -rf ./_protected/data/cache/pH7tpl_cache/*
        rm -rf ./_protected/data/cache/pH7_static/*
        rm -rf ./_protected/data/cache/pH7_cache/*
        echo "The caches were deleted!"
    fi
}

# Deleting temporary files
function remove-tmp-file() {
    _confirm "Are you sure you want to remove the temporary files (e.g. file.pl~, ._file.py)?"
    if [ $? -eq 1 ]; then
        find . -type f \( -name "*~" -or -name "*.swp" -or -name ".directory" -or -name "._*" -or -name ".DS_Store*" -or -name "Thumbs.db" \) -exec rm {} \;
        echo "The temporary files were deleted!"
    fi
}

# Clean up the code
function clean-code() {
    _confirm "Are you sure you want to clean up the code?"
    if [ $? -eq 1 ]; then
        find . -type f \( -name "*.php" -or -name "*.css" -or -name "*.js" -or -name "*.html" -or -name "*.xml" -or -name "*.xsl" -or -name "*.tpl" -or -name "*.phs" -or -name "*.ph7" -or -name "*.sh" -or -name "*.sql" -or -name ".htaccess" \) | xargs perl -wi -pe 's/\s+$/\n/'
        find . -type f \( -name "*.php" -or -name "*.css" -or -name "*.js" -or -name "*.html" -or -name "*.xml" -or -name "*.xsl" -or -name "*.tpl" -or -name "*.phs" -or -name "*.ph7" -or -name "*.sh" -or -name "*.sql" -or -name ".htaccess" \) | xargs perl -wi -pe 's/\t/    /g'

        # _clean-indent
        echo "The code has been cleaned!"
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

# Backup. Create a compressed archive of the project
function backup() {
    echo "Specify the path ending with a SLASH where the archive will be stored"
    read path
    if [ ! -d $path ]; then
        echo "The path is not a valid directory."
        exit 1
    fi
    file="PH7SocialDatingCms.tar.bz2"
    full_path=$path$file
    if [ -e  $full_path ]; then
        _confirm "A backup already exists in this directory, do you want to delete?"
        if [ $? -eq 1 ]; then
            rm $full_path
        else
            echo "Backup canceled in the future, please choose a different backup directory or delete the old backup."
            exit 2
        fi
    fi
    tar -jcvf $full_path  ../
    echo "Backup project successfully created into: $full_path"
}


#### Private functions ####

# Clean indentation code
function _clean-indent() {
    sed -i 's/\(.*\)\(function\|class\|try\|catch\)\([^{]*\){\([^}].*\)/\1\2\3\n\1{\4/'  $(find -name '*.php')
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
