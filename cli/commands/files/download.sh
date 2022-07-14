#!/bin/bash
REMOTE_SSH_HOST=$(get_config REMOTE_SSH_HOST "SSH hostname")
REMOTE_SSH_USER=$(get_config REMOTE_SSH_USER "SSH username")
SOURCE_PATH=$(read_input "Source path (remote)")
TARGET_PATH=$(read_input "Target path (local)")

if [ ! -z "$TARGET_PATH" ]
then
    TARGET_PATH="/"$TARGET_PATH
fi

if [[ ! -z "$REMOTE_SSH_USER" && ! -z "$REMOTE_SSH_HOST" && ! -z "$SOURCE_PATH" ]]
then
    rsync -avz $REMOTE_SSH_USER@$REMOTE_SSH_HOST:$SOURCE_PATH/ .$TARGET_PATH
fi