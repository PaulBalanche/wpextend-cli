#!/bin/bash
if ! wp core is-installed; then
    wp core install --url=$1 --title="$2" --admin_user=$3 --admin_password=$4 --admin_email=$5
fi