all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := libretime liquidsoap
MYPY_ARG := libretime liquidsoap

format: .format
lint: .format-check .pylint .mypy
clean: .clean
