#!/bin/sh

init()
{
    git remote add -f pixcore git@github.com:/pixnet/pixcore-php 2>&1 
    git fetch pixcore
    mkdir -p webdata/pixcore
    mkdir -p webdata/controllers
    mkdir -p webdata/views/common
    mkdir -p webdata/views/index
    mkdir -p webdata/views/foo
    mkdir -p webdata/models
    git archive --format=tar pixcore/master | tar -xf - -C webdata/pixcore
    cat > .gitignore << EOF
.*.swp
EOF

    cat > .htaccess << EOF
#
Options -Indexes
RewriteEngine On
RewriteBase /

RewriteRule static/ - [L]
RewriteRule .* index.php [L]

EOF
    RANDOM=`head /dev/random | md5`;
    cat > init.php << EOF
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
    if (preg_match('#postgres://([^:]*):([^@]*)@([^/]*)/(.*)#', strval(getenv('DATABASE_URL')), \$matches)) {
	\$user = \$matches[1];
	\$pass = \$matches[2];
	\$host = \$matches[3];
	\$dbname = \$matches[4];
	Pix_Table::setDefaultDb(new Pix_Table_Db_Adapter_PgSQL(array('user' => \$user, 'password' => \$pass, 'host' => \$host, 'dbname' => \$dbname)));
    }
}

Pix_Table::setCache(new Pix_Cache());
Pix_Table::addResultSetStaticPlugins('Pix_Table_ResultSet_Plugin_Volumnmode');
Pix_Controller::addCommonPlugins();

EOF

    cat > index.php << EOF
<?php
include(__DIR__ . '/init.php');

Pix_Session::setCore('cookie', array('secret' => '${RANDOM}'));
Pix_Controller::dispatch(__DIR__ . '/webdata');
EOF

    cat > webdata/controllers/IndexController.php << EOF
<?php

class IndexController extends Pix_Controller
{
    public function indexAction()
    {
    }
}
EOF

    cat > webdata/controllers/FooController.php << EOF
<?php

class FooController extends Pix_Controller
{
    public function barAction()
    {
    }
}
EOF

    cat > webdata/views/common/header.phtml << EOF
<html>
<head>
<title><?= \$this->escape(\$this->title) ?></title>
</head>
<body>
I am Header
<hr>
EOF

    cat > webdata/views/common/footer.phtml << EOF
<hr>
I am Footer
</body>
</html>
EOF

    cat > webdata/views/index/index.phtml << EOF
<?= \$this->partial('/common/header.phtml', \$this) ?>
I am index... <a href="/foo/bar">click me</a> to /foo/bar
<?= \$this->partial('/common/footer.phtml', \$this) ?>
EOF

    cat > webdata/views/foo/bar.phtml << EOF
<?= \$this->partial('/common/header.phtml', \$this) ?>
I am foo/bar
<?= \$this->partial('/common/footer.phtml', \$this) ?>
EOF

    git add .
    git commit -m 'init'
    git push
}

run_heroku()
{
    export LD_LIBRARY_PATH=/app/php/ext
    export PHP_INI_SCAN_DIR=/app/www
    /app/php/bin/php -r 'include("/app/www/init.php"); Pix_Prompt::init();'
}

run()
{
    heroku run sh /app/www/pixhero.sh run_heroku
}

show_usage()
{
    export LD_LIBRARY_PATH=/app/php/ext
    export PHP_INI_SCAN_DIR=/app/www
    /app/php/bin/php -r 'include("/app/www/init.php"); print_r(Pix_Table::getLink("master")->query("SELECT pg_size_pretty(pg_database_size("postgres"))")->fetch_assoc());';
}

case "$1" in
    init)
	init
	;;
    run_heroku)
	run_heroku;
	;;
    run)
	run;
	;;
    show_usage)
	show_usage;
	;;
esac
