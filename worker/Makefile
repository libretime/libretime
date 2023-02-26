all: lint test

include ../tools/python.mk

PIP_INSTALL := \
	--editable ../shared \
	--editable .[dev,sentry]
PYLINT_ARG := libretime_worker
MYPY_ARG := libretime_worker || true
BANDIT_ARG := libretime_worker

format: .format
lint: .format-check .pylint .mypy .bandit
test: .pytest
test-coverage: .coverage
clean: .clean
