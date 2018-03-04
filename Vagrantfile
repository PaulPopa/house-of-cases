Vagrant.configure("2") do |config|
  config.vm.box = "puppetlabs/ubuntu-16.04-64-puppet"
  config.vm.box_version = "1.0.0"
  config.vm.network "private_network", ip: "192.168.33.10"

  config.vm.synced_folder('.', '/vagrant', disabled: true)
  config.vm.synced_folder("magento/", "/var/www/magento")

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = 'puppet/manifests'
    puppet.module_path = 'puppet/modules'
    puppet.manifest_file = 'init.pp'
  end
end
