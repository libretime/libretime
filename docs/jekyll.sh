# Installing Jekyll

echo "Installing Ruby"
sudo apt-get install ruby-full build-essential zlib1g-dev

echo '# Install Ruby Gems to ~/gems' >> ~/.bashrc
echo 'export GEM_HOME="$HOME/gems"' >> ~/.bashrc
echo 'export PATH="$HOME/gems/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

echo "Installing Jekyll"
cd docs
gem install jekyll bundler

# Running Jekyll
bundle exec jekyll serve --watch --port 8888

echo "Visit http://localhost:8888 to see the LibreTime website."