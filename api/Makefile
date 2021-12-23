all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_api
MYPY_ARG := libretime_api

format: .format
lint: .format-check .pylint .mypy
clean: .clean

test: $(VENV)
	source $(VENV)/bin/activate
	LIBRETIME_DEBUG=True $(VENV)/bin/libretime-api test libretime_api
