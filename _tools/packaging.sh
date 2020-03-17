#!/bin/bash

##
# Title:           Packaging Automation Tool
# Description:     It is used to clean up the script before distribution to customers (e.g. before uploading it to SourceForge).
#                  To work correctly, you have to execute this script when you're in the project root with your terminal (generally the parent folder of "_tools/").
#                  (e.g., you@you:/path/to/root-project$ bash _tools/pH7.sh).
#
# Author:          Pierre-Henry Soria <hello@ph7cms.com>
# Copyright:       (c) 2014-2020, Pierre-Henry Soria. All Rights Reserved.
# License:         GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
##

function run() {
    _confirm "Are you sure you want to run the PACKAGING process (Irreversible Action)?"
    if [ $? -eq 1 ]; then
        _confirm "Have you made a copy of it before?"
        if [ $? -eq 1 ]; then

            ## TMP files
            find . -type f \( -name '*~' -or -name '*.log' -or -name '*.tmp' -or -name '*.swp' -or -name '.directory' -or -name '._*' -or -name '.DS_Store*' -or -name 'Thumbs.db' \) -exec rm {} \;

            ## Cleaning the code
            params="-name '*.php' -or -name '*.css' -or -name '*.js' -or -name '*.html' -or -name '*.xml' -or -name '*.xsl' -or -name '*.xslt' -or -name '*.svg' -or -name '*.json' -or -name '*.yml' -or -name '*.tpl' -or -name '*.phs' -or -name '*.ph7' -or -name '*.sh' -or -name '*.sql' -or -name '*.ini' -or -name '*.md' -or -name '*.markdown' -or -name '.htaccess'"
            exec="find . -type f \( $params \) -print0 | xargs -0 perl -wi -pe"
            eval "$exec 's/\s+$/\n/'"
            eval "$exec 's/\t/    /g'"

            # Update Composer itself to the latest version
            php ./composer.phar self-update

            # Install dependencies for production only (without dev packages)
            php ./composer.phar install --no-dev

            # Update the libraries to their latest versions
            # php ./composer.phar update --no-dev

            # Optimize Composer
            php ./composer.phar dump-autoload --optimize --no-dev

            ## Caches
            # public
            rm -rf ./_install/data/caches/smarty_compile/*
            rm -rf ./_install/data/caches/smarty_cache/*
            # _protected
            rm -rf ./_protected/data/cache/pH7tpl_compile/*
            rm -rf ./_protected/data/cache/pH7tpl_cache/*
            rm -rf ./_protected/data/cache/pH7_static/*
            rm -rf ./_protected/data/cache/pH7_cache/*
            rm -rf ./_protected/data/backup/file/*
            rm -rf ./_protected/data/backup/sql/*
            rm ./_protected/data/tmp/*.txt

            ## Config Files, etc.
            rm ./_constants.php
            rm ./.gitignore
            rm ./.gitattributes
            rm ./.scrutinizer.yml
            rm ./.travis.yml
            rm ./composer.lock
            rm ./composer.phar
            rm ./phpunit.phar
            rm ./phpunit.xml.dist
            rm ./_protected/app/configs/config.ini
            rm ./nginx.conf
            rm -rf ./.github/
            rm -rf ./coverage/ # PHPUnit coverage reports
            rm -rf ./.idea/ # PHPStorm

            ## Others
            rm -f ./_protected/app/system/core/assets/cron/_delay/*
            rm -rf ./_repository/import/*
            rm -rf ./_repository/module/*
            rm -rf ./_tests/
            rm -rf ./.git/

            ## PHPCS
            rm ./phpcs.xml.dist
            rm ./.php_cs
            rm ./.php_cs.cache
            rm ./.php_cs.dist

            ## Docker
            rm ./Dockerfile
            rm ./docker-compose.yml
            rm ./.dockerignore

            ## TMP folders
            # elFinder cache folders
            rm -rf ./.quarantine/
            rm -rf ./.tmb/
            rm -rf ./_protected/.quarantine/
            rm -rf ./_protected/.tmb/
            # Composer cache folder
            rm -rf ./_protected/vendor/cache/

            ## Permissions
            # Generic for folders/files
            find . -type f -print0 | sudo xargs -0 chmod 666 # for files
            find . -type d -print0 | sudo xargs -0 chmod 755 # for folders

            # Specific ones
            sudo chmod -R 777 ./_install/data/logs/
            sudo chmod -R 777 ./_install/data/caches/
            sudo chmod -R 777 ./data/system/modules/*
            sudo chmod -R 777 ./_repository/module/*
            sudo chmod -R 777 ./_repository/upgrade/*
            sudo chmod -R 777 ./_protected/app/configs/*
            sudo chmod -R 777 ./_protected/data/cache/*
            sudo chmod -R 777 ./_protected/data/backup/*
            sudo chmod -R 777 ./_protected/data/tmp/*
            sudo chmod -R 777 ./_protected/data/log/*

            echo "Done!"
            echo "Remove \"_tools/\" folder (containing this file) before packaging pH7Builder"
        else
            echo "You must backup the software before running the packaging script. Go back!"
            exit 1
        fi
    fi
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

run
