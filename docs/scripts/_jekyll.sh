# Installing Jekyll
# This script does not work and exists as a skeleton for
# someone to use to get Jekyll building outside of GitHub

echo "Installing Ruby"
sudo apt-get install ruby-full build-essential zlib1g-dev

export GEM_HOME=".gems"
export PATH=".gems/bin:$PATH"

echo "Installing Jekyll"
cd docs
gem install jekyll bundler

# Running Jekyll
bundle exec jekyll serve --watch --port 8888

echo "Visit http://localhost:8888 to see the LibreTime website."
