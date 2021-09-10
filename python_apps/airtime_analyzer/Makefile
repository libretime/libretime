all: lint test

include ../../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := airtime_analyzer tests
PYTEST_ARG := --cov=airtime_analyzer tests

lint: .pylint

fixtures:
	bash tests/fixtures/generate.sh

test: fixtures .pytest
clean: .clean
