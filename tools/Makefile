all: lint test

include python.mk

PIP_INSTALL := -r requirements-dev.txt
PYLINT_ARG := tools
MYPY_ARG := .
PYTEST_ARG := .

lint: .pylint .mypy
test: .pytest
clean: .clean
