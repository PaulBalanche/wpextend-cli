#!/bin/bash
echo ''
echo ''
echo ''
echo '#################################### Docker setup ####################################'
echo ''
SERVER_DOCUMENT_ROOT="web"
source $COMMANDS_PATH/init/docker/init.sh

echo ''
echo '#################################### Bedrock init ####################################'
echo ''
source $COMMANDS_PATH/init/new/bedrock/init.sh

echo ''
echo ''
echo ''
echo '#################################### Env setup ####################################'
echo ''
source $COMMANDS_PATH/init/new/bedrock/env_setup.sh

SITE_TITLE=$(read_input "Site title" "$PROJECT_NAME")
WP_ADMIN_USER=$(read_input "Wordpress admin user" "admin")
WP_ADMIN_PASSWORD=$(read_input "Wordpress admin password" "pass")
WP_ADMIN_EMAIL=$(read_input "Wordpress admin email" "paul.balanche@gmail.com")

cd docker
make composer-install
make wp-core-install SITE_TITLE=$SITE_TITLE WP_ADMIN_USER=$WP_ADMIN_USER WP_ADMIN_PASSWORD=$WP_ADMIN_PASSWORD WP_ADMIN_EMAIL=$WP_ADMIN_EMAIL
cd ..