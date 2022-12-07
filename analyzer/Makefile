all: lint test

include ../tools/python.mk

PIP_INSTALL := \
	--editable ../shared \
	--editable .[dev]
PYLINT_ARG := libretime_analyzer tests || true
MYPY_ARG := libretime_analyzer tests || true
BANDIT_ARG := libretime_analyzer || true

format: .format
lint: .format-check .pylint .mypy .bandit

fixtures:
	bash tests/fixtures/generate.sh

test: fixtures .pytest
test-coverage: fixtures .coverage
clean: .clean
