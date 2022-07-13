#!/bin/bash
composer create-project roots/bedrock --no-install
mv bedrock/* .
mv bedrock/.gitignore .gitignore
rm -r bedrock