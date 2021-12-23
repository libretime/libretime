all: lint test

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := libretime_api_client tests
MYPY_ARG := libretime_api_client tests
PYTEST_ARG := --cov=libretime_api_client tests

format: .format
lint: .format-check .pylint .mypy
test: .pytest
clean: .clean
