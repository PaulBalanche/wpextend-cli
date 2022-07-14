#!/bin/bash

# Repository
echo '';
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

# Project files
echo '';
read -p "Do you want to download files through SSH (for example \"wp-content\" directory) ? (y/n) " yn
case $yn in
    [yY] )
        source $COMMANDS_PATH/files/download.sh
        ;;
esac

echo -e "\n${FONT_BOLD}Docker setup${FONT_NORMAL}"
SERVER_DOCUMENT_ROOT=$(get_config "Server document root (leave empty if root)")
source $CLI_PATH/inc/docker/setup.sh

# Database
echo '';
read -p "Do you want to download a remote database? (y/n) " yn
case $yn in
    [yY] )
        source $COMMANDS_PATH/db/import.sh
        ;;
    [nN] )
        echo '';
        read -p "Do you have a database SQL dump file in the project? (y/n) " yn
        case $yn in
            [yY] )
                DB_LOCAL_LOCATION=$(read_input "\nSQL dump file location?")
                mv $DB_LOCAL_LOCATION docker/mariadb-init/dump.sql;;
        esac;;
    * ) echo invalid response;
        exit 1;;
esac

cd docker
make
cd ..