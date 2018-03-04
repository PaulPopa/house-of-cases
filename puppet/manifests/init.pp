exec { 'apt-get update':
  path => '/usr/bin',
}

file { '/var/www/test/':
  ensure => directory,
}

