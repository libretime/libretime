#!/bin/bash

set -xe

if [[ -n "$TRAVIS_PHP_VERSION" ]]; then
  composer install
fi
if [[ -n "$TRAVIS_PYTHON_VERSION" ]]; then
  pyenv local $TRAVIS_PYTHON_VERSION
  pushd python_apps/airtime_analyzer
  pip3 install -e .
  popd

  pushd python_apps/airtime-celery
  pip3 install -e .
  popd

  pushd python_apps/api_clients
  pip3 install -e .
  popd

  pushd python_apps/pypo
  pip3 install -e .
  popd
fi
