all: lint test

include ../../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := api_clients tests
MYPY_ARG := api_clients tests
PYTEST_ARG := --cov=api_clients tests

lint: .pylint .mypy
test: .pytest
clean: .clean
