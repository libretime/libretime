#!/bin/bash

set -xe

if [[ -n "$TRAVIS_PHP_VERSION" ]]; then
  pushd airtime_mvc/tests
  ../../vendor/bin/phpunit
  popd
fi
if [[ -n "$TRAVIS_PYTHON_VERSION" ]]; then
  pyenv local $TRAVIS_PYTHON_VERSION
  pushd python_apps/airtime_analyzer
  nosetests
  popd

  pushd python_apps/api_clients
  nosetests
  popd
fi
