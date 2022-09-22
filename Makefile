.PHONY: setup

SHELL = bash

all: setup

setup:
	command -v pre-commit > /dev/null && pre-commit install

# https://google.github.io/styleguide/shellguide.html
shell-format:
	shfmt -f . | xargs git ls-files | xargs shfmt -i 2 -ci -sr -kp -w

shell-check:
	shfmt -f . | xargs git ls-files | xargs shfmt -i 2 -ci -sr -kp -d
	shfmt -f . | xargs git ls-files | xargs shellcheck --color=always --severity=$${SEVERITY:-style}

.PHONY: VERSION
VERSION:
	tools/version.sh

changelog:
	tools/changelog.sh

.PHONY: tarball
tarball: VERSION
	$(MAKE) -C legacy build
	cd .. && tar -czf libretime-$(shell cat VERSION | tr -d [:blank:]).tar.gz \
		--owner=root --group=root \
		--exclude-vcs \
		--exclude .codespellignore \
		--exclude .git* \
		--exclude .pre-commit-config.yaml \
		--exclude dev_tools \
		--exclude jekyll.sh \
		--exclude legacy/vendor/phing \
		--exclude legacy/vendor/simplepie/simplepie/tests \
		libretime
	mv ../libretime-*.tar.gz .

# Only clean subdirs
clean:
	git clean -xdf */

docs-lint:
	vale sync
	vale docs website/src/pages
