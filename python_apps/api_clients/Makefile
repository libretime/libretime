all: lint test

include ../../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := api_clients tests
MYPY_ARG := api_clients tests
PYTEST_ARG := --cov=api_clients tests

format: .format
lint: .pylint .mypy
test: .pytest
clean: .clean
