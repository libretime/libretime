.PHONY: build php-cs-fixer

SHELL = bash

vendor:
	composer install --no-progress --no-interaction

test: vendor
	cd tests && ../vendor/bin/phpunit

build:
	composer install --no-progress --no-interaction --no-dev

tools/php-cs-fixer:
	mkdir -p tools/php-cs-fixer
	composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer

php-cs-fixer: tools/php-cs-fixer
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix
