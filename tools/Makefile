all: lint test

include python.mk

PIP_INSTALL := -r requirements-dev.txt
PYLINT_ARG := tools
PYTEST_ARG := .

lint: .pylint
test: .pytest
clean: .clean
