# Installing Jekyll
# This script does not work and exists as a skeleton for
# someone to use to get Jekyll building outside of GitHub

echo "Installing Ruby"
apt-get update -y && apt-get install -y ruby-full build-essential zlib1g-dev

export GEM_HOME=".gems"
export PATH=".gems/bin:$PATH"

echo "Installing Jekyll"

# Picking different directory if installing with Vagrant
localUser=$(who am i | awk '{print $1}')
if [ $localUser == vagrant ]
then 
	cd /vagrant/docs
else
	cd docs
fi

gem install jekyll bundler

# Running Jekyll
jekyll serve --port 8888
