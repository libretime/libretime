all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_api || true
MYPY_ARG := libretime_api || true
BANDIT_ARG := libretime_api || true

format: .format
lint: .format-check .pylint .mypy .bandit
clean: .clean

test: $(VENV)
	source $(VENV)/bin/activate
	LIBRETIME_DEBUG=True $(VENV)/bin/libretime-api test libretime_api
