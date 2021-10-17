SHELL = bash

all: vendor locale-build

PHP-CS-FIXER = tools/vendor/bin/php-cs-fixer
$(PHP-CS-FIXER):
	mkdir -p tools
	composer require --working-dir=tools friendsofphp/php-cs-fixer

locale-update:
	$(MAKE) -C locale update
locale-build:
	$(MAKE) -C locale clean build

vendor:
	composer install --no-progress --no-interaction $(COMPOSER_ARGS)

.PHONY: test
test: vendor
	cd tests && ../vendor/bin/phpunit

.PHONY: format
format: $(PHP-CS-FIXER)
	$(PHP-CS-FIXER) fix

.PHONY: lint
lint: $(PHP-CS-FIXER)
	$(PHP-CS-FIXER) fix --dry-run --diff

.PHONY: build
build:
	COMPOSER_ARGS="--no-dev --ignore-platform-reqs" $(MAKE)

propel-gen: vendor
	cd build && \
	../vendor/bin/propel-gen \
		-Dproject.home="$(shell pwd)"
