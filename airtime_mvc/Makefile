.PHONY: build

SHELL = bash

vendor:
	composer install --no-progress --no-interaction

test: vendor
	cd tests && ../vendor/bin/phpunit

build:
	composer install --no-progress --no-interaction --no-dev
