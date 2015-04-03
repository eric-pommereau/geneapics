<?php
require_once ('libs/RedBean/rb.php');
require_once("libs/vendor/autoload.php");

$conf = json_decode(file_get_contents('./conf/conf.json'));

\Slim\Slim::registerAutoloader();

$connectionString = sprintf('%s:host=%s;dbname=%s', $conf->db->driver, $conf->db->host, $conf->db->dbname);

R::setup(
    $connectionString, 
    $conf->db->login, 
    $conf->db->pwd
);

R::freeze(TRUE);

R::debug(FALSE);

$app = new \Slim\Slim(array('debug' => true));

try {
    
    $app->post('/uploads', function () use ($app) {
        var_dump($_FILES);
    });
    
    $app->get('/images', function () use ($app) {
        $images = R::findAll('image', "ORDER BY ID DESC LIMIT 100");
        
        $app->response()->header('Content-Type', 'application/json');
        // echo "<pre>";var_dump(R::exportAll($images));exit;
        echo json_encode(R::exportAll($images));
    });
    
    $app->get('/image/:id', function ($id) use ($app) {
        $rowImage = R::findOne('IMAGE', 'ID = ?', array($id));
        $app->response()->header('Content-Type', 'application/json');
        
        
        echo json_encode(R::exportAll($rowImage));
    });
    
    
    // Nouvelle image
    $app->post('/image', function () use ($app) {
        
        // var_dump($app->request->params(), $app->request->getContentLength(),$app->request->isFormData());
        var_dump($app->request->getContentLength());
        exit;
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
    
    // Update image
    // Nouvelle image
    $app->post('/image', function () use ($app) {
        
        echo json_encode($_FILES);exit;
                
        $request = $app->request();
        $body = $request->getBody();
        $input = json_decode($body);
        
        $rowImage = R::findOne('IMAGE', 'ID = ?', array($input->ID));
        
        if(! $rowImage instanceof RedBeanPHP\OODBBean) {
            throw new Exception("L'image n'a pas pu être récupérée", 1);
        }
        
        $app->response()->header('Content-Type', 'application/json');
        
        $rowImage->NAME_IMG = $input->NAME_IMG;
        $rowImage->MD5_IMG = $input->MD5_IMG;
        $rowImage->STORAGE_IMG = $input->STORAGE_IMG;
        $rowImage->DT_START_IMG = $input->DT_START_IMG; 
        $rowImage->DT_END_IMG = $input->DT_END_IMG; 
        
        //[{"id":"7","NAME_IMG":"image","MD5_IMG":"","STORAGE_IMG":"truc","PLACE_ID_PLA":"0","DT_START_IMG":"2015-03-16","DT_END_IMG":"0000-00-00"}]
        
        R::store($rowImage);
        
        $rowImage = R::findOne('image', 'ID = ?', array($input->ID));
        
        $app->response()->header('Content-Type', 'application/json');
        echo json_encode(R::exportAll($rowImage));
    });    
    
    $app->run();
    
} catch(Exception $ex) {
    echo json_encode(array('result' => false, 'message' => $ex->getMessage()));
}
    
    