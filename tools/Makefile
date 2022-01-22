all: lint test

include python.mk

PIP_INSTALL =
PYLINT_ARG = tools || true
MYPY_ARG = . || true
PYTEST_ARG = .

format: .format
lint: .format-check .pylint .mypy
test: .pytest
clean: .clean
