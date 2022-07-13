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
	@echo "\n--------------- 🎉 CONGRATS! ---------------\n\nYour website should now be up and running at:\n\n👉 $(PROJECT_HTTP_PROTOCOL)://$(PROJECT_BASE_URL):$(PROJET_PUBLIC_PORT)\n\n---------------------------------------------\n"

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

composer-install: down && up
	docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}") composer install

wp-core-install: down && up
	docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}") bash wpe-cli/inc/make/wp_core_install.sh $(PROJECT_HTTP_PROTOCOL)://$(PROJECT_BASE_URL):$(PROJET_PUBLIC_PORT) "$(SITE_TITLE)" $(WP_ADMIN_USER) $(WP_ADMIN_PASSWORD) $(WP_ADMIN_EMAIL)

remote-mysqldump: down && up
	docker exec $(shell docker ps --filter name='^/$(PROJECT_NAME)_php' --format "{{ .ID }}") mysqldump --host=$(REMOTE_DB_HOST) --user=$(REMOTE_DB_USER) --password=$(REMOTE_DB_PASSWORD) $(REMOTE_DB_NAME) --result-file=docker/mariadb-init/dump.sql

## logs	:	View containers logs.
##		You can optinally pass an argument with the service name to limit logs
##		logs php	: View `php` container logs.
##		logs nginx php	: View `nginx` and `php` containers logs.
logs:
	@docker-compose logs -f $(filter-out $@,$(MAKECMDGOALS))

# https://stackoverflow.com/a/6273809/1826109
%:
	@:
