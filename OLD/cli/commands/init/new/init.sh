#!/bin/bash
echo -e "\n${FONT_BOLD}Docker setup${FONT_NORMAL}"
SERVER_DOCUMENT_ROOT="web"
source $CLI_PATH/inc/docker/setup.sh

echo -e "\n${FONT_BOLD}Bedrock init${FONT_NORMAL}"
source $COMMANDS_PATH/init/new/bedrock/init.sh

SITE_TITLE=$(read_input "Site title" "$PROJECT_NAME")
WP_ADMIN_USER=$(read_input "Wordpress admin user" "admin")
WP_ADMIN_PASSWORD=$(read_input "Wordpress admin password" "pass")
WP_ADMIN_EMAIL=$(read_input "Wordpress admin email" "paul.balanche@gmail.com")

cd docker
make composer-install
make wp-core-install SITE_TITLE=$SITE_TITLE WP_ADMIN_USER=$WP_ADMIN_USER WP_ADMIN_PASSWORD=$WP_ADMIN_PASSWORD WP_ADMIN_EMAIL=$WP_ADMIN_EMAIL
cd ..