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
	DJANGO_SETTINGS_MODULE=libretime_api.settings.testing libretime-api test libretime_api
