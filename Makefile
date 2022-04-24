# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP_CONT) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs sh composer vendor sf cc

## —— 📬 GEC Makefile —————————————————————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: up ## Start the containers

stop:
	@$(DOCKER_COMP) stop

restart: stop start

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

db-update:
	@$(PHP) bin/console doctrine:schema:update --force

load-fixtures:
	@$(PHP) bin/console hautelook:fixtures:load -n --purge-with-truncate

init-fixtures: db-update load-fixtures
cc: c=c:c ## Clear the cache
cc: sf

## —— Tests 📋 ———————————————————————————————————————————————————————————————

clean-tests: ## Clean trace codeception
	@$(PHP) vendor/bin/codecept clean

reset-db-test: ## Drop and recreate test database
	@$(PHP) bin/console --env=test doctrine:database:drop --force --if-exists
	@$(PHP) bin/console --env=test doctrine:database:create --if-not-exists
	@$(PHP) bin/console --env=test doctrine:schema:create -n

run-test-api: ## Run test api
	@$(PHP) vendor/bin/codecept run api

run-test-console:  ## Run test console
	@$(PHP) vendor/bin/codecept run console

test: clean-tests reset-db-test run-test-api run-test-console ## Recreate database and run tests

## —— PHP Unit 📝 ———————————————————————————————————————————————————————————————

run-test-unit: ## Run PHP Unit
	@$(PHP) vendor/bin/codecept run unit --quiet

## —— CodeSniffer 💇 ———————————————————————————————————————————————————————————————

run-phpcs: ## Run PHP CodeSniffer
	@$(PHP) vendor/bin/phpcs --config-set show_warnings 1
	@$(PHP) vendor/bin/phpcs --config-set colors 1
	@$(PHP) vendor/bin/phpcs --config-set php_version 80012
	@$(PHP) vendor/bin/phpcs --standard=phpcs.xml.dist --extensions=php -sp src tests

run-phpcs-files: ## Run PHP CodeSniffer
	# Example : make run-phpcs-files FILES="path/to/class/ClassController.php path/to/class/ClassTwoController.php"
	@$(PHP) vendor/bin/phpcs $(FILES)

run-phpstan:  ## Run PHP Stan
	@$(PHP) vendor/bin/phpstan analyse src
