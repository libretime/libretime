# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  # libretime web interface
  config.vm.network "forwarded_port", guest: 8080, host:8080
  # icecast2
  config.vm.network "forwarded_port", guest: 8000, host:8000
  # liquidsoap input harbors for instreaming (ie. /master)
  config.vm.network "forwarded_port", guest: 8001, host:8001
  config.vm.network "forwarded_port", guest: 8002, host:8002
  # database
  config.vm.network "forwarded_port", guest: 5432, host:5432
  # api
  config.vm.network "forwarded_port", guest: 8081, host:8081

  # make sure we are using nfs (doesn't work out of the box with debian)
  nfsPath = "."
  # macOS Catalina support
  if Dir.exist?("/System/Volumes/Data")
      nfsPath = "/System/Volumes/Data" + Dir.pwd
  end
  config.vm.synced_folder nfsPath, "/vagrant", type: "nfs"
  # private network for nfs
  config.vm.network "private_network", ip: "192.168.10.100"

  config.vm.provider "virtualbox" do |v|
    # to run without OOMing we need at least 1GB of RAM
    v.memory = 1024

    # enable audio drivers on VM settings
    # pinched from https://github.com/GeoffreyPlitt/vagrant-audio
    config.vm.provider :virtualbox do |vb|
      if RUBY_PLATFORM =~ /darwin/
        vb.customize ["modifyvm", :id, '--audio', 'coreaudio', '--audiocontroller', 'hda'] # choices: hda sb16 ac97
      elsif RUBY_PLATFORM =~ /mingw|mswin|bccwin|cygwin|emx/
        vb.customize ["modifyvm", :id, '--audio', 'dsound', '--audiocontroller', 'ac97']
      end
    end
  end

  # default installer args used for all distros
  installer_args="--force --in-place --verbose --postgres --apache --icecast "

  # define all the OS boxes we support
  config.vm.define "ubuntu-bionic" do |os|
    os.vm.box = "bento/ubuntu-18.04"
    provision_libretime(os, "debian.sh", installer_args)
  end
  config.vm.define "ubuntu-xenial" do |os|
    os.vm.box = "bento/ubuntu-16.04"
    provision_libretime(os, "debian.sh", installer_args)
  end
  config.vm.define "debian-stretch" do |os|
    os.vm.box = "bento/debian-9"
    provision_libretime(os, "debian.sh", installer_args)
  end
  config.vm.define "debian-buster" do |os|
    os.vm.box = "bento/debian-10"
    provision_libretime(os, "debian.sh", installer_args)
  end
  config.vm.define "centos" do |os|
    os.vm.box = 'centos/8'
    provision_libretime(os, "centos.sh", installer_args + "--selinux")
  end

  def provision_libretime(config, prepare_script, installer_args)
    # Prepare OS
    config.vm.provision "prepare", type: "shell", path: "installer/vagrant/%s" % prepare_script

    # Provision LibreTime
    config.vm.provision "install", type: "shell", inline: "cd /vagrant; ./install %s --web-port=8080" % installer_args
  end

end
