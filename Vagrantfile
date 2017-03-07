# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/trusty64"

  config.vm.network "forwarded_port", guest:   80, host:8080
  config.vm.network "forwarded_port", guest: 8888, host:8888

  config.vm.provider "virtualbox" do |v|
    # to run without OOMing we need at least 1GB of RAM
    v.memory = 1024
  end

  config.vm.provision "shell", inline: "cd /vagrant; ./install -fIap"
  config.vm.provision "shell", path: "docs/scripts/install.sh"
  config.vm.provision "shell", path: "docs/scripts/serve.sh"

end
