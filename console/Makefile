all: lint test

include ../tools/python.mk

PIP_INSTALL = --editable .
PYLINT_ARG = libretime_console
MYPY_ARG = libretime_console
PYTEST_ARG = --cov=libretime_console

format: .format
lint: .pylint .mypy
test: .pytest
clean: .clean
