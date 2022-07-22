#!/bin/bash
echo ''
echo -e "${FONT_BOLD}-- What do you want to do?\n${FONT_NORMAL}"
select choose in "Download remote database only" "Download and import remote database" "Import local database"
do
    case $choose in

        "Download remote database only" )
            source $COMMANDS_PATH/db/download.sh
            break
            ;;

        "Download and import remote database" )
            source $COMMANDS_PATH/db/download.sh
            source $COMMANDS_PATH/db/import.sh
            break
            ;;

        "Import local database" )
            source $COMMANDS_PATH/db/import.sh
            break
            ;;
    esac
done