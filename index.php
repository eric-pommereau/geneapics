<?php 
    $conf = json_decode(file_get_contents('./conf/conf.json')); 
    // echo "<pre>" ;var_dump($conf);exit;
?>
<!doctype html>
<html ng-app="app">
    <head>
        <title>My Angular App</title>
        <script src="<?php echo $conf->js->angular->js;?>"></script>
        <script src="<?php echo $conf->js->angular->route;?>"></script>
        <script src="./resources/js/app.js"></script>
    </head>
    <body>
        <h1>liste des images</h1>
        <div ng-view></div>
    </body>
</html>
