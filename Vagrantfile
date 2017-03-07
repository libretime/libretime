# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/trusty64"

  # libretime web interface
  config.vm.network "forwarded_port", guest: 9080, host:9080
  # icecast2
  config.vm.network "forwarded_port", guest: 8000, host:8000
  # liquidsoap input harbors for instreaming (ie. /master)
  config.vm.network "forwarded_port", guest: 8001, host:8001
  # mkdics documentation
  config.vm.network "forwarded_port", guest: 8888, host:8888

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

  # ubuntu/trusty64 alsa setup
  # slightly modernized from  https://github.com/naomiaro/vagrant-alsa-audio
  # https://wiki.ubuntu.com/Audio/UpgradingAlsa/DKMS
  config.vm.provision "shell", inline: <<-SHELL
     alsa_deb="oem-audio-hda-daily-dkms_0.201703070301~ubuntu14.04.1_all.deb"
     wget -nv https://code.launchpad.net/~ubuntu-audio-dev/+archive/ubuntu/alsa-daily/+files/${alsa_deb}
     sudo dpkg -i ${alsa_deb}
     rm ${alsa_deb}
     sudo DEBIAN_FRONTEND=noninteractive apt-get -y -m --force-yes install  alsa
     sudo usermod -a -G audio vagrant
  SHELL
  config.vm.provision "shell", inline: "cd /vagrant; ./install -fIapv --web-port=9080"
  config.vm.provision "shell", path: "docs/scripts/install.sh"
  config.vm.provision "shell", path: "docs/scripts/serve.sh"

end
