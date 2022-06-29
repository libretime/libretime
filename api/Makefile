all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_api
MYPY_ARG := libretime_api
BANDIT_ARG := libretime_api || true

format: .format
lint: .format-check .pylint .mypy .bandit
clean: .clean

test: $(VENV)
	source $(VENV)/bin/activate
	export DJANGO_SETTINGS_MODULE=libretime_api.settings.testing
	pytest -v \
		--numprocesses=$(CPU_CORES) \
		--color=yes \
		--cov-config=pyproject.toml \
		--cov-report=term \
		--cov-report=xml:./coverage.xml \
		--cov=libretime_api \
		libretime_api

SCHEMA_FILE ?= schema.yml
schema: $(VENV)
	source $(VENV)/bin/activate
	export DJANGO_SETTINGS_MODULE=libretime_api.settings.testing
	libretime-api spectacular --file $(SCHEMA_FILE)
	if command -v npx > /dev/null; then npx prettier --write $(SCHEMA_FILE); fi
