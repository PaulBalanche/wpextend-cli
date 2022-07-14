#!/bin/bash
CLI_PATH=$WPE_ROOT_PATH"/cli"
CONFIG_FILE=$PWD/.config
FONT_BOLD=$(tput bold)
FONT_ITALIC=$(tput sitm)
FONT_NORMAL=$(tput sgr0)
COMMANDS_PATH=$CLI_PATH"/commands"

source $CLI_PATH/inc/library.sh
test -f $CONFIG_FILE || touch $CONFIG_FILE
source $CONFIG_FILE

echo -e "\nWelcome! 🍻\nDon't forget, WordPress is your best friend ❤️";

# Ensure docker is present
if [ ! -d $PWD/docker ]
then
	echo ''
	read -p "${FONT_BOLD}-- Your project does not have yet Docker instance. Add it? (y/n) ${FONT_NORMAL}" yn
	case $yn in
		[yY] )
			source $CLI_PATH/inc/docker/init.sh
			;;
		* ) echo "Sorry but for now WP Extend CLI needs its own docker instance to work..."
			exit 1;;
	esac
fi

# Ensure docker env is setup
if [ ! -f $PWD/docker/.env ]
then
	echo -e "\nLocal environnement is not yet configured.\n${FONT_BOLD}-- What do you want to do?${FONT_NORMAL}"

	select choose in "Run existing project" "Init Bedrock (new WP from scratch)"
	do
		case $choose in

			"Run existing project" )
				source $COMMANDS_PATH/init/existing/init.sh
				break
				;;

			"Init Bedrock (new WP from scratch)" )
				source $COMMANDS_PATH/init/new/init.sh
				break
				;;
		esac
	done
else
	# Normal choose
	echo -e "${FONT_BOLD}-- What do you want to do?\n${FONT_NORMAL}"
	select choose in "Download & import remote database" "Import remote database" "Download remote files" "Remove docker instance"
	do
		case $choose in

			"Download & import remote database" )
				source $COMMANDS_PATH/db/import.sh
				break
				;;

			"Download remote database" )
				source $COMMANDS_PATH/db/download.sh
				break
				;;

			"Download remote files" )
				source $COMMANDS_PATH/files/download.sh
				break
				;;

			"Remove docker instance" )
				source $CLI_PATH/inc/docker/remove.sh
				break
				;;
		esac
	done
fi

# Composer
if [ -f composer.json ] && [ ! -d vendor ]
then
	echo -e "\nWe found composer.json file.\n"
    read -p "${FONT_BOLD}-- Do you want to install depedencies ? (y/n) ${FONT_NORMAL}" yn
    case $yn in
        [yY] )
            cd docker
            make composer-install
            cd ..
            ;;
    esac
fi