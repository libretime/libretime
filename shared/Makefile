all: lint test

include ../tools/python.mk

PIP_INSTALL = --editable .[dev]
PYLINT_ARG = libretime_shared tests || true
MYPY_ARG = libretime_shared || true
PYTEST_ARG = --cov=libretime_shared tests

format: .format
lint: .pylint .mypy
test: .pytest
clean: .clean
