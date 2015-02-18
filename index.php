<?php

/* Require Slim and plugins */
require 'Slim/Slim.php';
require 'plugins/NotORM.php';

/* Register autoloader and instantiate Slim */
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

/* Database Configuration */
$dbhost   = 'localhost';
$dbuser   = 'root';
$dbpass   = '';
$dbname   = 'garage';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
$pdo = new PDO($dsn, $dbuser, $dbpass);
$db = new NotORM($pdo);

/* Routes */
$app->get('/', function(){
    echo 'Home - My Slim Application';
});

/* Run the application */
$app->run();