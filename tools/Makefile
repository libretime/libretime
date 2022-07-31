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

SCHEMA_FILE ?= ../config.schema.json
config-schema: $(VENV)
	source $(VENV)/bin/activate
	pip list --editable | grep playout	|| pip install --editable ../playout
	pip list --editable | grep shared	|| pip install --editable ../shared
	python3 ./generate_config_schema.py $(SCHEMA_FILE)
	if command -v npx > /dev/null; then npx prettier --write $(SCHEMA_FILE); fi
