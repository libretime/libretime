all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretimeapi
MYPY_ARG := libretimeapi

format: .format
lint: .format-check .pylint .mypy
clean: .clean

test: $(VENV)
	source $(VENV)/bin/activate
	LIBRETIME_DEBUG=True $(VENV)/bin/libretime-api test libretimeapi
