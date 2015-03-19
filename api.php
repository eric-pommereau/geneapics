<?php
require_once ('libs/RedBean/rb.php');
require_once("libs/vendor/autoload.php");
$conf = json_decode(file_get_contents('./conf/conf.json'));

\Slim\Slim::registerAutoloader();

$version = '0.1';



R::setup('mysql:host=localhost;dbname=geneapics', 'root', 'root');
R::freeze(true);
R::debug(FALSE);

$app = new \Slim\Slim(array('debug' => true));

$app->get('/images', function () use ($app) {
    $images = R::find('image');
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode(R::exportAll($images));
});

$app->get('/image/:id', function ($id) use ($app) {
    $rowImage = R::findOne('IMAGE', 'ID = ?', array($id));
    $app->response()->header('Content-Type', 'application/json');
    echo json_encode(R::exportAll($rowImage));
});

$app->post('/image', function () use ($app) {
    var_dump($app->request->params());  
    
    $image = R::dispense( 'image' );
    
    $image->NAME_IMG = $app->request->params(name_img);
    $image->STORAGE_IMG = $app->request->params(storage_img);
    $image->DT_START_IMG = $app->request->params(dt_img); 
    
    $id = R::store($image, "ID_IMG");
    
    $rowImage = R::findOne('image', 'ID = ?', array($id));
    
    $app->response()->header('Content-Type', 'application/json');
    
    // Message + success
    echo json_encode(R::exportAll($rowImage));
});

$app->run();