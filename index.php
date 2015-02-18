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

$app->get('/cars', function() use($app, $db){
    $cars = array();
    foreach ($db->cars() as $car) {
        $cars[]  = array(
            'id' => $car['id'],
            'year' => $car['year'],
            'make' => $car['make'],
            'model' => $car['model']
        );
    }
    $app->response()->header("Content-Type", "application/json");
    echo json_encode($cars, JSON_FORCE_OBJECT);
});

/* Run the application */
$app->run();