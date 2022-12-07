.ONESHELL:

.DEFAULT_GOAL := install
SHELL := bash
CPU_CORES := $(shell N=$$(nproc); echo $$(( $$N > 4 ? 4 : $$N )))

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

# SETUPTOOLS_ENABLE_FEATURES=legacy-editable is required to work
# around https://github.com/PyCQA/pylint/issues/7306
install: $(VENV)
	source $(VENV)/bin/activate
	pip install --upgrade pip setuptools wheel
	pip install $(SHARED_DEV_REQUIREMENTS)
	[[ -z "$(PIP_INSTALL)" ]] || SETUPTOOLS_ENABLE_FEATURES=legacy-editable pip install $(PIP_INSTALL)

.PHONY: .format
.format: $(VENV)
	source $(VENV)/bin/activate
	black .
	isort . --combine-as --profile black

.PHONY: .format-check
.format-check: $(VENV)
	source $(VENV)/bin/activate
	black . --check
	isort . --combine-as --profile black --check

.PHONY: .pylint
.pylint: $(VENV)
	source $(VENV)/bin/activate
	pylint --jobs=$(CPU_CORES) --output-format=colorized --recursive=true $(PYLINT_ARG)

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
	pytest -v \
		--numprocesses=$(CPU_CORES) \
		--color=yes \
		--cov-config=pyproject.toml \
		--cov-report=term \
		--cov-report=xml:./coverage.xml \
		$(PYTEST_ARG)

.PHONY: .clean
.clean:
	rm -Rf $(VENV)

DISTRO ?= bullseye
DOCKER_RUN = docker run -it --rm \
			--user $$(id -u):$$(id -g) \
			--env HOME=/src/.docker/$(DISTRO) \
			--volume $$(pwd)/..:/src \
			--workdir /src/$(APP) \
			ghcr.io/libretime/libretime-dev:$(DISTRO)

docker-dev:
	$(MAKE) clean
	$(DOCKER_RUN) bash

docker-test:
	$(MAKE) clean
	$(DOCKER_RUN) make test
