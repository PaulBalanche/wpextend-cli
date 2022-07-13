#!/bin/bash
source include/inc/library.sh

CONFIG_FILE=$PWD/.config
test -f $CONFIG_FILE || touch $CONFIG_FILE
source $CONFIG_FILE

COMMANDS_PATH="include/commands"

if [ -f $COMMANDS_PATH/$1/index.sh ]
then
	if [ -f $COMMANDS_PATH/$1/$2.sh ]
	then
		source $COMMANDS_PATH/$1/$2.sh
	else
		source $COMMANDS_PATH/$1/index.sh
	fi
else
	wpe_cli_select
fi