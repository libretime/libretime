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

vendor:
	composer install --no-progress --no-interaction $(COMPOSER_ARGS)

.PHONY: test
test: vendor
	cd tests && ../vendor/bin/phpunit

.PHONY: format
format: tools
	$(TOOLS)/bin/php-cs-fixer fix

.PHONY: lint
lint: tools
	$(TOOLS)/bin/php-cs-fixer fix --verbose --dry-run --diff
	$(TOOLS)/bin/phpstan analyse application build public tests

.PHONY: build
build:
	COMPOSER_ARGS="--no-dev --ignore-platform-reqs" $(MAKE)

propel-gen: vendor
	cd build && \
	../vendor/bin/propel-gen \
		-Dproject.home="$(shell pwd)"
