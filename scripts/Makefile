.PHONY: lint test
.ONESHELL:

SHELL := bash
CPU_CORES := $(shell nproc)

all: lint test

venv:
	python3 -m venv venv
	source venv/bin/active
	pip install -r requirements-dev.txt

lint: venv
	pylint scripts

test: venv
	pytest -n ${CPU_CORES} --color=yes -v .
