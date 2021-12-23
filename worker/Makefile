all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := libretime_worker
MYPY_ARG := libretime_worker

format: .format
lint: .format-check .pylint .mypy
clean: .clean
