all: lint test

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := libretime tests
MYPY_ARG := libretime tests
PYTEST_ARG := --cov=libretime tests

format: .format
lint: .format-check .pylint .mypy
test: .pytest
clean: .clean
