.PHONY: lint test

SHELL := bash
CPU_CORES := $(shell nproc)

MODULE_APP := airtime_analyzer
MODULE_TESTS := tests

lint:
	pylint ${MODULE_APP}
	pylint ${MODULE_TESTS}

fixtures:
	bash tests/fixtures/generate.sh

test: fixtures
	pytest -n ${CPU_CORES} --color=yes -v --cov=${MODULE_APP} ${MODULE_TESTS}

all: lint test
