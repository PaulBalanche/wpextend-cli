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

    if [ ! -z "$3" ]
    then
        read -s -p "$2 [$3]: " SECRET
        SECRET=${SECRET:-$3}
    else
        read -s -p "$2 : " SECRET
    fi

    echo $SECRET
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