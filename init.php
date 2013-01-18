<?php
error_reporting(E_ALL ^ E_STRICT ^ E_NOTICE);

require(__DIR__ . '/webdata/pixcore/Pix/Loader.php');
set_include_path(__DIR__ . '/webdata/pixcore/'
    . PATH_SEPARATOR . __DIR__ . '/webdata/models'
    . PATH_SEPARATOR . __DIR__ . '/webdata/Dropbox-master'
);

Pix_Loader::registerAutoload();

if (file_exists(__DIR__ . '/webdata/setting.php')) {
    include(__DIR__ . '/webdata/setting.php');
}

date_default_timezone_set('Asia/Taipei');

if (!getenv('DATABASE_URL')) {
    die('need DATABASE_URL');
}

if (preg_match('#postgres://([^:]*):([^@]*)@([^/]*)/(.*)#', strval(getenv('DATABASE_URL')), $matches)) {
    $user = $matches[1];
    $pass = $matches[2];
    $host = $matches[3];
    $dbname = $matches[4];
    Pix_Table::setDefaultDb(new Pix_Table_Db_Adapter_PgSQL(array('user' => $user, 'password' => $pass, 'host' => $host, 'dbname' => $dbname)));
} else if (preg_match('#mysql://([^:]*):([^@]*)@([^/]*)/(.*)#', strval(getenv('DATABASE_URL')), $matches)) {
    $db = new StdClass;
    $db->host = $matches[3];
    $db->username = $matches[1];
    $db->password = $matches[2];
    $db->dbname = $matches[4];
    $config = new StdClass;
    $config->master = $config->slave = $db;
    Pix_Table::setDefaultDb(new Pix_Table_Db_Adapter_MysqlConf(array($config)));
}

Pix_Controller::addCommonHelpers();

