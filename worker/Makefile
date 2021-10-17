all: lint

include ../tools/python.mk

PIP_INSTALL := --editable .
PYLINT_ARG := airtime-celery
MYPY_ARG := airtime-celery

format: .format
lint: .format-check .pylint .mypy
clean: .clean
