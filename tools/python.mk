.ONESHELL:

SHELL = bash
CPU_CORES = $(shell nproc)

# PIP_INSTALL = --editable .
# PYLINT_ARG =
# MYPY_ARG =
# PYTEST_ARG =

SHARED_DEV_REQUIREMENTS = \
	black \
	isort \
	mypy \
	pylint \
	pytest \
	pytest-cov \
	pytest-xdist

VENV = venv
$(VENV):
	python3 -m venv $(VENV)
	source $(VENV)/bin/activate
	$(MAKE) install

install: venv
	source $(VENV)/bin/activate
	pip install --upgrade pip setuptools wheel
	pip install $(SHARED_DEV_REQUIREMENTS) $(PIP_INSTALL)

.PHONY: .format
.format: $(VENV)
	source $(VENV)/bin/activate
	black .
	isort --profile black .

.PHONY: .pylint
.pylint: $(VENV)
	source $(VENV)/bin/activate
	pylint --output-format=colorized $(PYLINT_ARG) || true

.PHONY: .mypy
.mypy: $(VENV)
	source $(VENV)/bin/activate
	mypy $(MYPY_ARG) || true

.PHONY: .pytest
.pytest: $(VENV)
	source venv/bin/activate
	pytest -n $(CPU_CORES) --color=yes -v $(PYTEST_ARG)

.PHONY: .clean
.clean:
	rm -Rf $(VENV)
