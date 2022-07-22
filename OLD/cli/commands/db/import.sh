#!/bin/bash
if [ -z "$SQL_FILENAME" ]; then SQL_FILENAME=$(read_input "SQL filename (for example docker/mariadb-init/dump.sql)"); fi

echo $SQL_FILENAME" importing..."

if [[ ! -z "$SQL_FILENAME" && -f $SQL_FILENAME ]]
then
    cd docker
    make mysql-import SQL_FILE=$SQL_FILENAME
    cd ..
fi