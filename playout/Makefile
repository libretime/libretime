all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_liquidsoap libretime_playout tests || true
MYPY_ARG := libretime_liquidsoap libretime_playout tests || true
BANDIT_ARG := libretime_liquidsoap libretime_playout || true
PYTEST_ARG := --cov=libretime_liquidsoap --cov=libretime_playout tests

format: .format
lint: .format-check .pylint .mypy .bandit
test: .pytest
clean: .clean
