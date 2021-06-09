.PHONY: lint test

SHELL := bash
CPU_CORES := $(shell nproc)

MODULE_APP := api_clients
MODULE_TESTS := tests

lint:
	pylint ${MODULE_APP}
	pylint ${MODULE_TESTS}

test:
	pytest -n ${CPU_CORES} --color=yes -v --cov=${MODULE_APP} ${MODULE_TESTS}

all: lint test
