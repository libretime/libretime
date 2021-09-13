all: lint

include ../../tools/python.mk

PIP_INSTALL := --editable .[dev]
PYLINT_ARG := airtime-celery
MYPY_ARG := airtime-celery

lint: .pylint .mypy
clean: .clean
