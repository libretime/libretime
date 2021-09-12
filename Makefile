.PHONY: setup

SHELL = bash

all: setup

setup:
	command -v pre-commit > /dev/null && pre-commit install

# https://google.github.io/styleguide/shellguide.html
shell-format:
	shfmt -f . | xargs shfmt -i 2 -ci -sr -kp -w

shell-check:
	shfmt -f . | xargs shfmt -i 2 -ci -sr -kp -d
	shfmt -f . | xargs shellcheck --color=always --severity=$${SEVERITY:-style}

VERSION:
	tools/version.sh
