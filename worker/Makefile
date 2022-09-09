all: lint test

include ../tools/python.mk

PIP_INSTALL := \
	--editable ../shared \
	--editable .[dev]
PYLINT_ARG := libretime_worker || true
MYPY_ARG := libretime_worker || true
BANDIT_ARG := libretime_worker || true
PYTEST_ARG = --cov=libretime_worker tests

format: .format
lint: .format-check .pylint .mypy .bandit
test: .pytest
clean: .clean
