MAIN = lib
VENDOR = mnavarro
COMPOSE_CMD ?= docker-compose

default: setup pr

# The setup job should setup the project and leave it ready for development
setup: dc-build dc-up deps
	cat .$(VENDOR)/msg/setup.txt

dc-build:
	$(COMPOSE_CMD) build

dc-rebuild:
	$(COMPOSE_CMD) build --force-rm --no-cache

dc-up:
	$(COMPOSE_CMD) up -d --remove-orphans

deps:
	$(COMPOSE_CMD) exec $(MAIN) composer install

test:
	$(COMPOSE_CMD) exec $(MAIN) vendor/bin/phpunit --coverage-text

fmt:
	$(COMPOSE_CMD) exec $(MAIN) vendor/bin/php-cs-fixer fix

analysis:
	$(COMPOSE_CMD) exec $(MAIN) vendor/bin/psalm --stats --no-cache --show-info=true

pr: fmt analysis test
	cat .$(VENDOR)/msg/pr.txt