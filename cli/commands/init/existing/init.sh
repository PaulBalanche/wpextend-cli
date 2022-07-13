#!/bin/bash
echo ''
echo ''
echo ''
echo '#################################### Docker setup ####################################'
echo ''
source $COMMANDS_PATH/init/docker/init.sh

echo ''
echo ''
echo ''
echo '#################################### Env setup ####################################'
echo ''
source $COMMANDS_PATH/init/existing/env_setup.sh

cd docker
make
cd ..