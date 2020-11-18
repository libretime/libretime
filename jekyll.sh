#/bin/bash

echo "Installing Ruby"
apt-get update -y && apt-get install -y ruby-full build-essential zlib1g-dev

export GEM_HOME=".gems"
export PATH=".gems/bin:$PATH"

echo "Installing Jekyll"

cd docs
gem install jekyll bundler

# Running Jekyll
jekyll serve
