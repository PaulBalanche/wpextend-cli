#!/bin/bash
read -p "Do you want to install Wordpress from scratch (empty Bedrock)? Or you already have an existing project (local or remote)... (y/n) " yn
case $yn in
    [yY] )
        source $COMMANDS_PATH/init/new/init.sh
        ;;
    [nN] )
        source $COMMANDS_PATH/init/existing/init.sh
        ;;
    * ) echo invalid response;
        exit 1;;
esac