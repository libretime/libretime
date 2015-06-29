#!/bin/bash -e
composer install

git rev-parse HEAD > VERSION

