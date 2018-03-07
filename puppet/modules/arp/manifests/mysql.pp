class arp::mysql {
  include '::mysql::server'

  ::mysql::db { 'hoc_dev':
    user     => 'hoc_dev',
    password => 'n2@79Bn628T7',
    host     => 'localhost',
  }  
}
