Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.hostname = "dev-houseofcases.co.uk"
  config.vm.network "private_network", ip: "192.168.33.10"

  config.vm.provider :virtualbox do |vb|
    vb.name = "dev-houseofcases.co.uk"
  end

  config.vm.synced_folder('.', '/vagrant', disabled: true)
  config.vm.synced_folder("magento/", "/var/www/magento", owner: "www-data", group: "www-data")

  config.puppet_install.puppet_version = :latest
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = 'puppet/manifests'
    puppet.module_path = 'puppet/modules'
    puppet.manifest_file = 'init.pp'
  end

  config.vm.provision "shell", name: "Magento Setup", keep_color: true, inline: "
    cd /var/www/magento
    composer install
    php bin/magento setup:install --db-host=localhost --db-name=hoc_dev --db-user=hoc_dev --db-password='n2@79Bn628T7' --admin-firstname=Admin --admin-lastname=User --admin-email=pxp420mail@gmail.com --admin-user=admin --admin-password='house0fcases' --backend-frontname=admin_hoc
    php bin/magento setup:store-config:set --base-url=http://dev-houseofcases.co.uk/
    php bin/magento module:enable --all
    php bin/magento cache:enable
    php bin/magento cache:disable full_page
    php bin/magento cache:flush
    php bin/magento cache:clean
    php bin/magento setup:upgrade
    php bin/magento deploy:mode:set developer
  "
end
