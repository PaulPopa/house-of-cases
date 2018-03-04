exec { "apt-get update":
    command => "/usr/bin/apt-get update",
}


## Server configuration
class { '::apache':
  mpm_module => false,
}

apache::vhost { 'houseofcases.co.uk':
  port          => '80',
  docroot       => '/var/www/magento',
  docroot_owner => 'www-data',
  docroot_group => 'www-data',
}

include '::apache::mod::prefork'
include '::apache::mod::php'
include '::mysql::server'

include '::composer'
