.ONESHELL:

.DEFAULT_GOAL = install
SHELL = bash
CPU_CORES = $(shell nproc)

# PIP_INSTALL = --editable .
# PYLINT_ARG =
# MYPY_ARG =
# BANDIT_ARG =
# PYTEST_ARG =

SHARED_DEV_REQUIREMENTS = \
	bandit \
	black \
	flake8 \
	isort \
	mypy \
	pylint \
	pytest \
	pytest-cov \
	pytest-xdist

VENV = .venv
$(VENV):
	python3 -m venv $(VENV)
	source $(VENV)/bin/activate
	$(MAKE) install

install: $(VENV)
	source $(VENV)/bin/activate
	pip install --upgrade pip setuptools wheel
	pip install $(SHARED_DEV_REQUIREMENTS)
	[[ -z "$(PIP_INSTALL)" ]] || pip install $(PIP_INSTALL)

.PHONY: .format
.format: $(VENV)
	source $(VENV)/bin/activate
	black .
	isort . --profile black

.PHONY: .format-check
.format-check: $(VENV)
	source $(VENV)/bin/activate
	black . --check
	isort . --profile black --check

.PHONY: .pylint
.pylint: $(VENV)
	source $(VENV)/bin/activate
	pylint --jobs=$(CPU_CORES) --output-format=colorized $(PYLINT_ARG)

.PHONY: .mypy
.mypy: $(VENV)
	source $(VENV)/bin/activate
	mypy $(MYPY_ARG)

.PHONY: .bandit
.bandit: $(VENV)
	source $(VENV)/bin/activate
	bandit -r $(BANDIT_ARG)

.PHONY: .pytest
.pytest: $(VENV)
	source $(VENV)/bin/activate
	pytest -n $(CPU_CORES) --color=yes -v $(PYTEST_ARG)

.PHONY: .clean
.clean:
	rm -Rf $(VENV)
