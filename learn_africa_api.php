<?php

    header("Access-Control-Allow-Origin:*");
    header("Access-Control-Allow-Methods: GET,POST,PUT,PATCH,OPTIONS");
    header("Access-Control-Allow-Headers:*");
    header("Access-Control-Allow-Headers: Authorization,Origin, X-Requested-With, Content-Type,Accept");
    header("Content-Type: application/json");

 ///  $headers = getallheaders();
    //echo $headers['Authorization'];

    require_once('Api.php');
    
  
     $api = new Api;
    $api->processApi();


?>