all: lint test

include ../tools/python.mk

PIP_INSTALL := \
	--editable ../shared \
	--editable .[dev]
PYLINT_ARG := libretime_api_client tests || true
MYPY_ARG := libretime_api_client tests || true
BANDIT_ARG := libretime_api_client || true

format: .format
lint: .format-check .pylint .mypy .bandit
test: .pytest
test-coverage: .coverage
clean: .clean
