all: lint test

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := libretime_api_client tests || true
MYPY_ARG := libretime_api_client tests || true
PYTEST_ARG := --cov=libretime_api_client tests

format: .format
lint: .format-check .pylint .mypy
test: .pytest
clean: .clean
