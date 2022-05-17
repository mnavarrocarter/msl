MAIN=lib
VENDOR=mnavarro

default: setup pr

# The setup job should setup the project and leave it ready for development
setup: dc-build dc-up deps
	cat .$(VENDOR)/msg/setup.txt

dc-build:
	docker-compose build

dc-rebuild:
	docker-compose build --force-rm --no-cache

dc-up:
	docker-compose up -d --remove-orphans

deps:
	docker-compose exec $(MAIN) composer install

test:
	docker-compose exec $(MAIN) vendor/bin/phpunit --coverage-text

fmt:
	docker-compose exec $(MAIN) vendor/bin/php-cs-fixer fix

analysis:
	docker-compose exec $(MAIN) vendor/bin/psalm --stats --no-cache --show-info=true

pr: fmt analysis test
	cat .$(VENDOR)/msg/pr.txt