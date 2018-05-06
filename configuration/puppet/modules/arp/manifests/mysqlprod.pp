class arp::mysqlprod {
  include '::mysql::server'

  ::mysql::db { 'casacuhuse':
    user     => 'casacuhuse',
    password => '9Ue61hv7$9eh',
    host     => 'localhost',
  }
}
