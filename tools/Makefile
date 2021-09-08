.PHONY: lint test
.ONESHELL:

SHELL := bash
CPU_CORES := $(shell nproc)

all: lint test

venv:
	python3 -m venv venv
	source venv/bin/activate
	pip install -r requirements-dev.txt

lint: venv
	source venv/bin/activate
	pylint tools

test: venv
	source venv/bin/activate
	pytest -n ${CPU_CORES} --color=yes -v .

clean:
	rm -Rf venv
