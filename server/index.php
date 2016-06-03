<?php
/**
 * File: index.php
 * User: mofasa
 * Date: 26/02/15 14:20
 */

require "vendor/autoload.php";
use Ipedis\Application;
//force setting
date_default_timezone_set('europe/paris');

//Let's do that !
try{
    Application::getInstance()
        ->run();
    return true;
}catch (\Ipedis\RouterException $e){
    Application::getInstance()
        ->factoryError($e); //Shit, some error, let's catch that and return what's happen.

}
//var_dump($_FILES["resume"]["tmp_name"]);
//var_dump($_FILES);exit;
//exit;
//echo json_encode($_POST['json']);
// process();
//function process(){
////die("gothere2");
//    //echo json_encode($_POST['json']);
//
//    $retour= array(
//        "code" => 201,
//        "message" => "test good",
//        "status" => "error"
//    );
//   echo json_encode($retour);
//
//}
//
//?>