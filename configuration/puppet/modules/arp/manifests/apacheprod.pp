class arp::apacheprod {
  class { '::apache':
    mpm_module => 'prefork',
  }

  apache::vhost { 'houseofcases.co.uk':
    port          => '80',
    docroot       => '/var/www/house-of-cases/magento',
    docroot_owner => 'www-data',
    docroot_group => 'www-data',
    override      => ['All'],
  }

  include '::apache::mod::rewrite'
  include '::apache::mod::php'
}
