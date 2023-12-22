.PHONY: setup

SHELL = bash

all: setup

setup:
	command -v pre-commit > /dev/null && pre-commit install

.env:
	cp .env.dev .env

dev-certs:
	rm -f dev/certs/fake.*
	openssl req -x509 \
		-newkey rsa:2048 \
		-days 365 \
		-nodes \
		-subj "/CN=localhost" -addext "subjectAltName=DNS:localhost,IP:127.0.0.1" \
		-keyout dev/certs/fake.key \
		-out dev/certs/fake.crt
	cat dev/certs/fake.{key,crt} > dev/certs/fake.pem

dev: .env dev-certs
	DOCKER_BUILDKIT=1 docker-compose build
	docker-compose run --rm legacy make build
	docker-compose run --rm api libretime-api migrate
	docker-compose up -d

.PHONY: VERSION
VERSION:
	tools/version.sh

.PHONY: tarball
tarball: VERSION
	$(MAKE) -C legacy build
	cd .. && tar -czf libretime-$(shell cat VERSION | tr -d [:blank:]).tar.gz \
		--owner=root --group=root \
		--exclude-vcs \
		libretime/analyzer \
		libretime/api \
		libretime/api-client \
		libretime/docs \
		libretime/installer \
		libretime/legacy \
		--exclude legacy/vendor/phing \
		--exclude legacy/vendor/simplepie/simplepie/tests \
		libretime/playout \
		libretime/shared \
		libretime/tools \
		libretime/worker \
		libretime/CHANGELOG.md \
		libretime/install \
		libretime/LICENSE \
		libretime/Makefile \
		libretime/README.md \
		libretime/SECURITY.md \
		libretime/VERSION
	mv ../libretime-*.tar.gz .
	sha256sum libretime-*.tar.gz > sha256sums.txt

# Only clean subdirs
clean:
	git clean -xdf */

docs-lint:
	vale sync
	vale docs

website:
	git clone git@github.com:libretime/website.git

website/node_modules: website
	yarn --cwd website install

docs-dev: website website/node_modules
	DOCS_PATH="../docs" yarn --cwd website start
