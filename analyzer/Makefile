all: lint test

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime tests
MYPY_ARG := libretime tests
PYTEST_ARG := --cov=libretime tests

format: .format
lint: .format-check .pylint .mypy

fixtures:
	bash tests/fixtures/generate.sh

test: fixtures .pytest
clean: .clean
