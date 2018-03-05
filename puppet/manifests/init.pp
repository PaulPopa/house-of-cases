exec { "apt-get-update":
  command => "/usr/bin/apt-get update",
}

## Server configuration
class { '::apache':
  mpm_module => false,
}

apache::vhost { 'dev-houseofcases.co.uk':
  port          => '80',
  docroot       => '/var/www/magento',
  docroot_owner => 'www-data',
  docroot_group => 'www-data',
  override      => ['All'],
}

include '::apache::mod::prefork'
include '::apache::mod::rewrite'
include '::apache::mod::php'

class { '::php':
  extensions => {
    curl     => { },
    gd       => { },
    intl     => { },
    mbstring => { },
    mcrypt   => { },
    mysql    => {
      so_name => 'pdo_mysql',
    },
    soap     => { },
    zip      => { },
  },
}

exec { "enable-pdo-mysql":
  path => [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/" ],
  command => "phpenmod pdo_mysql",
  notify => Service["apache2"],
  require => Class["::php"],
}

include '::mysql::server'

::mysql::db { 'hoc_dev':
  user     => 'hoc_dev',
  password => 'n2@79Bn628T7',
  host     => 'localhost',
}

