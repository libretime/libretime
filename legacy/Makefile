SHELL = bash

all: vendor locale-build

TOOLS =  tools/vendor
tools: $(TOOLS)
$(TOOLS):
	composer --working-dir=tools install

locale-update:
	$(MAKE) -C locale update
locale-build:
	$(MAKE) -C locale clean build

export COMPOSER_ALLOW_SUPERUSER = true
COMPOSER_INSTALL = composer install \
	--no-interaction \
	--no-progress \
	--no-plugins \
	--no-scripts \
	--optimize-autoloader

vendor:
	$(COMPOSER_INSTALL)

.PHONY: test
test: vendor
	cd tests && ../vendor/bin/phpunit

.PHONY: format
format: tools
	$(TOOLS)/bin/php-cs-fixer fix

.PHONY: lint
lint: tools
	$(TOOLS)/bin/php-cs-fixer fix --verbose --dry-run --diff

.PHONY: build
build: locale-build
	$(COMPOSER_INSTALL) --no-dev --ignore-platform-reqs

propel-gen: vendor
	cd build && \
	../vendor/bin/propel-gen \
		-Dproject.home="$(shell pwd)"
