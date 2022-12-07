all: lint

include ../tools/python.mk

PIP_INSTALL := \
	--editable ../shared \
	--editable .[dev]
PYLINT_ARG := libretime_api
MYPY_ARG := libretime_api
BANDIT_ARG := --exclude '*/tests/*' libretime_api || true

export DJANGO_SETTINGS_MODULE=libretime_api.settings.testing

format: .format
lint: .format-check .pylint .mypy .bandit
clean: .clean

test: $(VENV)
	$(VENV)/bin/pytest -v \
		--numprocesses=$(CPU_CORES) \
		--color=yes \
		--cov-config=pyproject.toml \
		--cov-report=term \
		--cov-report=xml:./coverage.xml \
		--cov=libretime_api \
		libretime_api

SCHEMA_FILE ?= schema.yml
schema: $(VENV)
	$(VENV)/bin/libretime-api spectacular --file $(SCHEMA_FILE)
	if command -v npx > /dev/null; then npx prettier --write $(SCHEMA_FILE); fi

schema-foreach-commit:
	git rebase main -x "$(MAKE) -C api schema"
