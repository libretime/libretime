all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := libretimeapi
MYPY_ARG := libretimeapi

format: .format
lint: .pylint .mypy
clean: .clean
