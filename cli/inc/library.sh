#!/bin/bash
get_config () {

    if ( ! grep -q $1 $CONFIG_FILE )
    then

        if [ ! -z "$3" ]
        then
            read -p "$2 [$3]: " VALUE
            VALUE=${VALUE:-$3}
        else
            read -p "$2 : " VALUE
        fi

        if [ ! -z "$VALUE" ]
        then
            echo $1'="'$VALUE'"' >> $CONFIG_FILE
        fi
        
    else
        VALUE=$(printf '%s\n' "${!1}")
	fi

    echo $VALUE
}

read_input () {

    if [ ! -z "$2" ]
    then
        read -p "$1 [$2]: " VALUE
        VALUE=${VALUE:-$2}
    else
        read -p "$1 : " VALUE
    fi

    echo $VALUE
}

get_secret () {

	read -p "$2 [$3]: " VALUE
    VALUE=${VALUE:-$3}

    echo $VALUE
}

wpe_cli_help() {
  cli_name=${0##*/}
  echo "
WPExtend CLI
Version: $(cat $(cd $(dirname $0) && pwd)/wpe-cli/VERSION)
Usage: $cli_name [command]
Commands:
  init          Init
  db_import     Import remote database
  get_uploads   Download remote uploads
  *             Help
"
  exit 1
}

wpe_cli_select() {
    echo -e "\n\033[1m🍻 Hey! What do you want to do?\033[0m\n"

    select choose in "Download remote database" "Download remote files" "Init docker in existing project" "Init new Bedrock instance (from scratch)"
    do
            case $choose in

                "Download remote database" )
                    source $COMMANDS_PATH/db/download.sh
                    ;;

                "Download remote files" )
                    source $COMMANDS_PATH/files/download.sh
                    ;;

                "Init docker in existing project" )
                    source $COMMANDS_PATH/init/existing/init.sh
                    ;;

                "Init new Bedrock instance (from scratch)" )
                    source $COMMANDS_PATH/init/new/init.sh
                    ;;
            esac
    done


    
}