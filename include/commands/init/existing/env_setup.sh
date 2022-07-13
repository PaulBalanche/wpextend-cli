#!/bin/bash

# Database
read -p "Do you want to download a remote database? (y/n) " yn
case $yn in
    [yY] )
        source $COMMANDS_PATH/db/download.sh
        ;;
    [nN] )
        read -p "Do you have a database SQL dump file in the project? (y/n) " yn
        case $yn in
            [yY] )
                DB_LOCAL_LOCATION=$(read_input "SQL dump file location?")
                mv $DB_LOCAL_LOCATION docker/mariadb-init/dump.sql;;
        esac;;
    * ) echo invalid response;
        exit 1;;
esac

# Repository
read -p "Do you want to pull project from a GIT repository? (y/n) " yn
case $yn in
    [yY] )
        GIT_REPOSITORY_URL=$(read_input "Git repository URL")
        if [ ! -z "$GIT_REPOSITORY_URL" ]
        then
            git clone $GIT_REPOSITORY_URL repo-$PROJECT_NAME
            mv repo-$PROJECT_NAME/* .
            mv repo-$PROJECT_NAME/.gitignore .gitignore
            rm -r repo-$PROJECT_NAME
        fi
        ;;
esac

if [ -z "$GIT_REPOSITORY_URL" ]
then
    # Project files
    read -p "Do you want to download files project through SSH? (y/n) " yn
    case $yn in
        [yY] )
            source $COMMANDS_PATH/files/download.sh
            ;;
    esac
fi

# Composer
if [ -f composer.json ]
then
    read -p "We found composer.json file. Do you want to install depedencies ? (y/n) " yn
    case $yn in
        [yY] )
            cd docker
            make composer-install
            cd ..
            ;;
    esac
fi