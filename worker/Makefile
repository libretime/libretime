all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := libretime
MYPY_ARG := libretime

format: .format
lint: .format-check .pylint .mypy
clean: .clean
