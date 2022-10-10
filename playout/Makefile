all: lint

include ../tools/python.mk

APP := playout
PIP_INSTALL := \
	--editable ../api-client \
	--editable ../shared \
	--editable .[dev]
PYLINT_ARG := libretime_playout tests || true
MYPY_ARG := libretime_playout tests || true
BANDIT_ARG := libretime_playout || true
PYTEST_ARG := --cov=libretime_playout tests

format: .format
lint: .format-check .pylint .mypy .bandit
test: .pytest
clean: .clean

.PHONY: snapshot
snapshot: $(VENV)
	source $(VENV)/bin/activate
	pytest --snapshot-update
