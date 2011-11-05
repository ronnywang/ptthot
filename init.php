<?php
require(__DIR__ . '/webdata/pixcore/Pix/Loader.php');
set_include_path(__DIR__ . '/webdata/pixcore/'
    . PATH_SEPARATOR . __DIR__ . '/webdata/models'
);

Pix_Loader::registerAutoload();

/*
Pix_Cache::addServer('Pix_Cache_Core_Memcache', array(
    'servers' => array(
	array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 10),
    ),  
));
*/
if (getenv('DATABASE_URL')) {
    if (preg_match('#postgres://([^:]*):([^@]*)@([^/]*)/(.*)#', strval(getenv('DATABASE_URL')), $matches)) {
	$user = $matches[1];
	$pass = $matches[2];
	$host = $matches[3];
	$dbname = $matches[4];
	Pix_Table::setDefaultDb(new Pix_Table_Db_Adapter_PgSQL(array('user' => $user, 'password' => $pass, 'host' => $host, 'dbname' => $dbname)));
    }
}

Pix_Table::setCache(new Pix_Cache());
Pix_Table::addResultSetStaticPlugins('Pix_Table_ResultSet_Plugin_Volumnmode');
Pix_Controller::addCommonPlugins();

