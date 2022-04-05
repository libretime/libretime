all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_liquidsoap libretime_playout || true
MYPY_ARG := libretime_liquidsoap libretime_playout || true
BANDIT_ARG := libretime_liquidsoap libretime_playout || true

format: .format
lint: .format-check .pylint .mypy .bandit
clean: .clean
