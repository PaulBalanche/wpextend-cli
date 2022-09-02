include .env

.PHONY: up down stop prune ps shell wp logs mutagen

default: up

WP_ROOT ?= /var/www/html/

## help	:	Print commands help.
help : docker.mk
	@sed -n 's/^##//p' $<

## up	:	Start up containers.
up:
	@echo "Starting up containers for $(PROJECT_NAME)..."
	docker-compose pull
	docker-compose up -d --remove-orphans
	@echo "\n\n\033[1;32m	Your website is up and running at 👉 $(PROJECT_HTTP_PROTOCOL)://$(PROJECT_BASE_URL):$(PROJET_PUBLIC_PORT)\033[0m\n"

up-quiet:
	docker-compose pull
	docker-compose up -d --remove-orphans

mutagen:
	mutagen-compose up

## down	:	Stop containers.
down: stop

## start	:	Start containers without updating.
start:
	@echo "Starting containers for $(PROJECT_NAME) from where you left off..."
	@docker-compose start

## stop	:	Stop containers.
stop:
	@echo "Stopping containers for $(PROJECT_NAME)..."
	@docker-compose stop

## prune	:	Remove containers and their volumes.
##		You can optionally pass an argument with the service name to prune single container
##		prune mariadb	: Prune `mariadb` container and remove its volumes.
##		prune mariadb solr	: Prune `mariadb` and `solr` containers and remove their volumes.
prune:
	@echo "Removing containers for $(PROJECT_NAME)..."
	@docker-compose down -v $(filter-out $@,$(MAKECMDGOALS))

## ps	:	List running containers.
ps:
	@docker ps --filter name='$(PROJECT_NAME)*'

## shell	:	Access `php` container via shell.
##		You can optionally pass an argument with a service name to open a shell on the specified container
shell:
	docker exec -ti -e COLUMNS=$(shell tput cols) -e LINES=$(shell tput lines) $(shell docker ps --filter name='$(PROJECT_NAME)_$(or $(filter-out $@,$(MAKECMDGOALS)), 'php')' --format "{{ .ID }}") sh

## wp	:	Executes `wp cli` command in a specified `WP_ROOT` directory (default is `/var/www/html/`).
## 		Doesn't support --flag arguments.
wp:
	docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}") wp --path=$(WP_ROOT) $(filter-out $@,$(MAKECMDGOALS))

composer-install:
	@[ "$(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}")" ] || ( docker-compose pull && docker-compose up -d --remove-orphans )
	docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}") composer install

wp-core-install:
	@[ "$(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}")" ] || ( docker-compose pull && docker-compose up -d --remove-orphans )
	docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}") bash docker/make/wp_core_install.sh $(PROJECT_HTTP_PROTOCOL)://$(PROJECT_BASE_URL):$(PROJET_PUBLIC_PORT) "$(SITE_TITLE)" $(WP_ADMIN_USER) $(WP_ADMIN_PASSWORD) $(WP_ADMIN_EMAIL)

remote-mysqldump:
	docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}") sh docker/make/mysqldump.sh "$(REMOTE_DB_HOST)" "$(REMOTE_DB_USER)" "$(REMOTE_DB_PASSWORD)" "$(REMOTE_DB_NAME)" "$(SQL_FILE)"

mysql-import:
	docker exec -i $(shell docker ps --filter name='^/$(PROJECT_NAME)_mariadb' --format "{{ .ID }}") sh -c 'exec mysql -uroot -p"$(DB_ROOT_PASSWORD)" $(DB_NAME)' < $(filter-out $@,$(MAKECMDGOALS))

php-up:
	@docker-compose up -d --remove-orphans php

database-up:
	@docker-compose up -d --remove-orphans mariadb

database-healthcheck:
	@docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_mariadb' --format "{{ .ID }}") mysqlshow -uroot -p"$(DB_ROOT_PASSWORD)" $(DB_NAME)

quiet-logs:
	@docker-compose logs $(filter-out $@,$(MAKECMDGOALS))
## logs	:	View containers logs.
##		You can optinally pass an argument with the service name to limit logs
##		logs php	: View `php` container logs.
##		logs nginx php	: View `nginx` and `php` containers logs.
logs:
	@docker-compose logs -f $(filter-out $@,$(MAKECMDGOALS))

# https://stackoverflow.com/a/6273809/1826109
%:
	@:
