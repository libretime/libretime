#!/usr/bin/env bash

echo "Installing Ruby"
apt-get update -y && apt-get install -y ruby-full build-essential zlib1g-dev

export GEM_HOME=".gems"
export PATH=".gems/bin:$PATH"

echo "Installing Jekyll"

cd docs || (echo "Could not cd in docs" && exit 1)
gem install jekyll bundler

# Running Jekyll
jekyll serve
