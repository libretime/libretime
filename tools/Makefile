all: lint test

include python.mk

PIP_INSTALL =
PYLINT_ARG = tools
MYPY_ARG = .
PYTEST_ARG = .

format: .format
lint: .format-check .pylint .mypy
test: .pytest
clean: .clean
