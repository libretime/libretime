# -*- mode: ruby -*-
# vi: set ft=ruby :

#
# Tweak guest CPUS and MEMORY using:
# export VAGRANT_NO_PORT_FORWARDING=true
# export VAGRANT_CPUS=4
# export VAGRANT_MEMORY=4096
# vagrant up buster
#

Vagrant.configure('2') do |config|
  if ! ENV.fetch('VAGRANT_NO_PORT_FORWARDING', 'false') == 'true'
    config.vm.network 'forwarded_port', guest: 8080, host: 8080 # web
    config.vm.network 'forwarded_port', guest: 8081, host: 8081 # api
    config.vm.network 'forwarded_port', guest: 8000, host: 8000 # icecast2
    config.vm.network 'forwarded_port', guest: 8001, host: 8001 # liquidsoap
    config.vm.network 'forwarded_port', guest: 8002, host: 8002 # liquidsoap
    config.vm.network 'forwarded_port', guest: 5432, host: 5432 # database
    config.vm.network 'forwarded_port', guest: 15672, host: 15672 # rabbitmq interface
  end

  config.vm.provider 'virtualbox' do |v|
    v.cpus = ENV.fetch('VAGRANT_CPUS', 2)
    v.memory = ENV.fetch('VAGRANT_MEMORY', 1024)

    # Enable audio drivers on VM settings
    # See https://github.com/geoffreyplitt/vagrant-audio
    if RUBY_PLATFORM =~ /darwin/
      v.customize [
                    'modifyvm', :id,
                    '--audio', 'coreaudio',
                    '--audiocontroller', 'hda' # choices: hda sb16 ac97
                  ]
    elsif RUBY_PLATFORM =~ /mingw|mswin|bccwin|cygwin|emx/
      v.customize [
                    'modifyvm', :id,
                    '--audio', 'dsound',
                    '--audiocontroller', 'ac97'
                  ]
    end
  end

  config.vm.provider 'libvirt' do |v|
    v.cpus = ENV.fetch('VAGRANT_CPUS', 2)
    v.memory = ENV.fetch('VAGRANT_MEMORY', 1024)
  end

  # NFS
  def setup_nfs(config, nfs_version = nil)
    config.vm.network 'private_network', ip: '192.168.10.100'
    nfs_path = '.'

    # NFS macOS support
    if Dir.exist?('/System/Volumes/Data')
      nfs_path = '/System/Volumes/Data' + Dir.pwd
    end

    # See https://www.vagrantup.com/docs/synced-folders/nfs#nfs-synced-folder-options
    case
    when nfs_version.nil? then config.vm.synced_folder nfs_path, '/vagrant', type: 'nfs'
    when nfs_version == 4 then config.vm.synced_folder nfs_path, '/vagrant', type: 'nfs',
                                                       nfs_version: 4,
                                                       nfs_udp: false
    else
      raise 'Invalid nfs_version provided'
    end
  end

  def setup_libretime(config, prepare_script, install_args = '')
    config.vm.provision 'prepare',
                        type: 'shell',
                        path: 'installer/vagrant/%s' % prepare_script

    $script = <<-SCRIPT
    set -e
    cd /vagrant

    LIBRETIME_POSTGRESQL_PASSWORD=libretime \
    LIBRETIME_RABBITMQ_PASSWORD=libretime \
    bash install \
      --listen-port 8080 \
      --in-place \
      http://192.168.10.100:8080

    installer/vagrant/post-install.sh

    libretime-api migrate

    systemctl restart libretime.target
    SCRIPT

    config.vm.provision 'install', type: 'shell', inline: $script
  end

  # Define all the OS boxes we support
  config.vm.define "focal" do |os|
    os.vm.box = "bento/ubuntu-20.04"
    setup_libretime(os, "debian.sh")
  end

  config.vm.define 'bionic' do |os|
    os.vm.box = 'bento/ubuntu-18.04'
    setup_nfs(config)
    setup_libretime(os, 'debian.sh')
  end

  config.vm.define 'bullseye' do |os|
    os.vm.box = 'debian/bullseye64'
    config.vm.provider 'virtualbox' do |v, override|
      override.vm.box = 'bento/debian-11'
    end
    setup_nfs(config, 4)
    setup_libretime(os, 'debian.sh')
  end

  config.vm.define 'buster' do |os|
    os.vm.box = 'debian/buster64'
    config.vm.provider 'virtualbox' do |v, override|
      override.vm.box = 'bento/debian-10'
    end
    setup_nfs(config)
    setup_libretime(os, 'debian.sh')
  end
end
