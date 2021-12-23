all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := libretime_liquidsoap pypo
MYPY_ARG := libretime_liquidsoap pypo

format: .format
lint: .format-check .pylint .mypy
clean: .clean
