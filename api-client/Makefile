all: lint test

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_api_client tests || true
MYPY_ARG := libretime_api_client tests || true
BANDIT_ARG := libretime_api_client || true
PYTEST_ARG := --cov=libretime_api_client tests

format: .format
lint: .format-check .pylint .mypy .bandit
test: .pytest
clean: .clean
