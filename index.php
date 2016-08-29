<?php

//Start session
if(!isset($_SESSION)){
	session_start();
}

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);
ini_set('bower_path', 'public/bower_components/');

//Define a constante with de path enviroment 
define('SYS_ROOT', $_SERVER['DOCUMENT_ROOT']);
//Define a constante with de path enviroment 
define('APP_VIEWS', SYS_ROOT.'/app/views/');
//Define a constante with de path enviroment 
define('SYS_CONFIG', SYS_ROOT.'/config/');

//INclude the autoload configuration
require SYS_ROOT."config/routes_autoload.php";
//Functions to View
require SYS_ROOT."libs/Helpers/HTMLFunctions.php";

$without_loggin =  ['checklogin'];

ManagerSessionController::getController();

//Inform template
if(isset($_SESSION['user_id'])){
	View::template(APP_VIEWS.'/template_dashboard.html');
} else {
	View::template(APP_VIEWS.'/template_login.html');
}

//Display page
View::display();

