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

// Home route
$app->get('/', function(){
    echo 'Home - My Slim Application';
});

// Get all cars
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

// Get a single car
$app->get('/cars/:id', function($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $car = $db->cars()->where('id', $id);
    if($data = $car->fetch()){
        echo json_encode(array(
            'id' => $data['id'],
            'year' => $data['year'],
            'make' => $data['make'],
            'model' => $data['model']
        ));
    }
    else{
        echo json_encode(array(
            'status' => false,
            'message' => "Car ID $id does not exist"
        ));
    }
});

// Add a new car
$app->post('/car', function() use($app, $db){
    $app->response()->header("Content-Type", "application/json");
    $car = $app->request()->post();
    $result = $db->cars->insert($car);
    echo json_encode(array('id' => $result['id']));
});

// Update a car
$app->put('/car/:id', function($id) use($app, $db){
    $app->response()->header("Content-Type", "application/json");
    $car = $db->cars()->where("id", $id);
    if ($car->fetch()) {
        $post = $app->request()->put();
        $result = $car->update($post);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Car updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Car id $id does not exist"
        ));
    }
});

// Remove a car
$app->delete('/car/:id', function($id) use($app, $db){
    $app->response()->header("Content-Type", "application/json");
    $car = $db->cars()->where('id', $id);
    if($car->fetch()){
        $result = $car->delete();
        echo json_encode(array(
            "status" => true,
            "message" => "Car deleted successfully"
        ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Car id $id does not exist"
        ));
    }
});

/* Run the application */
$app->run();