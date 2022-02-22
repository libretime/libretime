all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_worker || true
MYPY_ARG := libretime_worker || true
BANDIT_ARG := libretime_worker || true

format: .format
lint: .format-check .pylint .mypy .bandit
clean: .clean
