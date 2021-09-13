all: lint

include ../../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := liquidsoap pypo
MYPY_ARG := liquidsoap pypo

lint: .pylint .mypy
clean: .clean
