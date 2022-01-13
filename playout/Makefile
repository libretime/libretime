all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretime_liquidsoap libretime_playout
MYPY_ARG := libretime_liquidsoap libretime_playout

format: .format
lint: .format-check .pylint .mypy
clean: .clean
