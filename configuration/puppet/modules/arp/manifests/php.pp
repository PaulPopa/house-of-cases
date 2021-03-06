class arp::php {
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
}
